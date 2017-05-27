<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Zizaco\Entrust\Traits\EntrustUserTrait;

use Qywx;
use App\Department;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Eloquent model method
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,
             ]
        ];
    }

    public static function findByUsername($username)
    {
        return self::where(['username' => $username])->firstOrFail();
    }

    public static function sync($update = true)
    {
        $departments = Department::select('id', 'name')->get()->keyBy('id')->toArray();
        $members = array_column(
            Qywx::getDepartmentMembers(config('qywx')['rootid'], true, true),
            null,
            'userid'
        );

        $count = 0;
        foreach ($members as $member) {
            $isNew = false;
            if (! $user = self::where(['username' => $member['userid']])->first()) {
                $user = new self;
                $isNew = true;
            } elseif ($update) {
                continue; // 如果是增量更新就不修改原来的记录了
            }

            if ($isNew) {
                $user->username = $member['userid'];
                $user->name = $member['name'];
                $user->password = bcrypt(str_random(8));
                $user->email = isset($member['email']) ? ($member['email'] ? $member['email'] : null) : null;
                $user->mobile = $member['mobile'] ?? '';
                $user->departments = json_encode(array_map(function ($departmentId) use ($departments) {
                    return [$departmentId => $departments[$departmentId]];
                }, $member['department']));
                $user->info = json_encode($user);

                $user->save() and ++$count;

                // 是新用户就分配默认角色
                $role = Role::where(['name' => 'user'])->firstOrFail();
                $user->attachRole($role);
            } else {
                $user->name = $member['name'];
                $user->email = isset($member['email']) ? ($member['email'] ? $member['email'] : null) : null;
                $user->mobile = $member['mobile'] ?? '';
                $user->departments = json_encode(array_map(function ($departmentId) use ($departments) {
                    return [$departmentId => $departments[$departmentId]];
                }, $member['department']));
                $user->info = json_encode($user);

                $user->save() and ++$count;
            }
        }

        return $count;
    }

    public static function sendWxMsg($username, $title, $message, $url = '')
    {
        $articles = [
            Qywx::buildNewsItem($title, $message, $url, ''),
        ];

        if (!is_array($username)) {
            $username = [$username];
        }

        $username = array_filter($username, function ($name) {
            return !in_array($name, ['suadmin', 'admin', 'demo']);
        });

        return Qywx::sendNewsMsg(
            $articles,
            ['touser' => $username],
            config('qywx.appid')
        );
    }
}

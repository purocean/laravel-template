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

    public static function sync()
    {
        $departments = Department::select('id', 'name')->get()->keyBy('id')->toArray();
        $members = array_column(
            Qywx::getDepartmentMembers(config('qywx')['rootid'], true, true),
            null,
            'userid'
        );

        $count = 0;
        foreach ($members as $member) {
            if (!$user = self::where(['username' => $member['userid']])->first()) {
                $user = new self;
            }

            $user->username = $member['userid'];
            $user->name = $member['name'];
            $user->password = bcrypt(str_random(8));
            $user->email = $member['email'] ?? null;
            $user->mobile = $member['mobile'] ?? '';
            $user->departments = json_encode(array_map(function ($departmentId) use ($departments) {
                return [$departmentId => $departments[$departmentId]];
            }, $member['department']));
            $user->info = json_encode($user);

            $user->save() and ++$count;
        }

        return $count;
    }
}

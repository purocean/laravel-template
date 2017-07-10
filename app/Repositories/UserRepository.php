<?php

namespace App\Repositories;

use App\User;
use App\Role;
use Wxsdk\Qywx;

class UserRepository extends AbstractRepository
{
    protected function modelName()
    {
        return User::class;
    }

    public function search($search = null)
    {
        return User::where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->paginate(15);
    }

    public function getByUsername($username)
    {
        return User::where('username', $username)
                    ->first();
    }

    public function getMe()
    {
        return auth()->user();
    }

    public function sync()
    {
        $qywx = new Qywx(config('qywx.contacts'));

        $members = $qywx->getDepartmentMembers(
            config('qywx.contacts.rootid'),
            true,
        true);

        $count = 0;
        foreach ((array) $members as $member) {
            $isNew = false;
            if (! $user = $this->getByUsername($member['userid'])) {
                $user = new User;
                $isNew = true;
            }

            $user->name = $member['name'];
            $user->email = isset($member['email']) ? ($member['email'] ? $member['email'] : null) : null;
            $user->mobile = $member['mobile'] ?? '';
            $user->avatar = $member['avatar'] ?? '';
            $user->info = json_encode($user);

            if ($isNew) {
                $user->username = $member['userid'];
                $user->password = bcrypt(str_random(8));

                $user->save() and ++$count;

                // 是新用户就分配默认角色
                $role = Role::where('name', 'user')->firstOrFail();
                $user->attachRole($role);
            } else {
                $user->save() and ++$count;
            }

            // 同步组织架构
            $user->departments()->sync($member['department']);
        }

        return $count;
    }
}

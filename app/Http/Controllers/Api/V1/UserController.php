<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use App\Jobs\SyncUserFromQywx;

/**
 *  @Resource("Users", uri="/users")
 */
class UserController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->json('username');
        $password = $request->json('password');

        $credentials = ['password' => $password, 'username' => $username];

        try {
            if (!$access_token = JWTAuth::attempt($credentials)) {
                return $this->ajax('error', '账号或者密码有误');
            }

            if ($user = \Auth::user()) {
                return $this->ajax('ok', '登录成功', [
                    'user' => [
                        'name' => $user->name,
                        'username' => $user->username,
                    ],
                    'token' => $access_token,
                ]);
            } else {
                return $this->response->errorInternal('获取登录用户失败');
            }

        } catch (JWTException $e) {
             return $this->response->errorInternal('无法生成 Token');
        }
    }

    public function items()
    {
        if ($user = \Auth::user()) {
            $roles = [];
            $perms = [];

            foreach ($user->roles as $role) {
                $roles[$role->name] = $role->display_name;
                $perms = array_column($role->perms->toArray(), 'display_name', 'name');
            }

            return $this->ajax('ok', '获取成功', compact('roles', 'perms'));
        } else {
            return $this->errorInternal('获取用户失败');
        }
    }

    public function sync()
    {
        dispatch(new SyncUserFromQywx);

        return $this->ajax('ok', "已经开始同步用户，请稍后刷新页面查看同步结果");
    }

    public function list()
    {
        return $this->ajax('ok', '获取成功', User::paginate(15)->toArray());
    }
}

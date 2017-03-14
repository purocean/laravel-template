<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\SyncUserFromQywx;
use App\User;
use Qywx;
use Cache;
use Auth;

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
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->ajax('error', '账号或者密码有误');
            }

            if ($user = Auth::user()) {
                return $this->ajax('ok', '登录成功', [
                    'user' => [
                        'name' => $user->name,
                        'username' => $user->username,
                    ],
                    'token' => $token,
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

        return $this->ajax('ok', "已经开始同步，请稍后刷新页面查看同步结果");
    }

    public function list()
    {
        return $this->ajax('ok', '获取成功', User::paginate(15)->toArray());
    }

    public function qrcode()
    {
        $nonce = str_random(32);

        // 一分钟二维码失效
        if (! Cache::tags('qrlogin')->add($nonce, ['username' => null, 'login' => false], 1)) {
            return $this->ajax('error', '获取nonce失败');
        }

        return $this->ajax('ok', '获取成功', [
            'nonce' => $nonce,
            'url' => url('/app.html#/qrlogin?nonce=' . urlencode($nonce)),
            'expires' => 60,
        ]);
    }

    public function qrlogin(Request $request)
    {
        $nonce = $request->json('nonce');
        if (! $result = Cache::tags('qrlogin')->get($nonce)) {
            return $this->ajax('error', '二维码已失效，请刷新页面');
        }

        if (! $result['username']) {
            return $this->ajax('error', '请扫码');
        }

        if (! $result['login']) {
            return $this->ajax('error', '请确认登录');
        }

        if (! $loginResult = $this->_loginByUsername($result['username'])) {
             return $this->response->errorInternal('无法生成 Token');
        }

        return $this->ajax('ok', '登录成功', [
            'user' => [
                'name' => $loginResult['user']->name,
                'username' => $loginResult['user']->username,
            ],
            'token' => $loginResult['token'],
        ]);
    }

    public function codelogin(Request $request)
    {
        if (! $code = $request->json('code')) {
            return $this->ajax('error', '未提供code');
        }

        if (! $username = Qywx::getUserId($code)) {
            return $this->ajax('error', '不属于企业号，请联系管理员');
        }

        if (! $loginResult = $this->_loginByUsername($username)) {
             return $this->response->errorInternal('无法生成 Token');
        }

        return $this->ajax('ok', '登录成功', [
            'user' => [
                'name' => $loginResult['user']->name,
                'username' => $loginResult['user']->username,
            ],
            'token' => $loginResult['token'],
        ]);
    }

    private function _loginByUsername($username)
    {
        try {
            $user = User::where(['username' => $username])->firstOrFail();
            $token = JWTAuth::fromUser($user);

            return compact('user', 'token');
        } catch (JWTException $e) {
            return false;
        }
    }

    public function confirmqrlogin(Request $request)
    {
        if (! $nonce = $request->json('nonce')) {
            return $this->ajax('error', '未提供nonce');
        }

        if (! $result = Cache::tags('qrlogin')->get($nonce)) {
            return $this->ajax('error', '二维码已失效，请刷新页面');
        }

        if (! $user = Auth::user()) {
            return $this->ajax('error', '获取登录用户失败');
        }

        $result = ['username' => $user->username, 'login' => $request->json('login')];

        Cache::tags('qrlogin')->put($nonce, $result, 1);

        return $this->ajax('ok', '确认登录成功');
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\SyncUserFromQywx;
use App\User;
use Auth;
use Cache;
use Qywx;

/**
 * 认证授权
 *
 * @Resource("认证授权", uri="/api")
 */
class AuthController extends Controller
{
    /**
     * 获取用户的角色权限
     *
     * @Get("limits")
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         "roles": {
     *             "suadmin": "超级管理员"
     *         },
     *         "perms": {
     *             "/qrlogin/*": "/qrlogin/*"
     *         }
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
    public function limits()
    {
        if ($user = Auth::user()) {
            $roles = [];
            $perms = [];

            foreach ($user->roles as $role) {
                $roles[$role->name] = $role->display_name;
                $perms = array_merge(
                    $perms,
                    array_column($role->perms->toArray(), 'display_name', 'name')
                );
            }

            return $this->ajax('ok', '获取成功', compact('roles', 'perms'));
        } else {
            return $this->errorInternal('获取用户失败');
        }
    }

    /**
     * 用账号密码登录
     *
     * @Post("login")
     * @Request({"username": "demo", "password": "testpw"})
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTkyLjE2OC4xLjEwODo4MDc3L2FwaS9sb2dpbiIsImlhdCI6MTQ4OTU2MzEyOCwiZXhwIjoxNDg5NTY2NzI4LCJuYmYiOjE0ODk1NjMxMjgsImp0aSI6IlBwR3VyY2hqT2cyb3RhV3YiLCJzdWIiOjEsInVzZXIiOnsiaWQiOjF9fQ.8BdtuyTS9oOiEOIJNnwKvbLIDpJ2Rr8aWqp8FPYvl04",
     *         "user": {
     *             "name": "测试姓名",
     *             "username": "demo"
     *         }
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
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

    /**
     * 获取扫码登录信息
     *
     * @Get("qrcode")
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {"expires": "60", "nonce": "HVaCH2KVNQrgvY5AxegKIcMPknCf6Qcs", "url": "http://xxx.xxx/xx.x/"},
     *     "errors":null,
     *     "code":0
     * })
     */
    public function qrcode()
    {
        $nonce = str_random(32);

        // 一分钟二维码失效
        if (! Cache::tags('qrlogin')->add($nonce, ['username' => null, 'login' => false], 1)) {
            return $this->ajax('error', '获取nonce失败');
        }

        return $this->ajax('ok', '获取成功', [
            'nonce' => $nonce,
            'url' => url('/mobile.html#/qrlogin?nonce=' . urlencode($nonce)),
            'expires' => 60,
        ]);
    }

    /**
     * 用二维码 nonce 登录
     *
     * @Post("qrlogin")
     * @Request({"nonce": "HVaCH2KVNQrgvY5AxegKIcMPknCf6Qcs"})
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": null,
     *     "errors":{
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTkyLjE2OC4xLjEwODo4MDc3L2FwaS9sb2dpbiIsImlhdCI6MTQ4OTU2MzEyOCwiZXhwIjoxNDg5NTY2NzI4LCJuYmYiOjE0ODk1NjMxMjgsImp0aSI6IlBwR3VyY2hqT2cyb3RhV3YiLCJzdWIiOjEsInVzZXIiOnsiaWQiOjF9fQ.8BdtuyTS9oOiEOIJNnwKvbLIDpJ2Rr8aWqp8FPYvl04",
     *         "user": {
     *             "name": "测试姓名",
     *             "username": "demo"
     *         }
     *     },
     *     "code":0
     * })
     */
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

    /**
     * 用微信授权返回的 code 登录（移动端）
     *
     * @Post("codelogin")
     * @Request({"code": "fafdcfac7e502ed2d008c52bf46abc67"})
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         "total": 150,
     *         "per_page": 15,
     *         "current_page": 1,
     *         "last_page": 10,
     *         "next_page_url": "http:\/\/...",
     *         "prev_page_url": null,
     *         "from": 1,
     *         "to": 15,
     *         "data": {
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTkyLjE2OC4xLjEwODo4MDc3L2FwaS9sb2dpbiIsImlhdCI6MTQ4OTU2MzEyOCwiZXhwIjoxNDg5NTY2NzI4LCJuYmYiOjE0ODk1NjMxMjgsImp0aSI6IlBwR3VyY2hqT2cyb3RhV3YiLCJzdWIiOjEsInVzZXIiOnsiaWQiOjF9fQ.8BdtuyTS9oOiEOIJNnwKvbLIDpJ2Rr8aWqp8FPYvl04",
     *         "user": {
     *             "name": "测试姓名",
     *             "username": "demo"
     *         }
     *     }
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
    public function codelogin(Request $request)
    {
        if (! $code = $request->json('code')) {
            return $this->ajax('error', '未提供code');
        }

        if (! $username = Qywx::getUserId($code)) {
            return $this->ajax('error', '不属于企业号，请联系管理员，或稍后再试');
        }

        if (! $loginResult = $this->_loginByUsername($username)) {
            // 可能数据库数据数据，尝试同步
            dispatch(new SyncUserFromQywx(true));
            return $this->ajax('error', '或许您是新加入的成员，请耐心等待系统同步数据，十分钟后再来吧 :)');
        }

        return $this->ajax('ok', '登录成功', [
            'user' => [
                'name' => $loginResult['user']->name,
                'username' => $loginResult['user']->username,
            ],
            'token' => $loginResult['token'],
        ]);
    }

    /**
     * 确认扫码登录
     *
     * @Post("confirmqrlogin")
     * @Request({"nonce": "HVaCH2KVNQrgvY5AxegKIcMPknCf6Qcs", "login": true})
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTkyLjE2OC4xLjEwODo4MDc3L2FwaS9sb2dpbiIsImlhdCI6MTQ4OTU2MzEyOCwiZXhwIjoxNDg5NTY2NzI4LCJuYmYiOjE0ODk1NjMxMjgsImp0aSI6IlBwR3VyY2hqT2cyb3RhV3YiLCJzdWIiOjEsInVzZXIiOnsiaWQiOjF9fQ.8BdtuyTS9oOiEOIJNnwKvbLIDpJ2Rr8aWqp8FPYvl04",
     *         "user": {
     *             "name": "测试姓名",
     *             "username": "demo"
     *         }
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
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

    private function _loginByUsername($username)
    {
        try {
            $user = User::where(['username' => $username])->firstOrFail();
            $token = JWTAuth::fromUser($user);

            return compact('user', 'token');
        } catch (\Exception $e) {
            return false;
        }
    }
}

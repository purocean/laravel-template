<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 *  @Resource("Users", uri="/users")
 */
class UserController extends Controller
{
    /**
     * @Get("/")
     */
    public function index()
    {
        return '尼玛';
    }

    public function login(Request $request)
    {
        $username = $request->json('username');
        $password = $request->json('password');

        $credentials = ['password' => $password, 'username' => $username];

        try {
            if (!$access_token = JWTAuth::attempt($credentials)) {
                return $this->ajax('error', '账号或者密码有误');
            }

            $user = \Auth::user();
            return $this->ajax('ok', '登录成功', [
                'name' => $user->name,
                'username' => $user->username,
                'access_token' => $access_token,
                'expires' => time() + 60 * 60, // 一个小时过期
            ]);
        } catch (JWTException $e) {
             return $this->response->errorInternal('无法生成 Token');
        }
    }
}

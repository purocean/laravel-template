<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Dingo\Api\Routing\Helpers;

/**
 *  @Resource("Users", uri="/users")
 */
class UserController extends Controller
{
    use Helpers;

    /**
     * @Get("/")
     */
    public function index()
    {
        return '尼玛';
    }

    public function auth(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $credentials = ['password' => $password, 'username' => $username];

        try {
            if (!$access_token = JWTAuth::attempt($credentials)) {
                $this->response->errorUnauthorized('帐号或密码错误');
            }
            return $this->response->array(['access_token' => $access_token]);
        } catch (JWTException $e) {
            $this->response->errorInternal('暂时无法生成token');
        }
    }
}

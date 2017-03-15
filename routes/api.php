<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    // 无需登录即可操作
    $api->get('/qrcode', 'UserController@qrcode');
    $api->get('/wxcode', 'WechatController@code');
    $api->post('/login', 'UserController@login');
    $api->post('/qrlogin', 'UserController@qrlogin');
    $api->post('/codelogin', 'UserController@codelogin');

    // 需要登录才能操作
    $api->group(['middleware' => ['api.auth', 'jwt.refresh']], function ($api) {
        // 无需特殊权限
        $api->post('/confirmqrlogin', 'UserController@confirmqrlogin');
        $api->get('/limits', 'UserController@limits');

        // 用户
        $api->group(['middleware' => ['can.path:/users/*']], function ($api) {
            $api->get('/users/list', 'UserController@list');
            $api->post('/users/sync', 'UserController@sync');
        });

        // 部门
        $api->group(['middleware' => ['can.path:/departments/*']], function ($api) {
            $api->post('/departments/sync', 'DepartmentController@sync');
            $api->get('/departments/list', 'DepartmentController@list');
        });
    });
});

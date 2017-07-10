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

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    // 无需登录即可操作
    $api->get('qrcode', 'AuthController@qrcode');
    $api->get('wxcode', 'WechatController@code');
    $api->get('wxjs', 'WechatController@wxjs');
    $api->post('login', 'AuthController@login');
    $api->post('qrlogin', 'AuthController@qrlogin');
    $api->post('codelogin', 'AuthController@codelogin');
    // 需要登录才能操作
    $api->group(['middleware' => ['api.auth', 'jwt.refresh']], function ($api) {
        // 无需特殊权限
        $api->post('confirmqrlogin', 'AuthController@confirmqrlogin');
        $api->get('limits', 'AuthController@limits');
        $api->get('file/{id?}', 'FileController@download');
        $api->post('file/upload', 'FileController@upload');

        // 用户
        $api->group(['middleware' => ['can.path:/users/*']], function ($api) {
            $api->get('users', 'UserController@list');
            $api->post('users/sync', 'UserController@sync');
            $api->post('users/sendmessage/{username}', 'UserController@sendMessage');
        });

        // RBAC
        $api->group(['middleware' => ['can.path:/rbac/*']], function ($api) {
            $api->get('rbac/roles', 'RbacController@allroles');
            $api->get('rbac/roles/users/{rolename}', 'RbacController@usersOfRole');
            $api->get('rbac/roles/{username}', 'RbacController@rolesOfUser');
            $api->post('rbac/roles/attach', 'RbacController@attachRoles');
        });

        // 部门
        $api->group(['middleware' => ['can.path:/departments/*']], function ($api) {
            $api->post('/departments/sync', 'DepartmentController@sync');
            $api->get('/departments', 'DepartmentController@list');
        });
    });
});

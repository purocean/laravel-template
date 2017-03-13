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
$api->version('v1', function ($api) {
    $api->post('/login', 'App\Http\Controllers\Api\V1\UserController@login');

    $api->group([
        'namespace' => 'App\Http\Controllers\Api\V1',
        'middleware' => ['api.auth', 'jwt.refresh']
    ], function ($api) {
        // 用户
        $api->get('/users/items', 'UserController@items');
        $api->get('/users/list', 'UserController@list');
        $api->post('/users/sync', 'UserController@sync');

        // 部门
        $api->post('/departments/sync', 'DepartmentController@sync');
        $api->get('/departments/list', 'DepartmentController@list');
    });
});

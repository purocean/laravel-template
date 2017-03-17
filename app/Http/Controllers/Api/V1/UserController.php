<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SyncUserFromQywx;
use App\User;
use App\Role;

/**
 * 用户
 *
 * @Resource("用户", uri="/api/users")
 */
class UserController extends Controller
{
    /**
     * 从企业号同步用户
     *
     * @post("sync")
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": null,
     *     "errors":null,
     *     "code":0
     * })
     */
    public function sync()
    {
        dispatch(new SyncUserFromQywx);

        return $this->ajax('ok', "已经开始同步，请稍后刷新页面查看同步结果");
    }

    /**
     * 获取用户列表
     * search 参数可以搜索 name，username，mobile，email
     *
     * @get("list{?page=1&search=管理员}")
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
     *             {
     *                 "created_at": "2017-03-14 20:42:26",
     *                 "departments": "{}",
     *                 "email": null,
     *                 "id": 1,
     *                 "info": "{}",
     *                 "mobile": "",
     *                 "name": "超级管理员",
     *                 "status": 0,
     *                 "updated_at": "2017-03-14 20:42:49",
     *                 "username": "suadmin",
                    },
     *         }
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
    public function list(Request $request)
    {
        $search = $request->input('search');

        $data = User::where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->paginate(15)
                    ->toArray();

        return $this->ajax('ok', '获取成功', $data);
    }

    /**
     * 给用户分配角色
     *
     * @post("attachroles")
     * @Request({"username": "admin", "rolenames": {"admin", "user"}})
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": null,
     *     "errors":null,
     *     "code":0
     * })
     */
    public function attachRoles(Request $request)
    {
        $username = $request->json('username');
        $rolenames = $request->json('rolenames');

        $user = User::where(['username' => $username])->firstOrFail();

        $user->roles()->sync([]);

        try {
            foreach ($rolenames as $rolename) {
                $role = Role::where(['name' => $rolename])->firstOrFail();
                $user->attachRole($role);
            }
        } catch (\Exception $e) {
            return $this->ajax('error', '出现一点错误，角色名不存在');
        }

        return $this->ajax('ok', '操作成功');
    }

    /**
     * 获取所有的角色列表
     *
     * @get("allroles")
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         {
     *             "id": 1,
     *             "name": "suadmin",
     *             "display_name": "超级管理员",
     *             "description": "suadmin",
     *             "created_at": "2017-03-16 11:14:14",
     *             "updated_at": "2017-03-16 11:14:14"
     *         },
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
    public function allRoles()
    {
        return $this->ajax('ok', '获取成功', Role::get());
    }

    /**
     * 向某个用户发送微信消息
     *
     * @post("sendmessage")
     * @Request({"username": "testuser", "message": "测试消息"})
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": null,
     *     "errors":null,
     *     "code":0
     * })
     */
    public function sendMessage(Request $request)
    {
        $username = $request->json('username');
        $message = $request->json('message');

        if (User::sendWxMsg($username, '管理员消息', $message)) {
            return $this->ajax('ok', '发送消息成功');
        }

        return $this->ajax('error', '发送消息失败');
    }

    /**
     * 获取某个用户所有的角色
     *
     * @get("roles{?username=suadmin}")
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         {
     *             "id": 1,
     *             "name": "suadmin",
     *             "display_name": "超级管理员",
     *             "description": "suadmin",
     *             "created_at": "2017-03-16 11:14:14",
     *             "updated_at": "2017-03-16 11:14:14",
     *             "pivot": {
     *                 "user_id": 1,
     *                 "role_id": 1
     *             }
     *         },
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
    public function roles(Request $request)
    {
        $username = $request->input('username');

        $user = User::findByUsername($username);

        return $this->ajax('ok', '获取成功', $user->roles->toArray());
    }
}

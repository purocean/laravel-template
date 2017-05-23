<?php

namespace App\Http\Controllers\APi\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;

/**
 * RBAC
 *
 * @Resource("RBAC", uri="/api/rbac")
 */
class RbacController extends Controller
{
    /**
     * 获取所有的角色列表
     *
     * @Get("roles")
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
     * 创建一个新角色
     *
     * @Post("roles")
     * @Request({"name": "角色名", "display": "显示名字", "description": "描述"})
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": null,
     *     "errors": null,
     *     "code": 0
     * })
     */
    public function createRole(Request $request)
    {
        $role = new Role();

        $name = $request->input('name');
        $display = $request->input('display');
        $description = $request->input('description');

        $role->name = $name;
        $role->display_name = $display;
        $role->description = $description;

        $role->saveOrFail();

        return $this->ajax('ok', '创建角色成功');
    }

    /**
     * 删除一个角色
     *
     * @Delete("roles/roleid")
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": null,
     *     "errors": null,
     *     "code": 0
     * })
     */
    public function removeRole($id)
    {
        $role = Role::findOrFail($id);

        $role->users()->sync([]);
        $role->perms()->sync([]);

        if ($role->forceDelete()) {
            return $this->ajax('ok', '操作成功');
        } else {
            return $this->ajax('error', '操作失败');
        }
    }

    /**
     * 给用户分配角色
     *
     * @Post("roles/attch")
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
     * 获取某个用户所有的角色
     *
     * @Get("roles/username")
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
    public function rolesOfUser($username)
    {
        $user = User::findByUsername($username);

        return $this->ajax('ok', '获取成功', $user->roles->toArray());
    }

    /**
     * 获取某个角色所有的用户
     *
     * @Get("roles/users/rolename")
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
     *     "errors": null,
     *     "code": 0
     * })
     */
    public function usersOfRole($rolename)
    {
        $role = Role::where('name', $rolename)->firstOrFail();

        return $this->ajax('ok', '获取成功', $role->users()->paginate(15));
    }
}

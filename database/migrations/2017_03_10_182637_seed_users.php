<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class SeedUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('rbac:addrole', ['name' => 'suadmin', 'displayName' => '超级管理员']);
        Artisan::call('rbac:addrole', ['name' => 'admin', 'displayName' => '管理员']);
        Artisan::call('rbac:addrole', ['name' => 'user', 'displayName' => '普通用户']);

        Artisan::call('rbac:adduser', ['username' => 'suadmin', 'password' => str_random(10), 'rolename' => 'suadmin', 'name' => '超级管理员']);
        Artisan::call('rbac:adduser', ['username' => 'admin', 'password' => str_random(10), 'rolename' => 'suadmin', 'name' => '管理员']);
        Artisan::call('rbac:adduser', ['username' => 'demo', 'password' => str_random(10), 'rolename' => 'suadmin', 'name' => '示例用户']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Artisan::call('rbac:detachrole', ['username' => 'demo', 'rolename' => 'user']);
        Artisan::call('rbac:detachrole', ['username' => 'admin', 'rolename' => 'admin']);
        Artisan::call('rbac:detachrole', ['username' => 'suadmin', 'rolename' => 'suadmin']);

        Artisan::call('rbac:removerole', ['name' => 'user']);
        Artisan::call('rbac:removerole', ['name' => 'admin']);
        Artisan::call('rbac:removerole', ['name' => 'suadmin']);

        DB::table('users')->where(['username' => ['suadmin', 'admin', 'demo']])->delete();
    }
}

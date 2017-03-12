<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SeedUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addrole suadmin "超级管理员"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addrole admin "管理员"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addrole user "普通用户"');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:adduser suadmin ' . str_random(10) . ' suadmin "超级管理员"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:adduser admin ' . str_random(10) . ' suadmin "管理员"');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:adduser demo ' . str_random(10) . ' suadmin "示例用户"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachrole demo user');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachrole admin admin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachrole suadmin suadmin');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:removerole user');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:removerole admin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:removerole suadmin');

        DB::table('users')->where(['username' => ['suadmin', 'admin', 'demo']])->delete();
    }
}

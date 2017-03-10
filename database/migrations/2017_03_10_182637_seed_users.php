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
        DB::table('users')->insert([
            [
                'username' => 'suadmin',
                'name' => '超级管理员',
                'email' => str_random(10).'@'.str_random(4).'.com',
                'password' => bcrypt(str_random(10)),
            ],
            [
                'username' => 'admin',
                'name' => '管理员',
                'email' => str_random(10).'@'.str_random(4).'.com',
                'password' => bcrypt(str_random(10)),
            ],
            [
                'username' => 'demo',
                'name' => '示例用户',
                'email' => str_random(10).'@'.str_random(4).'.com',
                'password' => bcrypt(str_random(10)),
            ],
        ]);

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addrole suadmin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addrole admin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addrole user');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachrole suadmin suadmin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachrole admin admin');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachrole demo user');
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

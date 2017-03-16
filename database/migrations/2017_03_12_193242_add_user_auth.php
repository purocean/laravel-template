<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddUserAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('rbac:addperm', ['name' => '/users/*']);

        Artisan::call('rbac:attachperm', ['rolename' => 'suadmin', 'permname' => '/users/*']);
        Artisan::call('rbac:attachperm', ['rolename' => 'admin', 'permname' => '/users/*']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Artisan::call('rbac:detachperm', ['rolename' => 'admin', 'permname' => '/users/*']);
        Artisan::call('rbac:detachperm', ['rolename' => 'suadmin', 'permname' => '/users/*']);

        Artisan::call('rbac:removeperm', ['name' => '/users/*']);
    }
}

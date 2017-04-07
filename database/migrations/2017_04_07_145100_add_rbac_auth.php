<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRbacAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('rbac:addperm', ['name' => '/rbac/*']);

        Artisan::call('rbac:attachperm', ['rolename' => 'suadmin', 'permname' => '/rbac/*']);
        Artisan::call('rbac:attachperm', ['rolename' => 'admin', 'permname' => '/rbac/*']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Artisan::call('rbac:detachperm', ['rolename' => 'admin', 'permname' => '/rbac/*']);
        Artisan::call('rbac:detachperm', ['rolename' => 'suadmin', 'permname' => '/rbac/*']);

        Artisan::call('rbac:removeperm', ['name' => '/rbac/*']);
    }
}

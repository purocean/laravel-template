<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddDepartmentsAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('rbac:addperm', ['name' => '/departments/*']);

        Artisan::call('rbac:attachperm', ['rolename' => 'suadmin', 'permname' => '/departments/*']);
        Artisan::call('rbac:attachperm', ['rolename' => 'admin', 'permname' => '/departments/*']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Artisan::call('rbac:detachperm', ['rolename' => 'admin', 'permname' => '/departments/*']);
        Artisan::call('rbac:detachperm', ['rolename' => 'suadmin', 'permname' => '/departments/*']);

        Artisan::call('rbac:removeperm', ['name' => '/departments/*']);
    }
}

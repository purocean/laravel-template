<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class AddQrloginAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Artisan::call('rbac:addperm', ['name' => '/qrlogin/*']);

        Artisan::call('rbac:attachperm', ['rolename' => 'suadmin', 'permname' => '/qrlogin/*']);
        Artisan::call('rbac:attachperm', ['rolename' => 'admin', 'permname' => '/qrlogin/*']);
        Artisan::call('rbac:attachperm', ['rolename' => 'user', 'permname' => '/qrlogin/*']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Artisan::call('rbac:detachperm', ['rolename' => 'user', 'permname' => '/qrlogin/*']);
        Artisan::call('rbac:detachperm', ['rolename' => 'admin', 'permname' => '/qrlogin/*']);
        Artisan::call('rbac:detachperm', ['rolename' => 'suadmin', 'permname' => '/qrlogin/*']);

        Artisan::call('rbac:removeperm', ['name' => '/qrlogin/*']);
    }
}

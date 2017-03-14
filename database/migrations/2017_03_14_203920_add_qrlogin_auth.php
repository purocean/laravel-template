<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQrloginAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addperm /qrlogin/*');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachperm suadmin /qrlogin/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachperm admin /qrlogin/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachperm user /qrlogin/* ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachperm user /qrlogin/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachperm admin /qrlogin/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachperm suadmin /qrlogin/* ');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:removeperm /qrlogin/*');
    }
}

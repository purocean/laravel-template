<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addperm /users/*');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachperm suadmin /users/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachperm admin /users/* ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachperm admin /users/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachperm suadmin /users/* ');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:removeperm /users/*');
    }
}

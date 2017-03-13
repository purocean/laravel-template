<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentsAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:addperm /departments/*');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachperm suadmin /departments/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:attachperm admin /departments/* ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachperm admin /departments/* ');
        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:detachperm suadmin /departments/* ');

        passthru('php "' . $_SERVER['PHP_SELF'] . '" rbac:removeperm /departments/*');
    }
}

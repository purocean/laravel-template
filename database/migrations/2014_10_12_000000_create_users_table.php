<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->comment('用户名');
            $table->string('name')->default('')->comment('名字');
            $table->string('email')->unique()->nullable()->comment('邮箱');
            $table->string('mobile')->default('')->comment('电话号码');
            $table->string('password')->comment('密码');
            $table->text('departments')->comment('部门信息');
            $table->text('info')->comment('个人信息');
            $table->smallInteger('status')->default(0)->comment('状态');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

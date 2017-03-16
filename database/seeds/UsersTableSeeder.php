<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('rbac:adduser', ['username' => 'useradmin', 'password' => 'password', 'rolename' => 'admin']);
        Artisan::call('rbac:adduser', ['username' => 'usertest', 'password' => 'password', 'rolename' => 'user']);
    }
}

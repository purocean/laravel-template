<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
    }
}

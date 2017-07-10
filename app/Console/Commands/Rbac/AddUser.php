<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\User;
use App\Role;

class AddUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:adduser {username} {password} {rolename?} {name?} {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = new User();

        $username = $this->argument('username');
        $password = $this->argument('password');
        $email = $this->argument('email');
        $name = $this->argument('name') ?: 'new_user_'.str_random(4);
        $rolename = $this->argument('rolename');

        $user->username = $username;
        $user->password = bcrypt($password);
        $user->name = $name;
        $user->email = $email;
        $user->info = '{}';

        $user->saveOrFail();

        $this->info("new user {$username}, password is {$password}");

        if ($rolename) {
            $role = Role::where(['name' => $rolename])->firstOrFail();

            $user->attachRole($role);

            $this->info("attach role $rolename for $username");
        }
    }
}

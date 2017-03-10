<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\User;
use App\Role;

class AttachRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:attachrole {username} {rolename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach role for user';

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
        $username = $this->argument('username');
        $rolename = $this->argument('rolename');

        $user = User::where(['username' => $username])->firstOrFail();
        $role = Role::where(['name' => $rolename])->firstOrFail();

        $user->attachRole($role);

        $this->info("attach role $rolename for $username");
    }
}

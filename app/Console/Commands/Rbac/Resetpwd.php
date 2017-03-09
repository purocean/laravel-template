<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\User;

class Resetpwd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:resetpwd {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'reset password';

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
        $password = $this->argument('password');

        $user = User::where(['username' => $username])->firstOrFail();
        $user->password = bcrypt($password);
        $user->saveOrFail();

        $this->info("$username new password is $password");
    }
}

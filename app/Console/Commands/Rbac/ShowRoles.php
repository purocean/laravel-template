<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\User;
use App\Role;
use App\Permission;

class ShowRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:showroles {username?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'show roles of permissions';

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
        $headers = ['name', 'display_name', 'description'];

        if (empty($username)) {
            $this->table($headers, Role::all($headers));
        } else {
            $user = User::where(['username' => $username])->firstOrFail();
            $this->table($headers, $user->roles->map(function ($row) {
                return $row->makeHidden(['id', 'pivot', 'created_at', 'updated_at'])->toArray();
            }));
        }
    }
}

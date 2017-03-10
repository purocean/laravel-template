<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\Role;
use App\Permission;

class detachPerm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:detachperm {rolename} {permname}';

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
        $rolename = $this->argument('rolename');
        $permname = $this->argument('permname');

        $role = Role::where(['name' => $rolename])->firstOrFail();
        $perm = Permission::where(['name' => $permname])->firstOrFail();

        $role->detachPermission($perm);

        $this->info("detach permission $permname from $rolename");
    }
}

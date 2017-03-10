<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\Role;
use App\Permission;

class ShowPerms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:showperms {rolename?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'show permissions of roles';

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
        $headers = ['name', 'display_name', 'description'];

        if (empty($rolename)) {
            $this->table($headers, Permission::all($headers));
        } else {
            $role = Role::where(['name' => $rolename])->firstOrFail();
            $this->table($headers, $role->perms->map(function ($row) {
                return $row->makeHidden(['id', 'pivot', 'created_at', 'updated_at'])->toArray();
            }));
        }
    }
}

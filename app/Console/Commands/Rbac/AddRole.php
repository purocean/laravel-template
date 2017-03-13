<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\Role;

class AddRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:addrole {name} {displayName?} {description?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add role';

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
        $role = new Role();

        $name = $this->argument('name');
        $displayName = $this->argument('displayName') ?: $name;
        $description = $this->argument('description') ?: $name;

        $role->name = $name;
        $role->display_name = $displayName;
        $role->description = $description;

        $role->saveOrFail();

        $this->info("new role {$name}");
    }
}

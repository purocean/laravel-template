<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\Permission;

class AddPerm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:addperm {name} {displayName?} {description?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission';

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
        $permission = new Permission();

        $name = $this->argument('name');
        $displayName = $this->argument('displayName') ?: $name;
        $description = $this->argument('description') ?: $name;

        $permission->name = $name;
        $permission->display_name = $displayName;
        $permission->description = $description;

        $permission->saveOrFail();

        $this->info("new permission {$name}");
    }
}

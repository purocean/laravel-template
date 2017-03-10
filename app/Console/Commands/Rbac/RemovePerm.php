<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\Permission;

class RemovePerm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:removeperm {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove permission';

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
        $name = $this->argument('name');

        $permission = Permission::where(['name' => $name])->firstOrFail();

        $permission->forceDelete();

        $this->info("removed permission {$name}");
    }
}

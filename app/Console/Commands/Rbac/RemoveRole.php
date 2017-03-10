<?php

namespace App\Console\Commands\Rbac;

use Illuminate\Console\Command;
use App\Role;

class RemoveRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:removerole {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove role';

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

        $role = Role::where(['name' => $name])->firstOrFail();

        $role->users()->sync([]);
        $role->perms()->sync([]);
        $role->forceDelete();

        $this->info("removed role {$name}");
    }
}

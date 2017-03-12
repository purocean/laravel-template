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

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        $name = $this->argument('name');
        $displayName = $this->argument('displayName') ?: $name;
        $description = $this->argument('description') ?: $name;

        $permission->name = $name;
        $permission->display_name = $isWindows ? mb_convert_encoding($displayName, 'UTF-8', 'GBK') : $displayName;
        $permission->description = $isWindows ? mb_convert_encoding($description, 'UTF-8', 'GBK') : $description;

        $permission->saveOrFail();

        $this->info("new permission {$name}");
    }
}

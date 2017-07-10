<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Repositories\DepartmentRepository;
use App\Repositories\UserRepository;

class SyncFromQywx implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userRepo;
    protected $departmentRepo;

    public function __construct(
        UserRepository $userRepo,
        DepartmentRepository $departmentRepo
    ) {
        $this->userRepo = $userRepo;
        $this->departmentRepo = $departmentRepo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->departmentRepo->sync();
        $this->userRepo->sync();
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Jobs\SyncUserFromQywx;
use Queue;

class DepartmentTest extends TestCase
{
    public function testList()
    {
        $this->getJson('/api/departments')->assertStatus(401);

        $this->iam('usertest')->getJson('/api/departments')->assertStatus(403);

        $this->iam('useradmin')->getJson('/api/departments')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function testSync()
    {
        $this->postJson('/api/departments/sync')->assertStatus(401);

        $this->iam('usertest')->postJson('/api/departments/sync')->assertStatus(403);

        $this->iam('suadmin')->getJson('/api/departments/sync')->assertStatus(405);

        $this->iam('suadmin', function () {
            Queue::fake();
        })->postJson('/api/departments/sync')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        Queue::assertPushed(SyncUserFromQywx::class);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RbacTest extends TestCase
{
    public function testAllRoles()
    {
        $this->getJson('/api/rbac/roles')->assertStatus(401);

        $this->iam('usertest')->getJson('/api/rbac/roles')->assertStatus(403);

        $this->iam('useradmin')
            ->getJson('/api/rbac/roles')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function testRoles()
    {
        $this->getJson('/api/rbac/roles/admin')->assertStatus(401);

        $this->iam('usertest')->getJson('/api/rbac/roles/admin')->assertStatus(403);

        $this->iam('useradmin')
            ->getJson('/api/rbac/roles/admin')
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function testAttachRoles()
    {
        $this->postJson('/api/rbac/roles/attach')->assertStatus(401);

        $this->iam('usertest')->postJson('/api/rbac/roles/attach')->assertStatus(403);

        $this->iam('useradmin')
            ->postJson('/api/rbac/roles/attach', ['username' => 'usertest', 'rolenames' => ['admin', 'xxxx']])
            ->assertStatus(200)
            ->assertJson(['status' => 'error']);

        $this->iam('useradmin')
            ->postJson('/api/rbac/roles/attach', ['username' => 'usertest', 'rolenames' => ['admin', 'user']])
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class AuthTest extends TestCase
{
    public function testLogin()
    {
        // 错误密码登录
        $this->postJson('/api/login', ['username' => 'useradmin', 'password' => 'xxxxxxxxx'])
            ->assertStatus(200)
            ->assertJson(['status' => 'error']);

        // 正确密码登录
        $this->postJson('/api/login', ['username' => 'useradmin', 'password' => 'password'])
            ->assertStatus(200)
            ->assertJson(['status' => 'ok']);
    }

    public function testLimits()
    {
        $this->getJson('/api/limits')->assertStatus(401);

        $this->iam('useradmin')->getJson('/api/limits')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['roles', 'perms']]);
    }

    public function testQrlogin()
    {
        $nonce = $this->getJson('/api/qrcode')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['nonce', 'url', 'expires']])
            ->json()['data']['nonce'];

        $this->postJson('/api/qrlogin', ['nonce' => $nonce])->assertStatus(200)->assertJson(['status' => 'error', 'message' => '请扫码']);

        $this->iam('suadmin', false)->postJson('/api/confirmqrlogin', ['nonce' => $nonce, 'login' => false]);

        $this->postJson('/api/qrlogin', ['nonce' => $nonce])->assertStatus(200)->assertJson(['status' => 'error', 'message' => '请确认登录']);

        $this->iam('suadmin', false)->postJson('/api/confirmqrlogin', ['nonce' => $nonce, 'login' => true]);

        $this->postJson('/api/qrlogin', ['nonce' => $nonce])->assertStatus(200)->assertJson(['status' => 'ok']);
    }
}

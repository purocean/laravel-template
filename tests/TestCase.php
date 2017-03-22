<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\User;
use Closure;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp()
    {
        parent::setUp();

        $this->initDatabase();
    }

    protected function initDatabase()
    {
        // 搞一个测试数据库
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver'    => 'sqlite',
                'database'  => ':memory:',
                'prefix'    => '',
            ],
            'database.connections.mongodb.database' => 'laravel_template_test',
        ]);

        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
    }

    protected function resetDatabase()
    {
        Artisan::call('migrate:reset');
    }

    protected function iam($username = null, $refresh = true)
    {

        if ($refresh !== false) {
            // 下面这三句必须加，不然在一个测试里面不能多次请求
            $this->tearDown();
            $this->refreshApplication();
            $this->setUp();

            if ($refresh instanceof Closure) {
                $refresh();
            }
        }

        if ($username) {
            $user = User::where(['username' => $username])->firstOrFail();
            $token = JWTAuth::fromUser($user);

            return $this->withServerVariables(
                $this->transformHeadersToServerVars(['Authorization' => 'Bearer ' . $token])
            );
        }

        return $this;
    }

    public function tearDown()
    {
        $this->resetDatabase();
    }
}

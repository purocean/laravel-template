<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
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

        $this->initApplication();

        $this->initDatabase();
    }

    public function initApplication()
    {
        // 配置测试数据库
        config([
            'database.connections.mysql.database' => env('TEST_DATABASE', 'laravel_template_test'),
            'database.connections.mongodb.database' => env('TEST_DATABASE', 'laravel_template_test'),
        ]);
    }

    protected function initDatabase()
    {
        $this->artisan('migrate:refresh');
        $this->artisan('db:seed');
    }

    protected function resetDatabase()
    {
        $this->artisan('migrate:reset');
    }

    protected function iam($username = null, $refresh = true)
    {
        if ($refresh !== false) {
            // 下面这三句必须加，不然在一个测试里面不能多次请求
            $this->refreshApplication(); // 刷新一个新的 app 实例
            $this->initApplication(); // 配置 app 参数
            $this->artisan('queue:flush'); // 刷去任务队列的任务,以免影响下一次执行

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
        } else {
            return $this->withServerVariables([]);
        }
    }

    public function tearDown()
    {
        // $this->resetDatabase();
    }
}

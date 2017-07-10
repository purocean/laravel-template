<?php

namespace App\Biz;

use App\Repositories\UserRepository;
use App\Exceptions\NormalException;
use Wxsdk\Qywx;

class UserBiz
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function sendWxMsg($username, $title, $message, $url = '')
    {
        $qywx = new Qywx(config('qywx.app'));

        $articles = [
            $qywx->buildNewsItem($title, $message, $url, ''),
        ];

        if (!is_array($username)) {
            $username = [$username];
        }

        $username = array_filter($username, function ($name) {
            return !in_array($name, ['suadmin', 'admin', 'demo']);
        });

        $result = $qywx->sendNewsMsg(
            $articles,
            ['touser' => $username],
            config('qywx.app.appid')
        );

        if (($result['invaliduser'] ?? false) or
            ($result['invalidparty'] ?? false) or
            ($result['invalidtag'] ?? false)) {
            throw new NormalException('部分发送失败');
        }

        return true;
    }
}

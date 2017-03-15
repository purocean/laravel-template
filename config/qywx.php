<?php

return [
    'rootid' => env('QYWX_ROOTID', 1),
    'corpid' => env('QYWX_CORPID'),
    'secret' => env('QYWX_SECRET'),
    'appid' => env('QYWX_APPID'),
    'dataPath' => storage_path('app/qywx/'),
];

<?php

return [
    'contacts' => [
        'file_prefix' => 'contacts_',
        'rootid' => 1,
        'corpid' => env('QYWX_CORPID'),
        'secret' => env('QYWX_CONTACTS_SECRET'),
        'dataPath' => storage_path('app/qywx'),
    ],
    'app' => [
        'file_prefix' => 'app_',
        'corpid' => env('QYWX_CORPID'),
        'appid' => env('QYWX_APPID'),
        'secret' => env('QYWX_SECRET'),
        'dataPath' => storage_path('app/qywx'),
    ],
];

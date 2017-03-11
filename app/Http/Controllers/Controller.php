<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;

    public function ajax($status, $message = '', $data = null, $errors = null, $code = 0)
    {
        return $this->response->array([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'errors' => $errors,
            'code' => $code,
        ]);
    }
}

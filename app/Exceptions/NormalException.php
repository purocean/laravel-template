<?php

namespace App\Exceptions;

use Exception;

class NormalException extends Exception
{
    protected $errors = [];
    protected $status = 'error';
    protected $data = null;

    public function __construct($message = '', $status = 'error', $errors = [], $data = null, $code = 0)
    {
        $this->errors = $errors;
        $this->status = $status;
        $this->data = $data;

        parent::__construct($message, $code);
    }

    public function toArray()
    {
        return [
            'status' => $this->status,
            'message' => $this->getMessage(),
            'data' => $this->data,
            'errors' => $this->errors,
            'code' => $this->getCode(),
        ];
    }
}

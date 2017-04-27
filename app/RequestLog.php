<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class RequestLog extends Model
{
    protected $connection = 'mongodb';
}

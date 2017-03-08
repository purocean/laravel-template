<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 *  @Resource("Users", uri="/users")
 */
class UserController extends Controller
{
    /**
     * @Get("/")
     */
    public function index()
    {
        return '尼玛';
    }
}

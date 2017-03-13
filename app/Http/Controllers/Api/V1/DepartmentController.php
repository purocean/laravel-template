<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Qywx;
use App\Http\Controllers\Controller;
use App\Department;
use App\Jobs\SyncUserFromQywx;

class DepartmentController extends Controller
{
    public function sync()
    {
        dispatch(new SyncUserFromQywx);

        return $this->ajax('ok', "已经开始同步，请稍后刷新页面查看同步结果");
    }

    public function list()
    {
        return $this->ajax('ok', '获取成功', Department::paginate(15)->toArray());
    }
}

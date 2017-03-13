<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Qywx;
use App\Http\Controllers\Controller;
use App\Department;

class DepartmentController extends Controller
{
    public function sync()
    {
        if ($departments = Qywx::getDepartments(config('qywx')['rootid'])) {
            Department::truncate();

            if (Department::insert(array_map(function ($row) {
                return array_merge($row, [
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            }, $departments))) {
                return $this->ajax('ok', '同步数据成功');
            } else {
                return $this->ajax('error', '同步数据失败');
            }
        } else {
            return $this->ajax('error', '无法从企业号获取部门');
        }
    }

    public function list()
    {
        return $this->ajax('ok', '获取成功', Department::paginate(15)->toArray());
    }
}

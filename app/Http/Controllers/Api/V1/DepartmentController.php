<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SyncFromQywx;
use App\Repositories\DepartmentRepository;

/**
 * 部门管理
 *
 * @Resource("部门管理", uri="/api/departments")
 */
class DepartmentController extends Controller
{
    protected $departmentRepo;

    public function __construct(DepartmentRepository $departmentRepo)
    {
        $this->departmentRepo = $departmentRepo;
    }

    /**
     * 从企业号同步部门
     *
     * @Post("/sync")
     * @Response(200, body={"status": "ok|error", "message": "..."})
     */
    public function sync()
    {
        dispatch(app(SyncFromQywx::class));

        return $this->ajax('ok', "已经开始同步，请稍后刷新页面查看同步结果");
    }

    /**
     * 列出部门列表
     *
     * @Get("{?page}")
     * @Response(200, body={
     *     "status": "ok|error",
     *     "message": "...",
     *     "data": {
     *         "total": 150,
     *         "per_page": 15,
     *         "current_page": 1,
     *         "last_page": 10,
     *         "next_page_url": "http:\/\/...",
     *         "prev_page_url": null,
     *         "from": 1,
     *         "to": 15,
     *         "data": {
     *             {"created_at": "2017-03-14 20:42:26", "departments": "{}", "email": null, "id": 1, "info": "{}", "mobile": "", "name": "超级管理员", "status": 0, "updated_at": "2017-03-14 20:42:49", "username": "suadmin"},
     *         }
     *     },
     *     "errors":null,
     *     "code":0
     * })
     */
    public function list()
    {
        return $this->ajax('ok', '获取成功', $this->departmentRepo->list());
    }
}

<?php

namespace App\Repositories;
use App\Department;
use Wxsdk\Qywx;

class DepartmentRepository extends AbstractRepository
{
    protected function modelName()
    {
        return Department::class;
    }

    public function sync()
    {
        $qywx = new Qywx(config('qywx.contacts'));

        if ($departments = $qywx->getDepartments('qywx.contacts.rootid')) {
            Department::where('id', '>', '0')->delete();

            return Department::insert(array_map(function ($row) {
                return array_merge($row, [
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            }, $departments));
        }

        return false;
    }
}

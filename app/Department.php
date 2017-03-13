<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Qywx;

class Department extends Model
{
    public static function sync()
    {
        if ($departments = Qywx::getDepartments(config('qywx')['rootid'])) {
            self::truncate();

            return self::insert(array_map(function ($row) {
                return array_merge($row, [
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]);
            }, $departments));
        } else {
            return false;
        }
    }
}

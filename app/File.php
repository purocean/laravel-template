<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;

class File extends Model
{
    protected $connection = 'mongodb';

    public function realPath($thumb = false)
    {
        $path = $this->path;
        if ($thumb) {
            $path = dirname($this->path) . '/thumb_' . basename($this->path);
        }

        return storage_path('app/' . $path);
    }
}

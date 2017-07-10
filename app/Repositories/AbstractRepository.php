<?php

namespace App\Repositories;
use App\Exceptions\NormalException;

abstract class AbstractRepository
{
    abstract protected function modelName();

    public function findOrFail($id, $message = null)
    {
        if (! $model = $this->find($id)) {
            throw new NormalException($message ?: "未找到资源 [{$id}]");
        }

        return $model;
    }

    public function find($id)
    {
        $modelName = $this->modelName();

        return $modelName::find($id);
    }

    public function list($orderBy = 'id', $direction = 'desc', $perPage = 15)
    {
        $modelName = $this->modelName();

        return $modelName::orderBy($orderBy, $direction)->paginate($perPage);
    }

    public function updateOrCreate($id, $data)
    {
        $modelName = $this->modelName();

        $model = $id ? $this->findOrFail($id) : new $modelName;

        if ($model->fill($data)->save()) {
            return $model;
        }

        return null;
    }

    public function delete($id)
    {
        if ($model = $this->find($id)) {
            return $model->delete();
        }

        return true;
    }
}

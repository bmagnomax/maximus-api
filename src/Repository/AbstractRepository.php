<?php

namespace MaximusApi\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AbstractRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        return $record->update($data);
    }

    public function delete($id)
    {
        $record = $this->model->find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    public function search(Request $request)
    {
        $query = $this->model->newQuery();

        foreach ($request->all() as $field => $value) {
            if (Schema::hasColumn($this->model->getTable(), $field)) {
                $value = $this->sanitizeValue($value);
                $query->orWhere($field, 'LIKE', "%$value%");
            }
        }

        return $query->get();
    }

    public function withRelations($relations)
    {
        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        return $this->model->with($relations)->get();
    }

    protected function sanitizeValue($value)
    {
        return str_replace(['%', '_'], ['\%', '\_'], $value);
    }
}

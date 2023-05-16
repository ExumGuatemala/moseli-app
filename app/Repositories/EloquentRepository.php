<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class EloquentRepository implements EloquentRepositoryInterface
{
    protected $model;

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findWith(int $id, array $with = [])
    {
        return $this->model->with($with)->find($id);
    }

    public function findBy(array $attributes)
    {
        $query = $this->model;

        foreach ($attributes as $key => $value) {
            $query = $query->where($key, $value);
        }

        return $query->first();
    }

    public function findMany(array $ids, array $with = []): Collection
    {
        return $this->model->with($with)->findMany($ids);
    }

    public function mustGetById(int $id, array $with = [])
    {
        $query = $this->make($with);

        return $query->findOrFail($id);
    }

    public function getManyBy($key, $value, array $with = [])
    {
        $obj = $this->make($with);

        return $obj->where($key, $value)->get();
    }

    public function mustFindBy($key, $value, array $with = [])
    {
        if ($value === null) {
            throw new ModelNotFoundException();
        }

        $obj = $this->make($with);

        return $obj->where($key, $value)->firstOrFail();
    }

    public function firstOrCreate(array $attributes)
    {
        return $this->model->firstOrCreate($attributes);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function updateById(int $id, array $attributes): bool
    {
        $obj = $this->model->find($id);

        foreach ($attributes as $key => $value) {
            $obj->{$key} = $value;
        }

        return $obj->save();
    }

    public function make(array $with)
    {
        return $this->model->with($with);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function delete(int $id): bool
    {
        return $this->deleteBy('id', $id);
    }

    public function deleteBy($key, $value)
    {
        $obj = $this->model->where($key, $value)->firstOrFail();

        return $obj->delete();
    }

    public function deleteMany(int ...$ids): bool
    {
        if (empty($ids)) {
            return true;
        }

        return $this->model->whereIn('id', $ids)
            ->delete();
    }

    public function findIn(string $key, array $value): Collection
    {
        return $this->model->whereIn($key, $value)->get();
    }
}

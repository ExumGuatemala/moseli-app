<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

interface EloquentRepositoryInterface
{
    public function find(int $id);

    public function findWith(int $id, array $with = []);

    public function findBy(array $attributes);

    public function findMany(array $ids, array $with = []): Collection;

    public function mustGetById(int $id, array $with = []);

    public function getManyBy($key, $value, array $with = []);

    public function mustFindBy($key, $value, array $with = []);

    public function create(array $attributes);

    public function updateById(int $id, array $attributes): bool;

    public function firstOrCreate(array $attributes);

    public function make(array $with);

    public function all(): Collection;

    public function delete(int $id): bool;

    public function deleteMany(int ...$ids): bool;

    public function findIn(string $key, array $value): Collection;

}

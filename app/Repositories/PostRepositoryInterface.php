<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Post;

interface PostRepositoryInterface
{
    public function getAll(): Collection;
    public function getById(int $id): ?Post;
    public function create(array $data): Post;
    public function update(array $data, int $id): bool;
    public function delete(int $id): bool;
}
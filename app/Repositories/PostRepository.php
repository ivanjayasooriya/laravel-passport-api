<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function getAll(): Collection
    {
        return Post::with('user')->latest()->get();
    }

    public function getById(int $id): ?Post
    {
        return Post::with('user')->find($id);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(array $data, int $id): bool
    {
        $post = Post::find($id);
        if (!$post) {
            return false;
        }
        return $post->update($data);
    }

    public function delete(int $id): bool
    {
        $post = Post::find($id);
        if (!$post) {
            return false;
        }
        return $post->delete();
    }
}
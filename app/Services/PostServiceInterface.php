<?php
namespace App\Services;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface PostServiceInterface
{
    public function getAllPosts(): Collection;
    public function getPostById(int $id): ?Post;
    public function createPost(array $data, int $userId): Post;
    public function updatePost(int $id, array $data): ?Post;
    public function deletePost(int $id): bool;
}
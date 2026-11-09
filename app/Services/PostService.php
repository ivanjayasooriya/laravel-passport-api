<?php

namespace App\Services;

use App\Models\Post;
use App\Repositories\PostRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PostService implements PostServiceInterface
{
    protected PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts(): Collection
    {
        return $this->postRepository->getAll();
    }

    public function getPostById(int $id): ?Post
    {
        return $this->postRepository->getById($id);
    }

    public function createPost(array $data, int $userId): Post
    {
        DB::beginTransaction();
        try {
            $data['user_id'] = $userId;
            $post = $this->postRepository->create($data);
            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updatePost(int $id, array $data): ?Post
    {
        $post = $this->postRepository->getById($id);
        if (!$post) {
            return null;
        }

        Gate::authorize('update', $post);

        DB::beginTransaction();
        try {
            $updated = $this->postRepository->update($id, $data);
            if (!$updated) {
                DB::rollBack();
                return null;
            }
            DB::commit();
            return $post;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deletePost(int $id): bool
    {
        $post = $this->postRepository->getById($id);
            if (!$post) {
                return false;
            }

        Gate::authorize('delete', $post);
        
        DB::beginTransaction();
        try {
            $deleted = $this->postRepository->delete($id);
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
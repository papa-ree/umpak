<?php

namespace Bale\Umpak\Repositories;

use Bale\Umpak\Contracts\PostRepositoryInterface;
use Bale\Umpak\DTOs\PostData;
use Bale\Umpak\Exceptions\PostNotFoundException;
use Bale\Umpak\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function getBySlug(string $slug): PostData
    {
        $post = $this->findBySlug($slug);

        if ($post === null) {
            throw PostNotFoundException::forSlug($slug);
        }

        return $post;
    }

    public function findBySlug(string $slug): ?PostData
    {
        return Post::published()
            ->where('slug', $slug)
            ->first()
            ?->toData();
    }

    public function latest(int $limit = 10): Collection
    {
        return Post::published()
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get()
            ->map(fn (Post $post) => $post->toData());
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Post::published()
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    public function byCategory(string $categorySlug, int $limit = 10): Collection
    {
        return Post::published()
            ->where('category_slug', $categorySlug)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get()
            ->map(fn (Post $post) => $post->toData());
    }

    public function search(int $limit = 10, string $keyword = '', string $date = ''): Collection
    {
        return $this->buildSearchQuery($keyword, $date)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get()
            ->map(fn (Post $post) => $post->toData());
    }

    public function countSearch(string $keyword = '', string $date = ''): int
    {
        return $this->buildSearchQuery($keyword, $date)->count();
    }

    public function suggested(int $limit = 5): Collection
    {
        return Post::published()
            ->inRandomOrder()
            ->limit($limit)
            ->get()
            ->map(fn (Post $post) => $post->toData());
    }

    private function buildSearchQuery(string $keyword, string $date)
    {
        $query = Post::published();

        if ($keyword) {
            $keyword = addcslashes($keyword, '%_');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%', '\\')
                  ->orWhere('content', 'like', '%' . $keyword . '%', '\\');
            });
        }

        if ($date) {
            if (str_contains($date, ' to ')) {
                [$start, $end] = explode(' to ', $date);
                $query->whereBetween('published_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
            } else {
                $query->whereDate('published_at', $date);
            }
        }

        return $query;
    }
}

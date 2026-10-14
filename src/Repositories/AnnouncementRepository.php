<?php

namespace Bale\Umpak\Repositories;

use Bale\Umpak\Contracts\AnnouncementRepositoryInterface;
use Bale\Umpak\DTOs\AnnouncementData;
use Bale\Umpak\Exceptions\AnnouncementNotFoundException;
use Bale\Umpak\Models\Announcement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AnnouncementRepository implements AnnouncementRepositoryInterface
{
    public function getBySlug(string $slug): AnnouncementData
    {
        $announcement = $this->findBySlug($slug);

        if ($announcement === null) {
            throw AnnouncementNotFoundException::forSlug($slug);
        }

        return $announcement;
    }

    public function findBySlug(string $slug): ?AnnouncementData
    {
        return Announcement::published()
            ->where('slug', $slug)
            ->first()
            ?->toData();
    }

    public function latest(int $limit = 10): Collection
    {
        return Announcement::published()
            ->orderByDesc('is_pinned')
            ->orderByDesc('start_date')
            ->limit($limit)
            ->get()
            ->map(fn (Announcement $announcement) => $announcement->toData());
    }

    public function pinned(int $limit = 5): Collection
    {
        return Announcement::published()
            ->where('is_pinned', true)
            ->orderByDesc('start_date')
            ->limit($limit)
            ->get()
            ->map(fn (Announcement $announcement) => $announcement->toData());
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Announcement::published()
            ->orderByDesc('is_pinned')
            ->orderByDesc('start_date')
            ->paginate($perPage);
    }

    public function search(int $limit = 10, string $keyword = '', string $date = ''): Collection
    {
        return $this->buildSearchQuery($keyword, $date)
            ->orderByDesc('start_date')
            ->limit($limit)
            ->get()
            ->map(fn (Announcement $announcement) => $announcement->toData());
    }

    public function countSearch(string $keyword = '', string $date = ''): int
    {
        return $this->buildSearchQuery($keyword, $date)->count();
    }

    private function buildSearchQuery(string $keyword, string $date): Builder
    {
        $query = Announcement::published();

        if ($keyword) {
            $keyword = addcslashes($keyword, '%_');
            $query->where(function (Builder $q) use ($keyword) {
                $q->where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('content', 'like', '%'.$keyword.'%');
            });
        }

        if ($date) {
            if (str_contains($date, ' to ')) {
                [$start, $end] = explode(' to ', $date);
                $query->whereBetween('start_date', [$start.' 00:00:00', $end.' 23:59:59']);
            } else {
                $query->whereDate('start_date', $date);
            }
        }

        return $query;
    }
}

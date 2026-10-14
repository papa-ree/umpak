<?php

namespace Bale\Umpak\Contracts;

use Bale\Umpak\DTOs\AnnouncementData;
use Bale\Umpak\Exceptions\AnnouncementNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AnnouncementRepositoryInterface
{
    /**
     * Ambil pengumuman berdasarkan slug.
     *
     * @throws AnnouncementNotFoundException
     */
    public function getBySlug(string $slug): AnnouncementData;

    /**
     * Ambil pengumuman berdasarkan slug, kembalikan null jika tidak ditemukan.
     */
    public function findBySlug(string $slug): ?AnnouncementData;

    /**
     * Ambil pengumuman yang dipublish, terbaru lebih dahulu.
     *
     * @return Collection<int, AnnouncementData>
     */
    public function latest(int $limit = 10): Collection;

    /**
     * Ambil pengumuman penting (is_pinned), terbaru lebih dahulu.
     *
     * @return Collection<int, AnnouncementData>
     */
    public function pinned(int $limit = 5): Collection;

    /**
     * Ambil pengumuman dengan pagination.
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    /**
     * Cari pengumuman berdasarkan keyword dan filter tanggal.
     *
     * @return Collection<int, AnnouncementData>
     */
    public function search(int $limit = 10, string $keyword = '', string $date = ''): Collection;

    /**
     * Hitung total pengumuman berdasarkan filter pencarian.
     */
    public function countSearch(string $keyword = '', string $date = ''): int;
}

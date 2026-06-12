<?php

namespace Bale\Umpak\Contracts;

use Bale\Umpak\DTOs\PostData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PostRepositoryInterface
{
    /**
     * Ambil post berdasarkan slug.
     *
     * @throws \Bale\Umpak\Exceptions\PostNotFoundException
     */
    public function getBySlug(string $slug): PostData;

    /**
     * Ambil post berdasarkan slug, kembalikan null jika tidak ditemukan.
     */
    public function findBySlug(string $slug): ?PostData;

    /**
     * Ambil post terbaru milik org aktif, dengan opsi limit.
     *
     * @return Collection<int, PostData>
     */
    public function latest(int $limit = 10): Collection;

    /**
     * Ambil post terbaru dengan pagination.
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    /**
     * Ambil post berdasarkan kategori.
     *
     * @return Collection<int, PostData>
     */
    public function byCategory(string $category, int $limit = 10): Collection;

    /**
     * Cari post berdasarkan keyword dan filter tanggal.
     *
     * @return Collection<int, PostData>
     */
    public function search(int $limit = 10, string $keyword = '', string $date = ''): Collection;

    /**
     * Hitung total post berdasarkan filter pencarian.
     */
    public function countSearch(string $keyword = '', string $date = ''): int;

    /**
     * Ambil post secara random untuk rekomendasi.
     *
     * @return Collection<int, PostData>
     */
    public function suggested(int $limit = 5): Collection;
}

<?php

namespace Bale\Umpak\Contracts;

use Bale\Umpak\DTOs\AgendaData;
use Bale\Umpak\Exceptions\AgendaNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AgendaRepositoryInterface
{
    /**
     * Ambil agenda berdasarkan id.
     *
     * @throws AgendaNotFoundException
     */
    public function getById(string $id): AgendaData;

    /**
     * Ambil agenda berdasarkan id, kembalikan null jika tidak ditemukan.
     */
    public function findById(string $id): ?AgendaData;

    /**
     * Ambil agenda yang akan datang (start_date >= sekarang), urut terdekat.
     *
     * @return Collection<int, AgendaData>
     */
    public function upcoming(int $limit = 10): Collection;

    /**
     * Ambil agenda terbaru (berdasarkan start_date terbaru).
     *
     * @return Collection<int, AgendaData>
     */
    public function latest(int $limit = 10): Collection;

    /**
     * Ambil agenda dengan pagination.
     */
    public function paginate(int $perPage = 10): LengthAwarePaginator;
}

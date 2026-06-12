<?php

namespace Bale\Umpak\Contracts;

use Bale\Umpak\DTOs\PageData;
use Illuminate\Support\Collection;

interface PageRepositoryInterface
{
    /**
     * Ambil page berdasarkan slug.
     *
     * @throws \Bale\Umpak\Exceptions\PageNotFoundException
     */
    public function getBySlug(string $slug): PageData;

    /**
     * Ambil page berdasarkan slug, kembalikan null jika tidak ditemukan.
     */
    public function findBySlug(string $slug): ?PageData;

    /**
     * Ambil semua page yang dipublish milik org aktif.
     *
     * @return Collection<int, PageData>
     */
    public function allPublished(): Collection;
}

<?php

namespace Bale\Umpak\Contracts;

use Bale\Umpak\DTOs\SectionData;
use Illuminate\Support\Collection;

interface SectionRepositoryInterface
{
    /**
     * Ambil section berdasarkan slug.
     *
     * @throws \Bale\Umpak\Exceptions\SectionNotFoundException
     */
    public function getBySlug(string $slug): SectionData;

    /**
     * Ambil section berdasarkan slug, kembalikan null jika tidak ditemukan.
     */
    public function findBySlug(string $slug): ?SectionData;

    /**
     * Ambil semua section milik org_slug yang sedang aktif.
     *
     * @return Collection<int, SectionData>
     */
    public function allForOrg(): Collection;
}

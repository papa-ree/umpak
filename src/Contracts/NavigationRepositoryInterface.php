<?php

namespace Bale\Umpak\Contracts;

use Bale\Umpak\DTOs\NavigationData;
use Illuminate\Support\Collection;

interface NavigationRepositoryInterface
{
    /**
     * Ambil semua root navigation items yang aktif,
     * beserta children-nya (eager loaded, satu level).
     *
     * @return Collection<int, NavigationData>
     */
    public function rootWithChildren(): Collection;
}

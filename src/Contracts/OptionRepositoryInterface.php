<?php

namespace Bale\Umpak\Contracts;

use Bale\Umpak\DTOs\OptionData;

interface OptionRepositoryInterface
{
    /**
     * Ambil semua options sebagai OptionData snapshot.
     */
    public function all(): OptionData;

    /**
     * Ambil nilai option berdasarkan key.
     */
    public function get(string $key, ?string $default = null): ?string;
}

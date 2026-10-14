<?php

namespace Bale\Umpak\Exceptions;

use RuntimeException;

class AnnouncementNotFoundException extends RuntimeException
{
    public static function forSlug(string $slug): self
    {
        return new self("Pengumuman dengan slug [{$slug}] tidak ditemukan.");
    }
}

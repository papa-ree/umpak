<?php

namespace Bale\Umpak\Exceptions;

use RuntimeException;

class SectionNotFoundException extends RuntimeException
{
    public static function forSlug(string $slug): self
    {
        return new self("Section dengan slug [{$slug}] tidak ditemukan.");
    }
}

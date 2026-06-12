<?php

namespace Bale\Umpak\Exceptions;

use RuntimeException;

class PageNotFoundException extends RuntimeException
{
    public static function forSlug(string $slug): self
    {
        return new self("Page dengan slug [{$slug}] tidak ditemukan.");
    }
}

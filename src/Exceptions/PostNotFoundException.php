<?php

namespace Bale\Umpak\Exceptions;

use RuntimeException;

class PostNotFoundException extends RuntimeException
{
    public static function forSlug(string $slug): self
    {
        return new self("Post dengan slug [{$slug}] tidak ditemukan.");
    }
}

<?php

namespace Bale\Umpak\Exceptions;

use RuntimeException;

class AgendaNotFoundException extends RuntimeException
{
    public static function forId(string $id): self
    {
        return new self("Agenda dengan id [{$id}] tidak ditemukan.");
    }
}

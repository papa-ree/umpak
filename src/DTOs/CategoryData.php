<?php

namespace Bale\Umpak\DTOs;

readonly class CategoryData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
    ) {}
}

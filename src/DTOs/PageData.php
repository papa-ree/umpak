<?php

namespace Bale\Umpak\DTOs;

use Carbon\Carbon;

/**
 * Representasi data Page yang dikembalikan ke landing page package.
 */
readonly class PageData
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $title,

        /**
         * Type page — menentukan cara render konten.
         * Default: 'paragraph' (EditorJS)
         */
        public string $type,

        /**
         * EditorJS JSON output dalam bentuk array.
         *
         * @var array<string, mixed>|null
         */
        public ?array $content,

        public Carbon $updatedAt,
    ) {}

    /**
     * Cek apakah page memiliki konten EditorJS.
     */
    public function hasContent(): bool
    {
        return ! empty($this->content['blocks']);
    }
}

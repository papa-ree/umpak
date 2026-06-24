<?php

namespace Bale\Umpak\DTOs;

use Carbon\Carbon;

/**
 * Representasi data Post yang dikembalikan ke landing page package.
 *
 * Catatan:
 * - thumbnail sudah full URL dari CDN, tidak perlu cdn_asset()
 * - content adalah EditorJS JSON yang dirender via <x-umpak::editorjs-renderer>
 */
readonly class PostData
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $title,
        public ?string $excerpt,

        /**
         * EditorJS JSON output dalam bentuk array.
         * Gunakan $post->hasContent() sebelum render.
         *
         * @var array<string, mixed>|null
         */
        public ?array $content,

        /**
         * Full URL thumbnail dari CDN.
         * Gunakan langsung di <img src>, tidak perlu cdn_asset().
         */
        public ?string $thumbnail,

        public ?string $categorySlug,
        public Carbon $publishedAt,
        public Carbon $updatedAt,
        public ?object $seo = null,
    ) {}

    /**
     * Format tanggal publikasi.
     * Contoh: "12 Mei 2025"
     */
    public function formattedDate(string $format = 'd F Y'): string
    {
        return $this->publishedAt->translatedFormat($format);
    }

    /**
     * Cek apakah post memiliki thumbnail.
     */
    public function hasThumbnail(): bool
    {
        return ! empty($this->thumbnail);
    }

    /**
     * Cek apakah post memiliki konten EditorJS.
     */
    public function hasContent(): bool
    {
        return ! empty($this->content['blocks']);
    }
}

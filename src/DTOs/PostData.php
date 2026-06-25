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

    /**
     * SEO Helpers (Proxy to SeoMeta model if exists)
     */
    public function getSeoTitle(): string
    {
        return $this->seo?->title ?? $this->title;
    }

    public function getSeoDescription(): string
    {
        return $this->seo?->description ?? $this->excerpt ?? '';
    }

    public function getOgTitle(): string
    {
        return $this->seo?->og_title ?? $this->getSeoTitle();
    }

    public function getOgDescription(): string
    {
        return $this->seo?->og_description ?? $this->getSeoDescription();
    }

    public function getOgImage(): ?string
    {
        return $this->seo?->og_image ?? $this->thumbnail;
    }

    public function getSeoKeywords(): string
    {
        return $this->seo?->keywords ?? '';
    }

    public function getCanonicalUrl(): string
    {
        return $this->seo?->canonical_url ?? url()->current();
    }

    public function getSeoRobots(): string
    {
        return $this->seo?->robots ?? 'index, follow';
    }

    public function getStructuredData(): ?array
    {
        return $this->seo?->structured_data;
    }
}

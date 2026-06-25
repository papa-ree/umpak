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
        public ?object $seo = null,
    ) {}

    /**
     * Cek apakah page memiliki konten EditorJS.
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
        return $this->seo?->description ?? '';
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
        return $this->seo?->og_image;
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

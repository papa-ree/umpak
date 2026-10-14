<?php

namespace Bale\Umpak\DTOs;

use Carbon\Carbon;

/**
 * Representasi data Announcement yang dikembalikan ke landing page package.
 *
 * Catatan:
 * - attachment sudah full URL dari CDN, tidak perlu cdn_asset()
 * - content adalah EditorJS JSON yang dirender via <x-umpak::editorjs-renderer>
 */
readonly class AnnouncementData
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $title,
        public ?string $excerpt,

        /**
         * EditorJS JSON output dalam bentuk array.
         * Gunakan $announcement->hasContent() sebelum render.
         *
         * @var array<string, mixed>|null
         */
        public ?array $content,

        /**
         * Full URL lampiran dari CDN.
         * Gunakan langsung di <a href>, tidak perlu cdn_asset().
         */
        public ?string $attachment,

        public bool $isPinned,
        public bool $published,
        public ?Carbon $startDate,
        public ?Carbon $endDate,
        public Carbon $createdAt,
        public Carbon $updatedAt,
        public ?object $seo = null,
    ) {}

    /**
     * Format tanggal publikasi/terbit.
     * Contoh: "12 Mei 2025"
     */
    public function formattedDate(string $format = 'd F Y'): string
    {
        return ($this->startDate ?? $this->createdAt)->translatedFormat($format);
    }

    /**
     * Cek apakah pengumuman memiliki konten EditorJS.
     */
    public function hasContent(): bool
    {
        return ! empty($this->content['blocks']);
    }

    /**
     * Cek apakah pengumuman memiliki lampiran.
     */
    public function hasAttachment(): bool
    {
        return ! empty($this->attachment);
    }

    /**
     * Apakah pengumuman aktif (published + dalam jendela start/end).
     */
    public function isActive(): bool
    {
        if (! $this->published) {
            return false;
        }

        $now = Carbon::now();

        if ($this->startDate !== null && $now->lt($this->startDate)) {
            return false;
        }

        if ($this->endDate !== null && $now->gt($this->endDate)) {
            return false;
        }

        return true;
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
        $image = $this->seo?->og_image ?? $this->attachment;

        if (empty($image)) {
            return null;
        }

        // Clean double slashes
        return preg_replace('#(?<!:)/+#', '/', $image);
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

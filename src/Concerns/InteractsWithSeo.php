<?php

namespace Bale\Umpak\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Trait InteractsWithSeo
 * 
 * Memberikan dukungan SEO opsional pada model Umpak.
 * Secara otomatis mendeteksi keberadaan package bale/seo.
 */
trait InteractsWithSeo
{
    /**
     * Relasi ke metadata SEO (Polymorphic).
     * Jika bale/seo tidak terinstall, akan merender relasi kosong.
     */
    public function seoMeta(): MorphOne
    {
        if (class_exists('Bale\Seo\Models\SeoMeta')) {
            return $this->morphOne('Bale\Seo\Models\SeoMeta', 'seoable');
        }

        // Return empty relation if SEO package is missing
        return $this->morphOne(static::class, 'id')->whereRaw('1=0');
    }

    /**
     * Mendapatkan Judul SEO.
     * Fallback ke title model jika SEO meta tidak ada.
     */
    public function getSeoTitle(): string
    {
        return $this->seoMeta?->title ?? $this->title ?? '';
    }

    /**
     * Mendapatkan Deskripsi SEO.
     * Fallback ke excerpt/content model jika SEO meta tidak ada.
     */
    public function getSeoDescription(): string
    {
        return $this->seoMeta?->description ?? $this->excerpt ?? '';
    }

    /**
     * Mendapatkan Open Graph Title.
     */
    public function getOgTitle(): string
    {
        return $this->seoMeta?->og_title ?? $this->getSeoTitle();
    }

    /**
     * Mendapatkan Open Graph Description.
     */
    public function getOgDescription(): string
    {
        return $this->seoMeta?->og_description ?? $this->getSeoDescription();
    }

    /**
     * Mendapatkan Open Graph Image.
     */
    public function getOgImage(): ?string
    {
        if ($this->seoMeta?->og_image) {
            return $this->seoMeta->og_image;
        }

        return $this->thumbnail ?? null;
    }

    /**
     * Mendapatkan Keywords SEO.
     */
    public function getSeoKeywords(): string
    {
        return $this->seoMeta?->keywords ?? '';
    }

    /**
     * Mendapatkan Canonical URL.
     */
    public function getCanonicalUrl(): ?string
    {
        return $this->seoMeta?->canonical_url ?? null;
    }

    /**
     * Mendapatkan Robots Meta.
     */
    public function getSeoRobots(): string
    {
        return $this->seoMeta?->robots ?? 'index, follow';
    }

    /**
     * Mendapatkan Structured Data (JSON-LD).
     */
    public function getStructuredData(): ?array
    {
        return $this->seoMeta?->structured_data ?? null;
    }

    /**
     * Mendapatkan Open Graph Type.
     */
    public function getOgType(): string
    {
        if (str_contains(get_class($this), 'Post')) {
            return 'article';
        }

        return 'website';
    }

    /**
     * Mendapatkan Twitter Card Type.
     */
    public function getTwitterCardType(): string
    {
        return $this->seoMeta?->twitter_card ?? 'summary_large_image';
    }

    /**
     * Cek apakah package SEO terinstall.
     */
    public function hasSeoPackage(): bool
    {
        return class_exists('Bale\Seo\Models\SeoMeta');
    }
}

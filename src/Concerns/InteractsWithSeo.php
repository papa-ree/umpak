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
        // Try calling method from bale/seo trait if applied manually or exists
        if (method_exists($this, 'getSeoTitleFromPackage')) {
            return $this->getSeoTitleFromPackage();
        }

        return $this->seoMeta?->title ?? $this->title ?? '';
    }

    /**
     * Mendapatkan Deskripsi SEO.
     * Fallback ke excerpt/content model jika SEO meta tidak ada.
     */
    public function getSeoDescription(): string
    {
        if (method_exists($this, 'getSeoDescriptionFromPackage')) {
            return $this->getSeoDescriptionFromPackage();
        }

        return $this->seoMeta?->description ?? $this->excerpt ?? '';
    }

    /**
     * Cek apakah package SEO terinstall.
     */
    public function hasSeoPackage(): bool
    {
        return class_exists('Bale\Seo\Models\SeoMeta');
    }
}

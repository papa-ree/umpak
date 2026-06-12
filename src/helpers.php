<?php

use Bale\Umpak\Contracts\OptionRepositoryInterface;
use Bale\Umpak\Services\CdnService;

if (! function_exists('cdn_asset')) {
    /**
     * Generate full URL ke aset di CDN.
     *
     * Format: {cdn_url}/{cdn_prefix}/{org_slug}/{path}
     *
     * Pengecualian path 'shared/':
     *   cdn_asset('shared/logo.png') → {cdn_url}/{prefix}/shared/logo.png
     *   cdn_asset('images/foto.jpg') → {cdn_url}/{prefix}/{org_slug}/images/foto.jpg
     *
     * Jika CDN tidak aktif, mengembalikan path relatif via asset().
     */
    function cdn_asset(string $path): string
    {
        return app(CdnService::class)->url($path);
    }
}

if (! function_exists('cdn_url')) {
    /**
     * Alias dari cdn_asset().
     */
    function cdn_url(string $path): string
    {
        return cdn_asset($path);
    }
}

if (! function_exists('cdn_enabled')) {
    /**
     * Cek apakah CDN sedang aktif.
     */
    function cdn_enabled(): bool
    {
        return app(CdnService::class)->isEnabled();
    }
}

if (! function_exists('umpak_org')) {
    /**
     * Ambil nilai konfigurasi organisasi dari tabel options.
     *
     * Penggunaan:
     *   umpak_org()           → OptionData (seluruh konfigurasi org)
     *   umpak_org('slug')     → organization_slug
     *   umpak_org('name')     → organization_name
     *   umpak_org('logo')     → organization_logo
     *   umpak_org('address')  → organization_address
     *   umpak_org('phone')    → organization_phone
     *   umpak_org('email')    → organization_email
     */
    function umpak_org(?string $key = null): mixed
    {
        $repo = app(OptionRepositoryInterface::class);

        if ($key === null) {
            return $repo->all();
        }

        return $repo->get("organization_{$key}");
    }
}

if (! function_exists('umpak_option')) {
    /**
     * Ambil nilai option arbitrary dari tabel options.
     * Tidak terbatas pada key organization_* saja.
     *
     * Penggunaan:
     *   umpak_option('url')
     *   umpak_option('social_facebook')
     *   umpak_option('key_custom', 'fallback')
     */
    function umpak_option(string $key, ?string $default = null): ?string
    {
        return app(OptionRepositoryInterface::class)->get($key, $default);
    }
}

if (! function_exists('umpak_config')) {
    /**
     * Shortcut untuk mengakses config umpak.
     *
     * Penggunaan:
     *   umpak_config('cdn.url')
     *   umpak_config('balystics_id')
     */
    function umpak_config(string $key, mixed $default = null): mixed
    {
        return config("umpak.{$key}", $default);
    }
}

<?php

namespace Bale\Umpak\Services;

use Bale\Umpak\Models\Option;

class CdnService
{
    private readonly bool $enabled;
    private readonly string $baseUrl;
    private readonly string $prefix;

    public function __construct()
    {
        $this->enabled = (bool) config('umpak.cdn.enabled', false);
        $this->baseUrl = rtrim((string) config('umpak.cdn.url', ''), '/');
        $this->prefix  = trim((string) config('umpak.cdn.prefix', 'bale'), '/');
    }

    /**
     * Generate full URL ke aset.
     *
     * org_slug diambil dari tabel options (key: organization_slug)
     * agar konsisten dengan sumber data yang sebenarnya.
     *
     * Path 'shared/' tidak menyertakan org_slug:
     *   url('shared/logo.png') → {base}/{prefix}/shared/logo.png
     *   url('images/foto.jpg') → {base}/{prefix}/{org_slug}/images/foto.jpg
     */
    public function url(string $path): string
    {
        $path = ltrim($path, '/');

        if (! $this->isEnabled()) {
            return asset($path);
        }

        if (str_starts_with($path, 'shared/')) {
            return "{$this->baseUrl}/{$this->prefix}/{$path}";
        }

        $orgSlug = ltrim($this->orgSlug(), '/');

        $fullPath = $orgSlug 
            ? "{$this->prefix}/{$orgSlug}/{$path}" 
            : "{$this->prefix}/{$path}";

        return "{$this->baseUrl}/" . ltrim($fullPath, '/');
    }

    public function isEnabled(): bool
    {
        return $this->enabled && $this->baseUrl !== '';
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Ambil org_slug dari tabel options.
     * Fallback ke string kosong jika belum dikonfigurasi.
     */
    public function orgSlug(): string
    {
        try {
            return Option::getValue('organization_slug', '') ?? '';
        } catch (\Throwable) {
            return '';
        }
    }
}

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
     * Format dengan CDN aktif:
     *   shared/logo.png    → {cdn_url}/{prefix}/shared/logo.png
     *   images/foto.jpg    → {cdn_url}/{prefix}/{org_slug}/images/foto.jpg
     *
     * Format ketika CDN tidak aktif (fallback ke asset() lokal):
     *   shared/logo.png    → asset({prefix}/shared/logo.png)
     *   images/foto.jpg    → asset({prefix}/{org_slug}/images/foto.jpg)
     *
     * Path prefix tidak pernah digandakan:
     *   Jika path sudah dimulai dengan {prefix}/, path digunakan as-is.
     */
    public function url(string $path): string
    {
        $path = ltrim($path, '/');

        // Bersihkan path dari directory traversal (.. atau \\)
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#\.\.(/|$)#', '', $path);

        // Cegah double-prefix: jika path sudah diawali dengan prefix/, gunakan as-is
        if (str_starts_with($path, "{$this->prefix}/")) {
            $fullPath = $path;
            return $this->isEnabled()
                ? "{$this->baseUrl}/{$fullPath}"
                : asset($fullPath);
        }

        if (str_starts_with($path, 'shared/')) {
            $fullPath = "{$this->prefix}/{$path}";
            return $this->isEnabled()
                ? "{$this->baseUrl}/{$fullPath}"
                : asset($fullPath);
        }

        $orgSlug  = ltrim($this->orgSlug(), '/');
        $fullPath = $orgSlug
            ? "{$this->prefix}/{$orgSlug}/{$path}"
            : "{$this->prefix}/{$path}";

        return $this->isEnabled()
            ? "{$this->baseUrl}/" . ltrim($fullPath, '/')
            : asset($fullPath);
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
        if (function_exists('umpak_org')) {
            return (string) umpak_org('slug');
        }

        try {
            return Option::getValue('organization_slug', '') ?? '';
        } catch (\Throwable) {
            return '';
        }
    }
}

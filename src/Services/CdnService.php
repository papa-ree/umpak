<?php

namespace Bale\Umpak\Services;

use Bale\Umpak\Models\Option;

class CdnService
{
    /**
     * Baca config secara lazy saat digunakan, bukan di constructor.
     *
     * Keuntungan:
     * - Selalu membaca config yang aktif (termasuk setelah config:cache diperbarui)
     * - Tidak ada masalah jika singleton diinstansiasi sebelum config fully loaded
     * - Perubahan config tanpa restart server langsung terefleksi
     */
    private function cdnEnabled(): bool
    {
        return (bool) config('umpak.cdn.enabled', false);
    }

    private function cdnBaseUrl(): string
    {
        return rtrim((string) config('umpak.cdn.url', ''), '/');
    }

    private function cdnPrefix(): string
    {
        return trim((string) config('umpak.cdn.prefix', 'bale'), '/');
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
        $path   = ltrim($path, '/');
        $prefix = $this->cdnPrefix();

        // Bersihkan path dari directory traversal (.. atau \\)
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#\.\.(/|$)#', '', $path);

        // Cegah double-prefix: jika path sudah diawali dengan prefix/, gunakan as-is
        if (str_starts_with($path, "{$prefix}/")) {
            return $this->isEnabled()
                ? "{$this->cdnBaseUrl()}/{$path}"
                : asset($path);
        }

        if (str_starts_with($path, 'shared/')) {
            $fullPath = "{$prefix}/{$path}";
            return $this->isEnabled()
                ? "{$this->cdnBaseUrl()}/{$fullPath}"
                : asset($fullPath);
        }

        $orgSlug  = ltrim($this->orgSlug(), '/');
        $fullPath = $orgSlug
            ? "{$prefix}/{$orgSlug}/{$path}"
            : "{$prefix}/{$path}";

        return $this->isEnabled()
            ? "{$this->cdnBaseUrl()}/" . ltrim($fullPath, '/')
            : asset($fullPath);
    }

    public function isEnabled(): bool
    {
        return $this->cdnEnabled() && $this->cdnBaseUrl() !== '';
    }

    public function getBaseUrl(): string
    {
        return $this->cdnBaseUrl();
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

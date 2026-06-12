<?php

namespace Bale\Umpak;

class Umpak
{
    /**
     * Registry landing page yang terdaftar secara dinamis oleh theme packages.
     *
     * @var array<string, array{slug: string, name: string}>
     */
    protected array $registeredPages = [];

    /**
     * Versi package saat ini.
     */
    public function version(): string
    {
        return '0.1.0';
    }

    /**
     * Ambil konfigurasi organisasi aktif.
     *
     * @return array{slug: string, name: string}
     */
    public function org(): array
    {
        return [
            'slug' => config('umpak.org_slug', ''),
            'name' => config('umpak.org_name', ''),
        ];
    }

    /**
     * Cek apakah sebuah feature aktif.
     */
    public function featureEnabled(string $feature): bool
    {
        return (bool) config("umpak.features.{$feature}", false);
    }

    // -------------------------------------------------------------------------
    // Landing Page Registry
    // -------------------------------------------------------------------------

    /**
     * Daftarkan landing page ke registry Umpak.
     *
     * Dipanggil oleh masing-masing theme ServiceProvider di register():
     *   app(Umpak::class)->registerLandingPage('kominfo', 'Dinas Kominfo');
     */
    public function registerLandingPage(string $slug, string $name): void
    {
        $this->registeredPages[$slug] = [
            'slug' => $slug,
            'name' => $name,
        ];
    }

    /**
     * Ambil seluruh landing page yang terdaftar.
     *
     * Menggabungkan dynamic registry (dari registerLandingPage) dengan
     * static override dari config('umpak.landing_page.pages').
     * Static config diutamakan (override dynamic).
     *
     * @return array<string, array{slug: string, name: string}>
     */
    public function landingPages(): array
    {
        return array_merge(
            $this->registeredPages,
            config('umpak.landing_page.pages', []),
        );
    }

    /**
     * Ambil slug landing page yang sedang aktif.
     * Mengembalikan null jika tidak ada pembatasan (single-theme mode).
     */
    public function activeLandingPage(): ?string
    {
        $active = config('umpak.landing_page.active');

        return ($active === null || $active === '') ? null : $active;
    }
}

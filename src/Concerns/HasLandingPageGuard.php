<?php

namespace Bale\Umpak\Concerns;

/**
 * Trait untuk digunakan oleh bale-* theme ServiceProviders.
 *
 * Penggunaan:
 *
 *   use Bale\Umpak\Concerns\HasLandingPageGuard;
 *
 *   class BaleDinkesServiceProvider extends ServiceProvider
 *   {
 *       use HasLandingPageGuard;
 *
 *       protected function landingPageSlug(): string
 *       {
 *           return 'dindik';
 *       }
 *
 *       public function boot(): void
 *       {
 *           if ($this->isActiveLandingPage()) {
 *               $this->app->booted(fn () => $this->loadRoutesFrom(...));
 *               $this->app['view']->prependLocation(...);
 *           }
 *       }
 *   }
 */
trait HasLandingPageGuard
{
    /**
     * Slug landing page milik package ini.
     * Wajib diimplementasikan oleh masing-masing theme package.
     */
    abstract protected function landingPageSlug(): string;

    /**
     * Cek apakah landing page ini sedang aktif.
     *
     * Logika:
     * - ACTIVE_LANDING_PAGE tidak di-set (null/kosong)
     *   → aktif (single-theme / non-multi-tenant mode)
     * - ACTIVE_LANDING_PAGE di-set
     *   → cocokkan dengan landingPageSlug() package ini
     */
    protected function isActiveLandingPage(): bool
    {
        $active = config('umpak.landing_page.active');

        // Single-theme mode: tidak ada pembatasan
        if ($active === null || $active === '') {
            return true;
        }

        return $active === $this->landingPageSlug();
    }
}

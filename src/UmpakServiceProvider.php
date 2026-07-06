<?php

namespace Bale\Umpak;

use Bale\Umpak\Concerns\HasLivewireComponents;
use Bale\Umpak\Console\Commands\InstallCommand;
use Bale\Umpak\Console\Commands\MakeCommand;
use Bale\Umpak\Console\Commands\SwitchLandingPageCommand;
use Bale\Umpak\Contracts\NavigationRepositoryInterface;
use Bale\Umpak\Contracts\OptionRepositoryInterface;
use Bale\Umpak\Contracts\PageRepositoryInterface;
use Bale\Umpak\Contracts\PostRepositoryInterface;
use Bale\Umpak\Contracts\SectionRepositoryInterface;
use Bale\Umpak\Repositories\NavigationRepository;
use Bale\Umpak\Repositories\OptionRepository;
use Bale\Umpak\Repositories\PageRepository;
use Bale\Umpak\Repositories\PostRepository;
use Bale\Umpak\Repositories\SectionRepository;
use Bale\Umpak\Services\CdnService;
use Bale\Umpak\View\Components\Icon;
use Bale\Umpak\ViewComposers\LandingPageComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class UmpakServiceProvider extends ServiceProvider
{
    use HasLivewireComponents;

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/umpak.php', 'umpak');

        $this->app->singleton(Umpak::class);
        $this->app->singleton(CdnService::class);

        $this->app->bind(SectionRepositoryInterface::class, SectionRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
        $this->app->bind(NavigationRepositoryInterface::class, NavigationRepository::class);
        $this->app->bind(OptionRepositoryInterface::class, OptionRepository::class);

        $this->configureCachePrefix();

        // TODO: Tahap 4 — bind SitemapService
        // $this->app->singleton(SitemapService::class);
    }

    /**
     * Dapatkan dan sesuaikan prefix cache/redis serta session cookie 
     * secara dinamis berdasarkan landing page yang aktif. Ini mencegah
     * tabrakan key pada shared Redis yang digunakan oleh beberapa bale.
     */
    private function configureCachePrefix(): void
    {
        $active = config('umpak.landing_page.active');

        if ($active) {
            // Isolasi Cache Prefix
            $cachePrefix = config('cache.prefix');
            if ($cachePrefix && !str_contains($cachePrefix, $active)) {
                config(['cache.prefix' => rtrim($cachePrefix, '_-') . '_' . $active]);
            }

            // Isolasi Redis Prefix
            $redisPrefix = config('database.redis.options.prefix');
            if ($redisPrefix && !str_contains($redisPrefix, $active)) {
                config(['database.redis.options.prefix' => rtrim($redisPrefix, '_-') . '_' . $active . ':']);
            }

            // Isolasi Session Cookie
            $sessionCookie = config('session.cookie');
            if ($sessionCookie && !str_contains($sessionCookie, $active)) {
                config(['session.cookie' => rtrim($sessionCookie, '_-') . '_' . $active]);
            }
        }
    }

    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerRoutes();
        $this->registerComponents();
        $this->registerViewComposers();

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }
    }

    private function registerPublishing(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../config/umpak.php' => config_path('umpak.php'),
        ], 'umpak:config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/umpak'),
        ], 'umpak:views');
    }

    private function registerRoutes(): void
    {
        // TODO: Tahap 4 — aktifkan setelah SitemapService selesai
        // $this->loadRoutesFrom(__DIR__.'/../routes/umpak.php');
    }

    private function registerComponents(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'umpak');

        Blade::anonymousComponentNamespace('umpak::components', 'umpak');
        Blade::component('umpak::icon', Icon::class);

        $this->discoverLivewireComponents(
            __DIR__ . '/Livewire',
            'Bale\\Umpak\\Livewire',
            'umpak'
        );
    }

    private function registerViewComposers(): void
    {
        // Hanya attach ke views namespace umpak sendiri (bukan semua view).
        // Theme packages harus memanggil View::composer() sendiri di dalam
        // blok isActiveLandingPage() mereka agar tidak mempengaruhi bale lain.
        View::composer('umpak::*', LandingPageComposer::class);
    }

    /**
     * Utility untuk digunakan oleh theme ServiceProviders yang ingin
     * menginject $umpakOrg + $umpakNav ke view mereka.
     *
     * Panggil ini **di dalam** blok isActiveLandingPage() di boot() theme:
     *
     *   if ($this->isActiveLandingPage()) {
     *       $this->app->booted(fn () => $this->registerLandingPageComposer('bale-dindik::*'));
     *   }
     *
     * @param string $viewPattern Pola view yang akan di-compose, misal: 'bale-dindik::*'
     */
    public static function registerLandingPageComposer(string $viewPattern): void
    {
        \Illuminate\Support\Facades\View::composer($viewPattern, LandingPageComposer::class);
    }

    private function registerCommands(): void
    {
        $this->commands([
            InstallCommand::class,
            MakeCommand::class,
            SwitchLandingPageCommand::class,
        ]);
    }
}

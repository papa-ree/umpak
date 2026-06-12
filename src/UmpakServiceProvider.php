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

        // TODO: Tahap 4 — bind SitemapService
        // $this->app->singleton(SitemapService::class);
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

        $this->publishes([
            __DIR__ . '/../resources/js/umpak.js' => public_path('vendor/umpak/umpak.js'),
        ], 'umpak:assets');
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
        View::composer('*', LandingPageComposer::class);
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

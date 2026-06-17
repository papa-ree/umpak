<?php

namespace Bale\Umpak\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Scaffold landing page package baru berbasis Full-Page Livewire.
 */
class MakeCommand extends Command
{
    protected $signature = 'umpak:make
                            {name? : Nama package, contoh: bale-kominfo}
                            {slug? : Slug organisasi, contoh: kominfo}
                            {namespace? : Namespace package (PascalCase), contoh: KominfoBC}
                            {--path=packages : Direktori target relatif dari root project}
                            {--force : Overwrite jika sudah ada}';

    protected $description = 'Scaffold landing page package baru (Full-Page Livewire) berbasis bale/umpak';

    private string $pkgName;
    private string $pkgSlug;
    private string $pkgPascal;
    private string $pkgNamespace;
    private string $pkgPath;

    public function handle(): int
    {
        $this->pkgName = $this->argument('name') ?? $this->ask('Masukkan nama package (contoh: bale-kominfo)');

        $defaultSlug = Str::after($this->pkgName, 'bale-');
        $this->pkgSlug = $this->argument('slug') ?? $this->ask('Masukkan slug organisasi', $defaultSlug);

        $defaultNamespace = Str::studly(Str::after($this->pkgName, 'bale-'));
        $inputNamespace = $this->argument('namespace') ?? $this->ask('Masukkan namespace (PascalCase, prefix Bale\\ otomatis)', $defaultNamespace);

        $this->pkgPascal = $inputNamespace;
        $this->pkgNamespace = 'Bale\\' . $inputNamespace;
        $packageDir = $this->option('path');
        $this->pkgPath = base_path("{$packageDir}/{$this->pkgName}");

        $this->components->info("Scaffolding package <fg=cyan>{$this->pkgName}</> (Full-Page Livewire)");
        $this->newLine();

        if (is_dir($this->pkgPath) && !$this->option('force')) {
            $this->components->error("Package [{$this->pkgName}] sudah ada di [{$this->pkgPath}].");
            $this->line('  Gunakan <fg=yellow>--force</> untuk overwrite.');

            return self::FAILURE;
        }

        $this->createDirectories();
        $this->createComposerJson();
        $this->createServiceProvider();
        $this->createLivewirePages(); // Pengganti Controller
        $this->createRoutes();
        $this->createLayouts();
        $this->createErrorPages();
        $this->createCss();
        $this->createJs();

        $this->newLine();
        $this->components->info("Package <fg=cyan>{$this->pkgName}</> berhasil dibuat.");
        $this->newLine();

        $this->components->twoColumnDetail('<fg=yellow>Langkah berikutnya</>', '');
        $this->line("  1. Tambahkan ke <fg=cyan>composer.json</> utama:");
        $this->line("     <fg=white>\"repositories\": [{\"type\": \"path\", \"url\": \"{$packageDir}/{$this->pkgName}\"}]</>");
        $this->line("     <fg=white>\"require\": {\"bale/{$this->pkgSlug}\": \"*\"}</>");
        $this->line('  2. Jalankan <fg=cyan>composer update</>');
        $this->line("  3. Daftarkan provider di <fg=cyan>bootstrap/providers.php</>:");
        $this->line("     <fg=white>{$this->pkgNamespace}\\{$this->pkgPascal}ServiceProvider::class</>");
        $this->newLine();

        return self::SUCCESS;
    }

    private function createDirectories(): void
    {
        $this->components->task('Creating directories', function () {
            $dirs = [
                'src/Livewire/LandingPage/Post',
                'src/Livewire/LandingPage/Page',
                'resources/views/layouts',
                'resources/views/errors',
                'resources/views/livewire/landing-page/post',
                'resources/views/livewire/landing-page/page',
                'resources/css',
                'resources/js',
                'routes',
            ];

            foreach ($dirs as $dir) {
                @mkdir("{$this->pkgPath}/{$dir}", 0755, true);
            }
        });
    }

    private function createComposerJson(): void
    {
        $this->components->task('Creating composer.json', function () {
            $content = json_encode([
                'name' => "bale/{$this->pkgSlug}",
                'description' => "Bale Landing Page — " . Str::title(str_replace('-', ' ', $this->pkgSlug)),
                'license' => 'MIT',
                'authors' => [
                    [
                        'name' => 'Ricky Romdhoni',
                        'email' => 'ricky.romdhoni@gmail.com',
                    ]
                ],
                'require' => [
                    'php' => '^8.3',
                    'bale/umpak' => '*',
                ],
                'autoload' => [
                    'psr-4' => [
                        "{$this->pkgNamespace}\\" => 'src/',
                    ],
                ],
                'extra' => [
                    'laravel' => [
                        'providers' => [
                            "{$this->pkgNamespace}\\{$this->pkgPascal}ServiceProvider",
                        ],
                    ],
                ],
                'minimum-stability' => 'dev',
                'prefer-stable' => true,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            file_put_contents("{$this->pkgPath}/composer.json", $content);
        });
    }

    private function createServiceProvider(): void
    {
        $this->components->task('Creating ServiceProvider', function () {
            $stub = <<<PHP
<?php

namespace {$this->pkgNamespace};

use Bale\Umpak\Concerns\HasLandingPageGuard;
use Bale\Umpak\Concerns\HasLivewireComponents;
use Bale\Umpak\Umpak;
use Illuminate\Support\ServiceProvider;

class {$this->pkgPascal}ServiceProvider extends ServiceProvider
{
    use HasLandingPageGuard, HasLivewireComponents;

    public function register(): void
    {
        \$this->app->resolving(Umpak::class, function (Umpak \$umpak) {
            \$umpak->registerLandingPage(
                '{$this->pkgSlug}',
                \Illuminate\Support\Str::title(str_replace('-', ' ', '{$this->pkgSlug}')),
            );
        });
    }

    protected function landingPageSlug(): string
    {
        return '{$this->pkgSlug}';
    }

    public function boot(): void
    {
        if (\$this->isActiveLandingPage()) {
            \$this->app->booted(function () {
                \$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            });

            \$this->app['view']->prependLocation(__DIR__.'/../resources/views');
        }

        \$this->loadViewsFrom(__DIR__.'/../resources/views', '{$this->pkgSlug}');

        \$this->registerLivewireComponents();
    }

    protected function registerLivewireComponents(): void
    {
        \$this->discoverLivewireComponents(
            __DIR__.'/Livewire',
            '{$this->pkgNamespace}\\Livewire',
            '{$this->pkgSlug}'
        );
    }
}
PHP;
            file_put_contents(
                "{$this->pkgPath}/src/{$this->pkgPascal}ServiceProvider.php",
                $stub
            );
        });
    }

    private function createLivewirePages(): void
    {
        $this->components->task('Creating Livewire Pages', function () {
            $orgName = Str::title(str_replace('-', ' ', $this->pkgSlug));

            // ─── INDEX (HOME) ───
            $indexClass = <<<PHP
<?php

namespace {$this->pkgNamespace}\Livewire\LandingPage;

use Bale\Umpak\Livewire\UmpakComponent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Index extends UmpakComponent
{
    #[Layout('layouts.app')]
    #[Title('{$orgName}')]
    public function render()
    {
        return view('{$this->pkgSlug}::livewire.landing-page.index');
    }
}
PHP;
            file_put_contents("{$this->pkgPath}/src/Livewire/LandingPage/Index.php", $indexClass);

            $indexView = <<<BLADE
<div>
    {{-- 
        Index sebagai pengepul komponen.
        Gunakan data dari UmpakComponent helper jika perlu.
        Contoh: <livewire:{$this->pkgSlug}.hero.index /> 
    --}}
    
    <div class="py-20 text-center">
        <h1 class="text-4xl font-bold">Welcome to {$orgName}</h1>
        <p class="mt-4 text-gray-600">Start building your amazing landing page by stacking components here.</p>
    </div>
</div>
BLADE;
            file_put_contents("{$this->pkgPath}/resources/views/livewire/landing-page/index.blade.php", $indexView);

            // ─── POST LIST ───
            $postListClass = <<<PHP
<?php

namespace {$this->pkgNamespace}\Livewire\LandingPage\Post;

use Bale\Umpak\Livewire\UmpakComponent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

class PostList extends UmpakComponent
{
    use WithPagination;

    #[Layout('layouts.app')]
    #[Title('Berita — {$orgName}')]
    public function render()
    {
        return view('{$this->pkgSlug}::livewire.landing-page.post.post-list', [
            'posts' => \$this->latestPosts(12) // Atau implementasi custom paginasi
        ]);
    }
}
PHP;
            file_put_contents("{$this->pkgPath}/src/Livewire/LandingPage/Post/PostList.php", $postListClass);

            $postListView = <<<BLADE
<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold mb-8">Berita</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach(\$posts as \$post)
            <article class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                @if(\$post->hasThumbnail())
                    <img src="{{ \$post->thumbnail }}" class="w-full h-48 object-cover">
                @endif
                <div class="p-5">
                    <h2 class="font-bold text-lg mb-2">{{ \$post->title }}</h2>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ \$post->excerpt }}</p>
                    <a href="{{ route('{$this->pkgSlug}.post.show', \$post->slug) }}" class="text-blue-600 font-medium hover:underline">
                        Baca Selengkapnya
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</div>
BLADE;
            file_put_contents("{$this->pkgPath}/resources/views/livewire/landing-page/post/post-list.blade.php", $postListView);

            // ─── POST SHOW ───
            $postShowClass = <<<PHP
<?php

namespace {$this->pkgNamespace}\Livewire\LandingPage\Post;

use Bale\Umpak\Livewire\UmpakComponent;
use Livewire\Attributes\Layout;

class PostShow extends UmpakComponent
{
    public string \$slug;

    public function mount(string \$slug): void
    {
        
        \$this->slug = \$slug;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        \$post = \$this->post(\$this->slug);

        if (! \$post) abort(404);

        return view('{$this->pkgSlug}::livewire.landing-page.post.post-show', [
            'post' => \$post
        ])->title(\$post->title . ' — {$orgName}');
    }
}
PHP;
            file_put_contents("{$this->pkgPath}/src/Livewire/LandingPage/Post/PostShow.php", $postShowClass);

            $postShowView = <<<BLADE
<article class="container mx-auto px-4 py-12 max-w-4xl">
    <x-umpak::breadcrumb :items="[
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => 'Berita', 'url' => route('{$this->pkgSlug}.post.index')],
        ['label' => \$post->title],
    ]" class="mb-6" />

    <h1 class="text-4xl font-bold mb-4">{{ \$post->title }}</h1>
    <time class="text-sm text-gray-500 block mb-8">{{ \$post->formattedDate() }}</time>

    @if(\$post->hasThumbnail())
        <img src="{{ \$post->thumbnail }}" class="w-full rounded-xl mb-8 shadow-lg">
    @endif

    <div class="prose prose-lg dark:prose-invert max-w-none">
        <x-umpak::editorjs-renderer :content="\$post->content" />
    </div>

    <x-umpak::share-button :url="request()->url()" :title="\$post->title" class="mt-12" />
</article>
BLADE;
            file_put_contents("{$this->pkgPath}/resources/views/livewire/landing-page/post/post-show.blade.php", $postShowView);

            // ─── PAGE SHOW ───
            $pageShowClass = <<<PHP
<?php

namespace {$this->pkgNamespace}\Livewire\LandingPage\Page;

use Bale\Umpak\Livewire\UmpakComponent;
use Livewire\Attributes\Layout;

class PageShow extends UmpakComponent
{
    public string \$slug;

    public function mount(string \$slug): void
    {
        
        \$this->slug = \$slug;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        \$page = \$this->page(\$this->slug);

        if (! \$page) abort(404);

        return view('{$this->pkgSlug}::livewire.landing-page.page.page-show', [
            'page' => \$page
        ])->title(\$page->title . ' — {$orgName}');
    }
}
PHP;
            file_put_contents("{$this->pkgPath}/src/Livewire/LandingPage/Page/PageShow.php", $pageShowClass);

            $pageShowView = <<<BLADE
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <x-umpak::breadcrumb :items="[
        ['label' => 'Beranda', 'url' => '/'],
        ['label' => \$page->title],
    ]" class="mb-8" />

    <h1 class="text-4xl font-bold mb-10">{{ \$page->title }}</h1>

    <div class="prose prose-lg dark:prose-invert max-w-none">
        <x-umpak::editorjs-renderer :content="\$page->content" />
    </div>
</div>
BLADE;
            file_put_contents("{$this->pkgPath}/resources/views/livewire/landing-page/page/page-show.blade.php", $pageShowView);
        });
    }

    private function createRoutes(): void
    {
        $this->components->task('Creating routes (Full-Page Livewire)', function () {
            $stub = <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use {$this->pkgNamespace}\Livewire\LandingPage\Index;
use {$this->pkgNamespace}\Livewire\LandingPage\Post\PostList;
use {$this->pkgNamespace}\Livewire\LandingPage\Post\PostShow;
use {$this->pkgNamespace}\Livewire\LandingPage\Page\PageShow;

// Landing Page Routes
Route::middleware(['web'])->group(function () {
    Route::get('/', Index::class)->name('{$this->pkgSlug}.home');

    Route::prefix('berita')->name('{$this->pkgSlug}.post.')->group(function () {
        Route::get('/', PostList::class)->name('index');
        Route::get('/{slug}', PostShow::class)->name('show');
    });

    Route::get('/{slug}', PageShow::class)->name('{$this->pkgSlug}.page.show');
});
PHP;
            file_put_contents("{$this->pkgPath}/routes/web.php", $stub);
        });
    }

    private function createLayouts(): void
    {
        $this->components->task('Creating layouts', function () {
            $app = <<<BLADE
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    {{-- SEO --}}
    {{-- <x-seo::seo-meta /> --}}

    <title>{{ \$title ?? \$umpakOrg?->organizationName ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-umpak::analytics />
    @livewireStyles
</head>
<body class="antialiased">

    {{-- Navbar --}}
    <nav x-data="umpakNav()" class="sticky top-0 z-50">
        {{-- Markup... --}}
    </nav>

    {{-- Main Content --}}
    <main>
        {{ \$slot }}
    </main>

    {{-- Footer --}}
    <footer>
        {{-- Markup... --}}
    </footer>

    <livewire:umpak.shared-components.floating-contact />

    @livewireScripts
</body>
</html>
BLADE;
            file_put_contents("{$this->pkgPath}/resources/views/layouts/app.blade.php", $app);

            $error = <<<BLADE
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \$title ?? 'Error' }} — {{ \$umpakOrg?->organizationName ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <main>
        {{ \$slot }}
    </main>
</body>
</html>
BLADE;
            file_put_contents("{$this->pkgPath}/resources/views/layouts/error.blade.php", $error);
        });
    }

    private function createErrorPages(): void
    {
        $this->components->task('Creating error pages', function () {
            $errors = [
                '401' => ['Tidak Terautentikasi', 'Silakan login terlebih dahulu untuk mengakses halaman ini.'],
                '403' => ['Akses Ditolak', 'Anda tidak memiliki izin untuk mengakses halaman ini.'],
                '404' => ['Halaman Tidak Ditemukan', 'Halaman yang Anda cari tidak ada atau telah dipindahkan.'],
                '419' => ['Halaman Kedaluwarsa', 'Halaman telah kedaluwarsa karena terlalu lama tidak ada aktivitas. Silakan segarkan halaman.'],
                '429' => ['Terlalu Banyak Permintaan', 'Terlalu banyak permintaan dalam waktu singkat. Silakan coba lagi nanti.'],
                '500' => ['Kesalahan Server', 'Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.'],
                '503' => ['Sedang Dalam Pemeliharaan', 'Sistem sedang dalam pemeliharaan. Silakan kembali nanti.'],
            ];

            foreach ($errors as $code => [$title, $message]) {
                $stub = <<<BLADE
<x-{$this->pkgSlug}::layouts.error>
    <x-slot:title>{$title}</x-slot:title>
    <div class="min-h-screen flex items-center justify-center text-center">
        <div>
            <h1 class="text-9xl font-bold text-gray-200">{$code}</h1>
            <h2 class="text-2xl font-semibold mt-4">{$title}</h2>
            <p class="text-gray-600 mt-2">{$message}</p>
            <a href="{{ url('/') }}" wire:navigate class="mt-6 inline-block text-blue-600 font-medium">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</x-{$this->pkgSlug}::layouts.error>
BLADE;
                file_put_contents("{$this->pkgPath}/resources/views/errors/{$code}.blade.php", $stub);
            }
        });
    }

    private function createCss(): void
    {
        $this->components->task('Creating app.css', function () {
            $stub = "@import 'tailwindcss';\n\n/* Theme customization */\n";
            file_put_contents("{$this->pkgPath}/resources/css/app.css", $stub);
        });
    }

    private function createJs(): void
    {
        $this->components->task('Creating app.js', function () {
            $stub = "import './bootstrap';\nimport '/vendor/umpak/umpak.js';\nimport Alpine from 'alpinejs';\nwindow.Alpine = Alpine;\nAlpine.start();\n";
            file_put_contents("{$this->pkgPath}/resources/js/app.js", $stub);
        });
    }
}

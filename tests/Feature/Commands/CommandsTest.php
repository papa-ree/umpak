<?php

use Illuminate\Support\Facades\File;

describe('umpak:install', function () {

    it('runs successfully', function () {
        $this->artisan('umpak:install')
            ->assertSuccessful();
    });

    it('shows next steps after install', function () {
        $this->artisan('umpak:install')
            ->expectsOutputToContain('UMPAK_')
            ->assertSuccessful();
    });

    it('publishes config file', function () {
        $configPath = config_path('umpak.php');

        if (File::exists($configPath)) {
            File::delete($configPath);
        }

        $this->artisan('umpak:install')->assertSuccessful();

        expect(File::exists($configPath))->toBeTrue();

        File::delete($configPath);
    });

});

describe('umpak:make', function () {

    $packagePath = null;

    beforeEach(function () use (&$packagePath) {
        $packagePath = base_path('packages/bale-test-dinkes');

        if (is_dir($packagePath)) {
            File::deleteDirectory($packagePath);
        }
    });

    afterEach(function () use (&$packagePath) {
        if (is_dir($packagePath)) {
            File::deleteDirectory($packagePath);
        }
    });

    it('runs successfully with name and slug', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertSuccessful();

        expect(is_dir($packagePath))->toBeTrue();
    });

    it('generates composer.json with correct package name', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertSuccessful();

        $composer = json_decode(
            file_get_contents("{$packagePath}/composer.json"),
            true
        );

        expect($composer['name'])->toBe('bale/test-dinkes')
            ->and($composer['require'])->toHaveKey('bale/umpak');
    });

    it('generates ServiceProvider with correct namespace', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertSuccessful();

        $content = file_get_contents("{$packagePath}/src/BaleTestDinkesServiceProvider.php");

        expect($content)
            ->toContain('namespace Bale\TestDinkes')
            ->toContain('class BaleTestDinkesServiceProvider');
    });

    it('generates all required view files', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertSuccessful();

        $expectedFiles = [
            'resources/views/layouts/app.blade.php',
            'resources/views/layouts/error.blade.php',
            'resources/views/errors/404.blade.php',
            'resources/views/errors/500.blade.php',
            'resources/views/errors/503.blade.php',
            'resources/views/pages/home.blade.php',
            'resources/views/pages/post/index.blade.php',
            'resources/views/pages/post/show.blade.php',
            'resources/views/pages/page/show.blade.php',
        ];

        foreach ($expectedFiles as $file) {
            expect(file_exists("{$packagePath}/{$file}"))
                ->toBeTrue("File [{$file}] tidak ditemukan");
        }
    });

    it('generates routes/web.php with correct controller reference', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertSuccessful();

        $routes = file_get_contents("{$packagePath}/routes/web.php");

        expect($routes)
            ->toContain('LandingController')
            ->toContain('test-dinkes.home')
            ->toContain("name('test-dinkes.post.')")
            ->toContain("name('index')");
    });

    it('generates layout with umpak components', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertSuccessful();

        $layout = file_get_contents("{$packagePath}/resources/views/layouts/app.blade.php");

        expect($layout)
            ->toContain('x-umpak::analytics')
            ->toContain('umpakNav()')
            ->toContain('$umpakOrg')
            ->toContain('$umpakNav');
    });

    it('generates home view with section-error fallback', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertSuccessful();

        $home = file_get_contents("{$packagePath}/resources/views/pages/home.blade.php");

        expect($home)
            ->toContain('x-umpak::section-error')
            ->toContain('$banner->meta(')
            ->toContain('$banner->buttons()');
    });

    it('fails gracefully when package already exists without --force', function () use (&$packagePath) {
        mkdir($packagePath, 0755, true);

        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])->assertFailed();
    });

    it('overwrites existing package with --force', function () use (&$packagePath) {
        mkdir($packagePath, 0755, true);

        $this->artisan('umpak:make', [
            'name'    => 'bale-test-dinkes',
            'slug'    => 'test-dinkes',
            '--force' => true,
        ])->assertSuccessful();
    });

    it('shows next steps after scaffold', function () use (&$packagePath) {
        $this->artisan('umpak:make', [
            'name' => 'bale-test-dinkes',
            'slug' => 'test-dinkes',
        ])
            ->expectsOutputToContain('composer.json')
            ->expectsOutputToContain('bootstrap/providers.php')
            ->assertSuccessful();
    });

});

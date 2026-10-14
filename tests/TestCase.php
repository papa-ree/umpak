<?php

namespace Bale\Umpak\Tests;

use Bale\Umpak\UmpakServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            UmpakServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('umpak.cdn.enabled', false);
        config()->set('umpak.cdn.url', 'https://cdn.ponorogo.go.id');
        config()->set('umpak.cdn.prefix', 'bale');

        $this->configureDatabase($app);
    }

    private function configureDatabase($app): void
    {
        $driver = env('DB_CONNECTION', 'sqlite');

        if ($driver === 'mysql') {
            config()->set('database.default', 'mysql');
            config()->set('database.connections.mysql', [
                'driver'    => 'mysql',
                'host'      => env('DB_HOST', '127.0.0.1'),
                'port'      => env('DB_PORT', '3306'),
                'database'  => env('DB_DATABASE', 'bale_umpak_test'),
                'username'  => env('DB_USERNAME', 'root'),
                'password'  => env('DB_PASSWORD', ''),
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix'    => '',
            ]);

            config()->set('umpak.database.connection', 'mysql');
        } else {
            config()->set('database.default', 'testing');
            config()->set('database.connections.testing', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);

            // UmpakModel::getConnectionName() membaca config umpak.database.connection,
            // jadi arahkan ke koneksi 'testing' saat suite berjalan di SQLite.
            config()->set('umpak.database.connection', 'testing');
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTables();
    }

    protected function tearDown(): void
    {
        $this->dropTables();
        parent::tearDown();
    }

    private function createTables(): void
    {
        Schema::create('options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        Schema::create('sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('type')->default('core');
            $table->string('usage')->default('general');
            $table->json('content')->nullable();
            $table->boolean('actived')->default(true);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('author');
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('category_slug')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('slug')->unique();
            $table->string('type')->default('paragraph');
            $table->json('content')->nullable();
            $table->timestamps();
        });

        Schema::create('navigations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url')->nullable();
            $table->boolean('url_mode')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('actived')->default(true);
            $table->uuid('parent_id')->nullable();
            $table->uuid('page_slug')->nullable();
            $table->timestamps();
        });

        Schema::create('agendas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('all_day')->default(false);
            $table->string('timezone')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('rrule')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->json('content')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('published')->default(false);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });
    }

    private function dropTables(): void
    {
        // Urutan penting — foreign key constraint
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('agendas');
        Schema::dropIfExists('navigations');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('users');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sections');
        Schema::dropIfExists('options');
    }
}

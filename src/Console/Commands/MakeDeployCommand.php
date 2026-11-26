<?php

namespace Bale\Umpak\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeDeployCommand extends Command
{
    protected $signature = 'umpak:make-deploy
                            {--app= : Slug aplikasi/image, default dari config app.name}
                            {--domain= : Host Traefik, default ${APP_DOMAIN} dari .env}
                            {--force : Timpa file yang sudah ada}';

    protected $description = 'Generate file deployment: Dockerfile, Caddyfile, entrypoint, compose, dan workflow GHCR';

    private const STUBS = [
        'Dockerfile.stub' => 'Dockerfile',
        'Caddyfile.stub' => 'Caddyfile',
        'docker-entrypoint.stub' => 'docker-entrypoint.sh',
        'dockerignore.stub' => '.dockerignore',
        'compose.stub' => 'docker-compose.yml',
        'deploy.yml.stub' => '.github/workflows/deploy.yml',
    ];

    public function handle(): int
    {
        $app = $this->option('app') ?: config('app.name');
        $slug = Str::slug($app) ?: 'app';
        $name = Str::headline($app) ?: $slug;
        $domain = $this->option('domain') ?: '${APP_DOMAIN}';

        $replacements = [
            '__APP_NAME__' => $name,
            '__APP_SLUG__' => $slug,
            '__APP_DOMAIN__' => $domain,
        ];

        $written = [];
        $skipped = [];

        foreach (self::STUBS as $stub => $target) {
            $path = base_path($target);

            if (File::exists($path) && ! $this->option('force')) {
                $skipped[] = $target;

                continue;
            }

            File::ensureDirectoryExists(dirname($path));
            File::put(
                $path,
                strtr(File::get(__DIR__.'/../../../resources/stubs/deploy/'.$stub), $replacements)
            );
            $written[] = $target;
        }

        $this->components->info('Generating deployment files...');

        foreach ($written as $file) {
            $this->components->twoColumnDetail($file, '<fg=green>created</>');
        }

        foreach ($skipped as $file) {
            $this->components->twoColumnDetail($file, '<fg=yellow>skipped</>');
        }

        $this->newLine();
        $this->line('  Set <fg=yellow>APP_DOMAIN</> dan <fg=yellow>GHCR_OWNER</> di <fg=cyan>.env</> server.');
        $this->line('  Build image: <fg=cyan>docker build -t ghcr.io/$GHCR_OWNER/'.$slug.':latest .</>');

        return self::SUCCESS;
    }
}

<?php

namespace Bale\Umpak\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'umpak:install';

    protected $description = 'Install bale/umpak — publish config dan assets';

    public function handle(): int
    {
        $this->components->info('Installing bale/umpak...');

        $this->publishConfig();
        $this->addEnvVariables();
        $this->registerMiddleware();

        $this->newLine();
        $this->components->info('bale/umpak installed successfully.');
        $this->newLine();

        $this->components->twoColumnDetail(
            '<fg=yellow>Langkah berikutnya</>',
            ''
        );
        $this->line('  1. Isi variabel <fg=yellow>UMPAK_*</> di file <fg=cyan>.env</>');
        $this->line('  2. Pastikan tabel <fg=cyan>options</> memiliki row <fg=yellow>organization_slug</>');
        $this->line('  3. <fg=gray>Multi-tema (opsional):</> Switch landing page: <fg=cyan>php artisan umpak:switch</> <fg=gray>[local only]</>');
        $this->newLine();

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->components->task('Publishing config', function () {
            $this->callSilently('vendor:publish', [
                '--tag' => 'umpak:config',
                '--force' => false,
            ]);
        });
    }

    private function addEnvVariables(): void
    {
        $this->components->task('Adding .env variables', function () {
            $envPath = base_path('.env');

            if (!file_exists($envPath)) {
                return;
            }

            // Membuka file dengan mode read/write dan melakukan locking eksklusif
            $file = fopen($envPath, 'r+');
            if ($file && flock($file, LOCK_EX)) {
                $existing = stream_get_contents($file);

                $variables = [
                    'UMPAK_CDN_ENABLED' => 'false',
                    'UMPAK_CDN_URL' => '',
                    'UMPAK_CDN_PREFIX' => 'bale',
                    'UMPAK_BALYSTICS_ID' => '',
                    'ACTIVE_LANDING_PAGE' => '',
                ];

                $toAppend = '';

                foreach ($variables as $key => $value) {
                    if (!str_contains($existing, $key)) {
                        $toAppend .= "\n{$key}={$value}";
                    }
                }

                if ($toAppend !== '') {
                    rewind($file);
                    ftruncate($file, 0);
                    fwrite($file, $existing . "\n# bale/umpak" . $toAppend . "\n");
                }

                flock($file, LOCK_UN);
                fclose($file);
            }
        });
    }

    private function registerMiddleware(): void
    {
        $this->components->task('Registering SecurityHeaders middleware', function () {
            $bootstrapPath = base_path('bootstrap/app.php');

            if (!file_exists($bootstrapPath)) {
                return false;
            }

            $content = file_get_contents($bootstrapPath);

            // Jika sudah terdaftar, lewati
            if (str_contains($content, 'SecurityHeaders::class')) {
                return true;
            }

            // Pola untuk mencocokkan ->withMiddleware(...)
            $pattern = '/(->withMiddleware\s*\(\s*function\s*\(\s*Middleware\s*\$middleware\s*\)(?:\s*:\s*void)?\s*\{)/i';

            if (preg_match($pattern, $content)) {
                $newContent = preg_replace($pattern, "$1\n        \$middleware->append(\\Bale\\Umpak\\Http\\Middleware\\SecurityHeaders::class);", $content);
                file_put_contents($bootstrapPath, $newContent);
                return true;
            }

            return false;
        });
    }
}

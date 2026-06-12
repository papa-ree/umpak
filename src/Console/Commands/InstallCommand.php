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
        $this->publishAssets();
        $this->addEnvVariables();

        $this->newLine();
        $this->components->info('bale/umpak installed successfully.');
        $this->newLine();

        $this->components->twoColumnDetail(
            '<fg=yellow>Langkah berikutnya</>',
            ''
        );
        $this->line('  1. Isi variabel <fg=yellow>UMPAK_*</> di file <fg=cyan>.env</>');
        $this->line('  2. Pastikan tabel <fg=cyan>options</> memiliki row <fg=yellow>organization_slug</>');
        $this->line('  3. Publish Alpine JS: <fg=cyan>php artisan vendor:publish --tag=umpak:assets</>');
        $this->line('  4. Import di app.js: <fg=cyan>import \'/vendor/umpak/umpak.js\'</>');
        $this->line('  5. <fg=gray>Multi-tema (opsional):</> Switch landing page: <fg=cyan>php artisan umpak:switch</> <fg=gray>[local only]</>');
        $this->newLine();

        return self::SUCCESS;
    }

    private function publishConfig(): void
    {
        $this->components->task('Publishing config', function () {
            $this->callSilently('vendor:publish', [
                '--tag'   => 'umpak:config',
                '--force' => false,
            ]);
        });
    }

    private function publishAssets(): void
    {
        $this->components->task('Publishing Alpine JS assets', function () {
            $this->callSilently('vendor:publish', [
                '--tag'   => 'umpak:assets',
                '--force' => false,
            ]);
        });
    }

    private function addEnvVariables(): void
    {
        $this->components->task('Adding .env variables', function () {
            $envPath = base_path('.env');

            if (! file_exists($envPath)) {
                return;
            }

            $existing = file_get_contents($envPath);

            $variables = [
                'UMPAK_CDN_ENABLED'   => 'false',
                'UMPAK_CDN_URL'       => '',
                'UMPAK_CDN_PREFIX'    => 'bale',
                'UMPAK_BALYSTICS_ID'  => '',
                'ACTIVE_LANDING_PAGE' => '',
            ];

            $toAppend = '';

            foreach ($variables as $key => $value) {
                if (! str_contains($existing, $key)) {
                    $toAppend .= "\n{$key}={$value}";
                }
            }

            if ($toAppend !== '') {
                file_put_contents(
                    $envPath,
                    $existing."\n# bale/umpak".$toAppend."\n"
                );
            }
        });
    }
}

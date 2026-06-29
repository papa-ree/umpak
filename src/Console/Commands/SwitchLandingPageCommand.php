<?php

namespace Bale\Umpak\Console\Commands;

use Bale\Umpak\Umpak;
use Illuminate\Console\Command;

/**
 * Command interaktif untuk switch landing page aktif di environment local.
 *
 * Signature : php artisan umpak:switch
 * Guard     : hanya berjalan di APP_ENV=local
 * Cara kerja: menampilkan daftar landing page dalam bentuk radio, lalu
 *             memperbarui ACTIVE_LANDING_PAGE di .env dan automatic clear cache.
 */
class SwitchLandingPageCommand extends Command
{
    protected $signature = 'umpak:switch';

    protected $description = 'Switch active landing page [local only]';

    public function handle(): int
    {
        // ── Guard: local only ─────────────────────────────────────────────────
        if (! app()->environment('local')) {
            $this->components->error('umpak:switch hanya dapat dijalankan di environment <fg=white>local</>.');
            $this->newLine();
            $this->line('  <fg=yellow>Di production/staging, set variabel environment:</>');
            $this->line('  <fg=cyan>ACTIVE_LANDING_PAGE=slug-landing-page</>');
            $this->newLine();

            return self::FAILURE;
        }

        // ── Ambil daftar landing page ─────────────────────────────────────────
        $pages   = app(Umpak::class)->landingPages();
        $current = config('umpak.landing_page.active', '');

        if (empty($pages)) {
            $this->components->error('Tidak ada landing page terdaftar.');
            $this->newLine();
            $this->line('  Daftarkan theme package via <fg=cyan>registerLandingPage()</> di ServiceProvider-nya.');
            $this->line('  Atau tambahkan ke <fg=cyan>config/umpak.php</> → <fg=white>landing_page.pages</>.');
            $this->newLine();

            return self::FAILURE;
        }

        $slugs = array_keys($pages);

        // ── Tampilan header ───────────────────────────────────────────────────
        $this->newLine();
        $this->line('  <fg=cyan;options=bold>  Bale Umpak — Switch Landing Page  </>');
        $this->newLine();

        if ($current) {
            $currentName = $pages[$current]['name'] ?? $current;
            $this->line("  <fg=gray>Aktif saat ini:</> <fg=green;options=bold>{$current}</> <fg=gray>({$currentName})</>");
        } else {
            $this->line('  <fg=gray>Aktif saat ini:</> <fg=yellow>tidak ada (single-theme mode)</>');
        }

        $this->newLine();
        $this->line('  <fg=gray>Pilih landing page:</>');
        $this->newLine();

        // ── Render radio-button list ──────────────────────────────────────────
        foreach ($slugs as $i => $slug) {
            $info     = $pages[$slug];
            $name     = $info['name'] ?? $slug;
            $isActive = $slug === $current;
            $num      = str_pad((string) ($i + 1), 2, ' ', STR_PAD_LEFT);

            if ($isActive) {
                $radio   = '<fg=green>◉</>';
                $slugFmt = "<fg=green;options=bold>{$slug}</>";
                $nameFmt = "<fg=white>{$name}</> <fg=green>← aktif</>";
            } else {
                $radio   = '<fg=gray>○</>';
                $slugFmt = "<fg=cyan>{$slug}</>";
                $nameFmt = "<fg=gray>{$name}</>";
            }

            $this->line("   {$radio} {$num}.  {$slugFmt}  —  {$nameFmt}");
        }

        $this->newLine();

        // ── Prompt ────────────────────────────────────────────────────────────
        $default = $current ?: $slugs[0];
        $raw     = $this->ask(
            "  Pilih <fg=gray>nomor</> atau <fg=gray>slug</> <fg=gray>[{$default}]</>",
            $default
        );

        $selected = $this->resolveInput((string) $raw, $slugs);

        // ── Validasi ──────────────────────────────────────────────────────────
        if ($selected === null) {
            $this->newLine();
            $this->components->error("Pilihan tidak valid: \"{$raw}\"");
            $this->line('  Masukkan nomor (1-'.count($slugs).') atau slug yang tersedia.');
            $this->newLine();

            return self::FAILURE;
        }

        // ── Tidak ada perubahan ───────────────────────────────────────────────
        if ($selected === $current) {
            $this->newLine();
            $this->components->warn("<fg=white>{$selected}</> sudah menjadi landing page aktif. Tidak ada perubahan.");
            $this->newLine();

            return self::SUCCESS;
        }

        // ── Tulis ke .env & clear cache ───────────────────────────────────────
        $this->writeToEnv($selected);
        $this->callSilently('config:clear');

        $this->newLine();
        $selectedName = $pages[$selected]['name'] ?? $selected;
        $this->components->info(
            "Switched → <fg=cyan;options=bold>{$selected}</> <fg=gray>({$selectedName})</>"
        );
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Resolve input user: bisa berupa nomor (1-based) atau slug langsung.
     */
    private function resolveInput(string $input, array $slugs): ?string
    {
        // Exactmatch slug
        if (in_array($input, $slugs, strict: true)) {
            return $input;
        }

        // Nomor (1-based)
        if (ctype_digit($input)) {
            $index = (int) $input - 1;

            return $slugs[$index] ?? null;
        }

        return null;
    }

    /**
     * Perbarui atau tambahkan ACTIVE_LANDING_PAGE di .env.
     */
    private function writeToEnv(string $slug): void
    {
        $envPath = base_path('.env');

        if (! file_exists($envPath)) {
            $this->components->warn('.env tidak ditemukan — set ACTIVE_LANDING_PAGE secara manual.');

            return;
        }

        // Membuat backup copy berkas .env sebelum modikasi
        try {
            $backupPath = $envPath . '.bak.' . date('YmdHis');
            copy($envPath, $backupPath);
        } catch (\Throwable) {
            // Abaikan jika backup gagal agar command tetap bisa memproses .env utama
        }

        // Membuka file untuk read/write secara eksklusif (flock)
        $file = fopen($envPath, 'r+');
        if ($file && flock($file, LOCK_EX)) {
            $content = stream_get_contents($file);

            if (preg_match('/^ACTIVE_LANDING_PAGE=.*$/m', $content)) {
                $content = preg_replace(
                    '/^ACTIVE_LANDING_PAGE=.*$/m',
                    "ACTIVE_LANDING_PAGE={$slug}",
                    $content
                );
            } else {
                $content .= "\nACTIVE_LANDING_PAGE={$slug}\n";
            }

            rewind($file);
            ftruncate($file, 0);
            fwrite($file, $content);

            flock($file, LOCK_UN);
            fclose($file);
        }
    }
}

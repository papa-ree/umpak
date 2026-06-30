<?php

namespace Bale\Umpak\Livewire;

use Bale\Umpak\Contracts\PageRepositoryInterface;
use Bale\Umpak\Contracts\PostRepositoryInterface;
use Bale\Umpak\Contracts\SectionRepositoryInterface;
use Bale\Umpak\DTOs\PageData;
use Bale\Umpak\DTOs\PostData;
use Bale\Umpak\DTOs\SectionData;
use Livewire\Component;

/**
 * Abstract base class untuk semua Livewire component di landing page package.
 *
 * Menyediakan:
 * - $orgSlug yang sudah terisi otomatis dari config
 * - Helper methods untuk akses data via repositories
 *
 * Penggunaan di landing page package:
 *
 *   class BeritaTerbaru extends UmpakComponent
 *   {
 *       public function render()
 *       {
 *           return view('bale-dinkes::livewire.berita-terbaru', [
 *               'posts' => $this->latestPosts(6),
 *           ]);
 *       }
 *   }
 */
abstract class UmpakComponent extends Component
{
    public string $orgSlug;

    public function boot(): void
    {
        $this->orgSlug = config('umpak.org_slug', '');
    }

    /**
     * Ambil section berdasarkan slug.
     * Kembalikan null jika tidak ditemukan — view handle via <x-umpak::section-error>.
     */
    protected function section(string $slug): ?SectionData
    {
        return app(SectionRepositoryInterface::class)->findBySlug($slug);
    }

    /**
     * Ambil post terbaru milik org aktif.
     *
     * @return \Illuminate\Support\Collection<int, PostData>
     */
    protected function latestPosts(int $limit = 10): \Illuminate\Support\Collection
    {
        return app(PostRepositoryInterface::class)->latest($limit);
    }

    /**
     * Ambil post berdasarkan slug.
     * Kembalikan null jika tidak ditemukan.
     */
    protected function post(string $slug): ?PostData
    {
        return app(PostRepositoryInterface::class)->findBySlug($slug);
    }

    /**
     * Ambil page berdasarkan slug.
     * Kembalikan null jika tidak ditemukan.
     */
    protected function page(string $slug): ?PageData
    {
        return app(PageRepositoryInterface::class)->findBySlug($slug);
    }

    /**
     * Cari post berdasarkan keyword dan filter tanggal.
     *
     * @return \Illuminate\Support\Collection<int, PostData>
     */
    protected function searchPosts(int $limit = 10, string $keyword = '', string $date = ''): \Illuminate\Support\Collection
    {
        return app(PostRepositoryInterface::class)->search($limit, $keyword, $date);
    }

    /**
     * Hitung total post berdasarkan filter pencarian.
     */
    protected function countSearchPosts(string $keyword = '', string $date = ''): int
    {
        return app(PostRepositoryInterface::class)->countSearch($keyword, $date);
    }
    /**
     * Ambil post secara random untuk rujukan/saran.
     *
     * @return \Illuminate\Support\Collection<int, PostData>
     */
    protected function getRandomPosts(int $limit = 5): \Illuminate\Support\Collection
    {
        return app(PostRepositoryInterface::class)->suggested($limit);
    }

    /**
     * Menghalangi/memblokir skema URL berbahaya (seperti javascript:, data:, vbscript:)
     * dari input URL dinamis CMS.
     */
    protected static function safeUrl(string $rawUrl): string
    {
        $rawUrl = trim($rawUrl);

        if ($rawUrl === '' || $rawUrl === '#') {
            return '#';
        }

        // Block dangerous URI schemes
        if (preg_match('/^\s*(javascript|data|vbscript)\s*:/i', $rawUrl)) {
            return '#';
        }

        return str_starts_with($rawUrl, 'http') ? $rawUrl : url($rawUrl);
    }
}

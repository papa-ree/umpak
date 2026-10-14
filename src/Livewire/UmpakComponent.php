<?php

namespace Bale\Umpak\Livewire;

use Bale\Umpak\Contracts\AgendaRepositoryInterface;
use Bale\Umpak\Contracts\AnnouncementRepositoryInterface;
use Bale\Umpak\Contracts\PageRepositoryInterface;
use Bale\Umpak\Contracts\PostRepositoryInterface;
use Bale\Umpak\Contracts\SectionRepositoryInterface;
use Bale\Umpak\DTOs\AgendaData;
use Bale\Umpak\DTOs\AnnouncementData;
use Bale\Umpak\DTOs\PageData;
use Bale\Umpak\DTOs\PostData;
use Bale\Umpak\DTOs\SectionData;
use Illuminate\Support\Collection;
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
     * @return Collection<int, PostData>
     */
    protected function latestPosts(int $limit = 10): Collection
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
     * @return Collection<int, PostData>
     */
    protected function searchPosts(int $limit = 10, string $keyword = '', string $date = ''): Collection
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
     * @return Collection<int, PostData>
     */
    protected function getRandomPosts(int $limit = 5): Collection
    {
        return app(PostRepositoryInterface::class)->suggested($limit);
    }

    /**
     * Ambil agenda terbaru milik org aktif.
     *
     * @return Collection<int, AgendaData>
     */
    protected function latestAgendas(int $limit = 10): Collection
    {
        return app(AgendaRepositoryInterface::class)->latest($limit);
    }

    /**
     * Ambil agenda yang akan datang.
     *
     * @return Collection<int, AgendaData>
     */
    protected function upcomingAgendas(int $limit = 10): Collection
    {
        return app(AgendaRepositoryInterface::class)->upcoming($limit);
    }

    /**
     * Ambil agenda berdasarkan id.
     * Kembalikan null jika tidak ditemukan.
     */
    protected function agenda(string $id): ?AgendaData
    {
        return app(AgendaRepositoryInterface::class)->findById($id);
    }

    /**
     * Ambil pengumuman terbaru milik org aktif.
     *
     * @return Collection<int, AnnouncementData>
     */
    protected function latestAnnouncements(int $limit = 10): Collection
    {
        return app(AnnouncementRepositoryInterface::class)->latest($limit);
    }

    /**
     * Ambil pengumuman penting (pinned).
     *
     * @return Collection<int, AnnouncementData>
     */
    protected function pinnedAnnouncements(int $limit = 5): Collection
    {
        return app(AnnouncementRepositoryInterface::class)->pinned($limit);
    }

    /**
     * Ambil pengumuman berdasarkan slug.
     * Kembalikan null jika tidak ditemukan.
     */
    protected function announcement(string $slug): ?AnnouncementData
    {
        return app(AnnouncementRepositoryInterface::class)->findBySlug($slug);
    }

    /**
     * Cari pengumuman berdasarkan keyword dan filter tanggal.
     *
     * @return Collection<int, AnnouncementData>
     */
    protected function searchAnnouncements(int $limit = 10, string $keyword = '', string $date = ''): Collection
    {
        return app(AnnouncementRepositoryInterface::class)->search($limit, $keyword, $date);
    }

    /**
     * Hitung total pengumuman berdasarkan filter pencarian.
     */
    protected function countSearchAnnouncements(string $keyword = '', string $date = ''): int
    {
        return app(AnnouncementRepositoryInterface::class)->countSearch($keyword, $date);
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

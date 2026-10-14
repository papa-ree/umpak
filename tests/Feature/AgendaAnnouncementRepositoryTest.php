<?php

use Bale\Umpak\Contracts\AgendaRepositoryInterface;
use Bale\Umpak\Contracts\AnnouncementRepositoryInterface;
use Bale\Umpak\Models\Agenda;
use Bale\Umpak\Models\Announcement;
use Carbon\Carbon;

describe('AgendaRepository — round trip', function () {

    it('returns visible upcoming agendas ordered by start_date ascending', function () {
        $soon = agendaRecord(['id' => 'a-soon', 'title' => 'Rapat Besok', 'start_date' => now()->addDay()]);
        $later = agendaRecord(['id' => 'a-later', 'title' => 'Rapat Lusa', 'start_date' => now()->addDays(2)]);
        agendaRecord(['id' => 'a-past', 'title' => 'Sudah Lewat', 'start_date' => now()->subDay()]);
        agendaRecord(['id' => 'a-hidden', 'title' => 'Tersembunyi', 'start_date' => now()->addDay(), 'is_visible' => false]);

        $titles = app(AgendaRepositoryInterface::class)->upcoming()->pluck('title');

        expect($titles->all())->toBe([$soon->title, $later->title]);
    });

    it('returns latest agendas ordered by start_date descending', function () {
        agendaRecord(['id' => 'a-soon', 'title' => 'Rapat Besok', 'start_date' => now()->addDay()]);
        agendaRecord(['id' => 'a-later', 'title' => 'Rapat Lusa', 'start_date' => now()->addDays(2)]);

        $titles = app(AgendaRepositoryInterface::class)->latest()->pluck('title');

        expect($titles->all())->toBe(['Rapat Lusa', 'Rapat Besok']);
    });

    it('maps a record to AgendaData with casts applied', function () {
        agendaRecord([
            'id' => 'a-1',
            'title' => 'Upacara',
            'location' => 'Alun-Alun',
            'all_day' => true,
        ]);

        $data = app(AgendaRepositoryInterface::class)->findById('a-1');

        expect($data)->not->toBeNull()
            ->and($data->id)->toBe('a-1')
            ->and($data->title)->toBe('Upacara')
            ->and($data->location)->toBe('Alun-Alun')
            ->and($data->allDay)->toBeTrue()
            ->and($data->isVisible)->toBeTrue()
            ->and($data->startDate)->toBeInstanceOf(Carbon::class);
    });

    it('hides agendas flagged as not visible', function () {
        agendaRecord(['id' => 'a-hidden', 'is_visible' => false, 'start_date' => now()->addDay()]);

        expect(app(AgendaRepositoryInterface::class)->findById('a-hidden'))->toBeNull();
    });

});

describe('AnnouncementRepository — round trip', function () {

    it('only returns published announcements inside the publication window', function () {
        announcementRecord(['id' => 'n-active', 'slug' => 'aktif', 'published' => true]);
        announcementRecord(['id' => 'n-draft', 'slug' => 'draft', 'published' => false]);
        announcementRecord(['id' => 'n-future', 'slug' => 'besok', 'published' => true, 'start_date' => now()->addDay()]);
        announcementRecord(['id' => 'n-expired', 'slug' => 'kedaluwarsa', 'published' => true, 'end_date' => now()->subDay()]);

        $slugs = app(AnnouncementRepositoryInterface::class)->latest()->pluck('slug');

        expect($slugs->all())->toBe(['aktif']);
    });

    it('returns an announcement by slug with full attachment url', function () {
        announcementRecord([
            'id' => 'n-1',
            'slug' => 'pengumuman-penting',
            'title' => 'Pengumuman Penting',
            'attachment' => 'surat.pdf',
        ]);

        $data = app(AnnouncementRepositoryInterface::class)->findBySlug('pengumuman-penting');

        expect($data)->not->toBeNull()
            ->and($data->title)->toBe('Pengumuman Penting')
            ->and($data->hasAttachment())->toBeTrue()
            ->and($data->attachment)->toContain('attachments/surat.pdf');
    });

    it('returns only pinned announcements from pinned()', function () {
        announcementRecord(['id' => 'n-pin', 'slug' => 'pinned', 'is_pinned' => true]);
        announcementRecord(['id' => 'n-norm', 'slug' => 'normal']);

        $slugs = app(AnnouncementRepositoryInterface::class)->pinned()->pluck('slug');

        expect($slugs->all())->toBe(['pinned']);
    });

    it('orders latest() with pinned announcements first', function () {
        announcementRecord(['id' => 'n-old', 'slug' => 'lama', 'start_date' => now()->subDays(5)]);
        announcementRecord(['id' => 'n-new', 'slug' => 'baru', 'start_date' => now()]);
        announcementRecord(['id' => 'n-pin', 'slug' => 'pinned', 'is_pinned' => true, 'start_date' => now()->subDays(10)]);

        $slugs = app(AnnouncementRepositoryInterface::class)->latest()->pluck('slug');

        expect($slugs->all())->toBe(['pinned', 'baru', 'lama']);
    });

    it('stores EditorJS content as an array', function () {
        announcementRecord([
            'id' => 'n-2',
            'slug' => 'berita',
            'content' => ['blocks' => [['type' => 'paragraph', 'data' => ['text' => 'Halo dunia']]]],
        ]);

        $data = app(AnnouncementRepositoryInterface::class)->findBySlug('berita');

        expect($data->hasContent())->toBeTrue()
            ->and($data->excerpt)->toContain('Halo dunia');
    });

});

function agendaRecord(array $attributes): Agenda
{
    $attributes = array_merge([
        'id' => 'agenda-'.uniqid(),
        'title' => 'Agenda',
        'start_date' => now(),
        'is_visible' => true,
    ], $attributes);

    return tap(new Agenda, function (Agenda $agenda) use ($attributes) {
        $agenda->forceFill($attributes)->save();
    });
}

function announcementRecord(array $attributes): Announcement
{
    $attributes = array_merge([
        'id' => 'ann-'.uniqid(),
        'title' => 'Pengumuman',
        'slug' => 'pengumuman-'.uniqid(),
        'published' => true,
        'is_pinned' => false,
    ], $attributes);

    return tap(new Announcement, function (Announcement $announcement) use ($attributes) {
        $announcement->forceFill($attributes)->save();
    });
}

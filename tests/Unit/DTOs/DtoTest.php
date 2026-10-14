<?php

use Bale\Umpak\DTOs\AgendaData;
use Bale\Umpak\DTOs\AnnouncementData;
use Bale\Umpak\DTOs\PageData;
use Bale\Umpak\DTOs\PostData;
use Bale\Umpak\DTOs\SectionData;
use Carbon\Carbon;

describe('SectionData', function () {

    it('can be constructed and accessed', function () {
        $dto = new SectionData(
            id: '1',
            name: 'Hero Section',
            slug: 'hero',
            type: 'banner',
            usage: 'general',
            actived: true,
            meta: ['title' => 'Selamat Datang', 'subtitle' => 'Sub'],
            items: [['type' => 'text', 'value' => 'Item 1']],
        );

        expect($dto->id)->toBe('1')
            ->and($dto->slug)->toBe('hero')
            ->and($dto->name)->toBe('Hero Section');
    });

    it('meta() returns correct value by key', function () {
        $dto = new SectionData('1', 'Name', 'hero', 'type', 'usage', true, ['title' => 'Judul'], []);

        expect($dto->meta('title'))->toBe('Judul');
    });

    it('meta() returns default when key does not exist', function () {
        $dto = new SectionData('1', 'Name', 'hero', 'type', 'usage', true, [], []);

        expect($dto->meta('tidak_ada', 'fallback'))->toBe('fallback');
    });

    it('hasItems() returns true when items exist', function () {
        $dto = new SectionData('1', 'Name', 'hero', 'type', 'usage', true, [], [['item' => 1]]);

        expect($dto->hasItems())->toBeTrue();
    });

    it('hasItems() returns false when items is empty', function () {
        $dto = new SectionData('1', 'Name', 'hero', 'type', 'usage', true, [], []);

        expect($dto->hasItems())->toBeFalse();
    });

    it('is immutable — properties cannot be changed', function () {
        $dto = new SectionData('1', 'Name', 'hero', 'type', 'usage', true, [], []);

        expect(fn () => $dto->slug = 'changed')
            ->toThrow(Error::class);
    });

});

describe('PostData', function () {

    function makePostData(array $overrides = []): PostData
    {
        $defaults = [
            'id' => '1',
            'slug' => 'berita-test',
            'title' => 'Berita Test',
            'excerpt' => 'Ringkasan berita',
            'content' => ['blocks' => [['type' => 'paragraph']]],
            'thumbnail' => 'https://cdn.example.com/img.jpg',
            'categorySlug' => 'umum',
            'publishedAt' => Carbon::parse('2025-05-01'),
            'updatedAt' => Carbon::parse('2025-05-02'),
        ];

        $data = array_merge($defaults, $overrides);

        return new PostData(
            id: $data['id'],
            slug: $data['slug'],
            title: $data['title'],
            excerpt: $data['excerpt'],
            content: $data['content'],
            thumbnail: $data['thumbnail'],
            categorySlug: $data['categorySlug'],
            publishedAt: $data['publishedAt'],
            updatedAt: $data['updatedAt'],
        );
    }

    it('can be constructed and accessed', function () {
        $dto = makePostData();

        expect($dto->title)->toBe('Berita Test')
            ->and($dto->slug)->toBe('berita-test');
    });

    it('hasThumbnail() returns true when thumbnail set', function () {
        $dto = makePostData(['thumbnail' => 'https://cdn.example.com/img.jpg']);

        expect($dto->hasThumbnail())->toBeTrue();
    });

    it('hasThumbnail() returns false when thumbnail is null', function () {
        $dto = makePostData(['thumbnail' => null]);

        expect($dto->hasThumbnail())->toBeFalse();
    });

    it('hasContent() returns true when blocks exist', function () {
        $dto = makePostData(['content' => ['blocks' => [['type' => 'paragraph']]]]);

        expect($dto->hasContent())->toBeTrue();
    });

    it('hasContent() returns false when content is null', function () {
        $dto = makePostData(['content' => null]);

        expect($dto->hasContent())->toBeFalse();
    });

    it('formattedDate() returns localized date string', function () {
        $dto = makePostData(['publishedAt' => Carbon::parse('2025-05-01')]);

        expect($dto->formattedDate('Y-m-d'))->toBe('2025-05-01');
    });

});

describe('PageData', function () {

    it('can be constructed and accessed', function () {
        $dto = new PageData(
            id: '1',
            slug: 'tentang-kami',
            title: 'Tentang Kami',
            type: 'paragraph',
            content: null,
            updatedAt: Carbon::now(),
        );

        expect($dto->slug)->toBe('tentang-kami');
    });

    it('hasContent() returns false when content is null', function () {
        $dto = new PageData('1', 'slug', 'Title', 'paragraph', null, Carbon::now());

        expect($dto->hasContent())->toBeFalse();
    });

});

describe('AgendaData', function () {

    function makeAgendaData(array $overrides = []): AgendaData
    {
        $defaults = [
            'id' => '1',
            'title' => 'Rapat Koordinasi',
            'description' => 'Deskripsi',
            'location' => 'Aula',
            'startDate' => Carbon::parse('2025-05-12 09:00:00'),
            'endDate' => Carbon::parse('2025-05-12 11:00:00'),
            'category' => 'rapat',
            'isVisible' => true,
            'allDay' => false,
            'timezone' => 'Asia/Jakarta',
            'isRecurring' => false,
            'rrule' => null,
        ];

        $data = array_merge($defaults, $overrides);

        return new AgendaData(
            id: $data['id'],
            title: $data['title'],
            description: $data['description'],
            location: $data['location'],
            startDate: $data['startDate'],
            endDate: $data['endDate'],
            category: $data['category'],
            isVisible: $data['isVisible'],
            allDay: $data['allDay'],
            timezone: $data['timezone'],
            isRecurring: $data['isRecurring'],
            rrule: $data['rrule'],
            createdAt: Carbon::parse('2025-05-01'),
            updatedAt: Carbon::parse('2025-05-02'),
        );
    }

    it('can be constructed and accessed', function () {
        $dto = makeAgendaData();

        expect($dto->title)->toBe('Rapat Koordinasi')
            ->and($dto->category)->toBe('rapat');
    });

    it('formattedDate() returns localized date string', function () {
        $dto = makeAgendaData(['startDate' => Carbon::parse('2025-05-12 09:00:00')]);

        expect($dto->formattedDate('Y-m-d'))->toBe('2025-05-12');
    });

    it('formattedStartTime() is empty when all_day', function () {
        $dto = makeAgendaData(['allDay' => true]);

        expect($dto->formattedStartTime())->toBe('');
    });

    it('isMultiDay() returns true for different days', function () {
        $dto = makeAgendaData([
            'startDate' => Carbon::parse('2025-05-12 09:00:00'),
            'endDate' => Carbon::parse('2025-05-14 15:00:00'),
        ]);

        expect($dto->isMultiDay())->toBeTrue();
    });

    it('isMultiDay() returns false for same day', function () {
        $dto = makeAgendaData();

        expect($dto->isMultiDay())->toBeFalse();
    });

    it('hasEnded() returns true for past agenda', function () {
        $dto = makeAgendaData([
            'startDate' => Carbon::parse('2020-01-01 09:00:00'),
            'endDate' => Carbon::parse('2020-01-01 11:00:00'),
        ]);

        expect($dto->hasEnded())->toBeTrue();
    });

});

describe('AnnouncementData', function () {

    function makeAnnouncementData(array $overrides = []): AnnouncementData
    {
        $defaults = [
            'id' => '1',
            'slug' => 'pengumuman-test',
            'title' => 'Pengumuman Test',
            'excerpt' => 'Ringkasan pengumuman',
            'content' => ['blocks' => [['type' => 'paragraph']]],
            'attachment' => 'https://cdn.example.com/attachments/doc.pdf',
            'isPinned' => false,
            'published' => true,
            'startDate' => Carbon::parse('2025-05-01'),
            'endDate' => null,
        ];

        $data = array_merge($defaults, $overrides);

        return new AnnouncementData(
            id: $data['id'],
            slug: $data['slug'],
            title: $data['title'],
            excerpt: $data['excerpt'],
            content: $data['content'],
            attachment: $data['attachment'],
            isPinned: $data['isPinned'],
            published: $data['published'],
            startDate: $data['startDate'],
            endDate: $data['endDate'],
            createdAt: Carbon::parse('2025-05-01'),
            updatedAt: Carbon::parse('2025-05-02'),
        );
    }

    it('can be constructed and accessed', function () {
        $dto = makeAnnouncementData();

        expect($dto->title)->toBe('Pengumuman Test')
            ->and($dto->slug)->toBe('pengumuman-test');
    });

    it('hasContent() returns true when blocks exist', function () {
        $dto = makeAnnouncementData(['content' => ['blocks' => [['type' => 'paragraph']]]]);

        expect($dto->hasContent())->toBeTrue();
    });

    it('hasContent() returns false when content is null', function () {
        $dto = makeAnnouncementData(['content' => null]);

        expect($dto->hasContent())->toBeFalse();
    });

    it('hasAttachment() returns true when attachment set', function () {
        $dto = makeAnnouncementData();

        expect($dto->hasAttachment())->toBeTrue();
    });

    it('hasAttachment() returns false when attachment is null', function () {
        $dto = makeAnnouncementData(['attachment' => null]);

        expect($dto->hasAttachment())->toBeFalse();
    });

    it('isActive() returns true when published within window', function () {
        $dto = makeAnnouncementData([
            'published' => true,
            'startDate' => Carbon::now()->subDay(),
            'endDate' => Carbon::now()->addDay(),
        ]);

        expect($dto->isActive())->toBeTrue();
    });

    it('isActive() returns false when unpublished', function () {
        $dto = makeAnnouncementData(['published' => false]);

        expect($dto->isActive())->toBeFalse();
    });

    it('isActive() returns false when past end date', function () {
        $dto = makeAnnouncementData([
            'published' => true,
            'startDate' => Carbon::parse('2020-01-01'),
            'endDate' => Carbon::parse('2020-02-01'),
        ]);

        expect($dto->isActive())->toBeFalse();
    });

});

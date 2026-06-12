<?php

use Bale\Umpak\DTOs\PageData;
use Bale\Umpak\DTOs\PostData;
use Bale\Umpak\DTOs\SectionData;

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
            'publishedAt' => \Carbon\Carbon::parse('2025-05-01'),
            'updatedAt' => \Carbon\Carbon::parse('2025-05-02'),
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
        $dto = makePostData(['publishedAt' => \Carbon\Carbon::parse('2025-05-01')]);

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
            updatedAt: \Carbon\Carbon::now(),
        );

        expect($dto->slug)->toBe('tentang-kami');
    });

    it('hasContent() returns false when content is null', function () {
        $dto = new PageData('1', 'slug', 'Title', 'paragraph', null, \Carbon\Carbon::now());

        expect($dto->hasContent())->toBeFalse();
    });

});

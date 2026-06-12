<?php

describe('Blade components', function () {

    it('renders analytics — empty when balystics_id not set', function () {
        config()->set('umpak.balystics_id', '');

        $html = $this->blade('<x-umpak::analytics />');

        expect(trim((string) $html))->toBe('');
    });

    it('renders analytics — injects script when balystics_id is set', function () {
        config()->set('umpak.balystics_id', 'test-id-123');

        $html = $this->blade('<x-umpak::analytics />');

        expect((string) $html)
            ->toContain('balystics.ponorogo.go.id/script.js')
            ->toContain('test-id-123');
    });

    it('renders section-error with default props', function () {
        $html = $this->blade('<x-umpak::section-error />');

        expect((string) $html)
            ->toContain('Konten Tidak Ditemukan')
            ->toContain('panel admin');
    });

    it('renders section-error with custom props', function () {
        $html = $this->blade(
            '<x-umpak::section-error title="Custom Title" message="Custom message." />'
        );

        expect((string) $html)
            ->toContain('Custom Title')
            ->toContain('Custom message.');
    });

    it('renders section-error with neutral slate colors — no primary variable', function () {
        $html = $this->blade('<x-umpak::section-error />');

        expect((string) $html)
            ->not->toContain('bg-primary')
            ->not->toContain('text-primary')
            ->toContain('bg-slate-')
            ->toContain('text-slate-');
    });

    it('renders breadcrumb — empty when no items', function () {
        $html = $this->blade('<x-umpak::breadcrumb :items="$items" />', ['items' => []]);

        expect(trim((string) $html))->toBe('');
    });

    it('renders breadcrumb with items', function () {
        $items = [
            ['label' => 'Beranda', 'url' => '/'],
            ['label' => 'Berita', 'url' => '/berita'],
            ['label' => 'Judul Berita'],
        ];

        $html = $this->blade(
            '<x-umpak::breadcrumb :items="$items" />',
            ['items' => $items]
        );

        expect((string) $html)
            ->toContain('Beranda')
            ->toContain('Berita')
            ->toContain('Judul Berita')
            ->toContain('BreadcrumbList')
            ->toContain('aria-current="page"');
    });

    it('renders share-button with required props', function () {
        $html = $this->blade(
            '<x-umpak::share-button url="https://example.com" title="Judul" />'
        );

        expect((string) $html)
            ->toContain('Cetak')
            ->toContain('Bagikan')
            ->toContain('print:hidden')
            ->not->toContain('<style>');
    });

});

describe('editorjs-renderer', function () {

    it('renders paragraph block', function () {
        $content = json_encode([
            'blocks' => [
                ['type' => 'paragraph', 'data' => ['text' => 'Teks paragraf test.']],
            ],
        ]);

        $html = $this->blade(
            '<x-umpak::editorjs-renderer :content="$c" />',
            ['c' => $content]
        );

        expect((string) $html)->toContain('Teks paragraf test.');
    });

    it('renders header block with correct tag', function () {
        $content = json_encode([
            'blocks' => [
                ['type' => 'header', 'data' => ['text' => 'Judul H2', 'level' => 2]],
            ],
        ]);

        $html = $this->blade(
            '<x-umpak::editorjs-renderer :content="$c" />',
            ['c' => $content]
        );

        expect((string) $html)->toContain('<h2')->toContain('Judul H2');
    });

    it('renders image block using full url directly — no cdn_asset()', function () {
        $url     = 'https://cdn.ponorogo.go.id/bale/dinas-pendidikan/images/foto.jpg';
        $content = json_encode([
            'blocks' => [
                ['type' => 'image', 'data' => ['file' => ['url' => $url], 'caption' => '']],
            ],
        ]);

        $html = $this->blade(
            '<x-umpak::editorjs-renderer :content="$c" />',
            ['c' => $content]
        );

        expect((string) $html)->toContain($url);
    });

    it('handles checklist inside List block (EditorJS v2.26+ format)', function () {
        $content = json_encode([
            'blocks' => [[
                'type' => 'List',
                'data' => [
                    'style' => 'checklist',
                    'items' => [
                        ['content' => 'Item satu', 'meta' => ['checked' => false], 'items' => []],
                        ['content' => 'Item dua',  'meta' => ['checked' => true],  'items' => []],
                    ],
                ],
            ]],
        ]);

        $html = $this->blade(
            '<x-umpak::editorjs-renderer :content="$c" />',
            ['c' => $content]
        );

        expect((string) $html)
            ->toContain('Item satu')
            ->toContain('Item dua')
            ->toContain('line-through');
    });

    it('handles strtolower on block type — List vs list', function () {
        $content = json_encode([
            'blocks' => [[
                'type' => 'List',
                'data' => [
                    'style' => 'unordered',
                    'items' => [['content' => 'Item', 'items' => [], 'meta' => []]],
                ],
            ]],
        ]);

        $html = $this->blade(
            '<x-umpak::editorjs-renderer :content="$c" />',
            ['c' => $content]
        );

        expect((string) $html)->toContain('Item');
    });

    it('accepts content as array (already decoded)', function () {
        $content = [
            'blocks' => [
                ['type' => 'paragraph', 'data' => ['text' => 'Array input test.']],
            ],
        ];

        $html = $this->blade(
            '<x-umpak::editorjs-renderer :content="$c" />',
            ['c' => $content]
        );

        expect((string) $html)->toContain('Array input test.');
    });

    it('renders unsupported block type gracefully', function () {
        $content = json_encode([
            'blocks' => [
                ['type' => 'unknownBlock', 'data' => []],
            ],
        ]);

        $html = $this->blade(
            '<x-umpak::editorjs-renderer :content="$c" />',
            ['c' => $content]
        );

        expect((string) $html)->toContain('unknownBlock');
    });

});

describe('LandingPageComposer', function () {

    it('injects umpakOrg as OptionData into views', function () {
        $html = Blade::render('{{ get_class($umpakOrg) }}');

        expect($html)->toContain('OptionData');
    });

    it('umpakOrg returns null organization_name when options table is empty', function () {
        $html = Blade::render('{{ $umpakOrg->organizationName ?? "null" }}');

        expect(trim($html))->toBe('null');
    });

    it('injects umpakNav as empty collection when navigations table is empty', function () {
        $html = Blade::render('{{ $umpakNav->count() }}');

        expect(trim($html))->toBe('0');
    });

});

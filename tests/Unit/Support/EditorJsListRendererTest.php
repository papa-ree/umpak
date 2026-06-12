<?php

use Bale\Umpak\Support\EditorJsListRenderer;

describe('EditorJsListRenderer', function () {

    describe('format baru (array dengan content key)', function () {

        it('renders unordered list items', function () {
            $items = [
                ['content' => 'Item satu', 'items' => [], 'meta' => []],
                ['content' => 'Item dua', 'items' => [], 'meta' => []],
            ];

            $html = EditorJsListRenderer::render($items, 'unordered');

            expect($html)
                ->toContain('<li')
                ->toContain('Item satu')
                ->toContain('Item dua');
        });

        it('renders ordered list items', function () {
            $items = [
                ['content' => 'Langkah satu', 'items' => [], 'meta' => []],
                ['content' => 'Langkah dua', 'items' => [], 'meta' => []],
            ];

            $html = EditorJsListRenderer::render($items, 'ordered');

            expect($html)
                ->toContain('Langkah satu')
                ->toContain('Langkah dua');
        });

        it('renders nested items recursively', function () {
            $items = [
                [
                    'content' => 'Parent',
                    'items'   => [
                        ['content' => 'Child', 'items' => [], 'meta' => []],
                    ],
                    'meta' => [],
                ],
            ];

            $html = EditorJsListRenderer::render($items, 'unordered');

            expect($html)
                ->toContain('Parent')
                ->toContain('Child')
                ->toContain('<ul');
        });

        it('uses list-decimal class for ordered nested list', function () {
            $items = [
                [
                    'content' => 'Parent',
                    'items'   => [
                        ['content' => 'Child', 'items' => [], 'meta' => []],
                    ],
                    'meta' => [],
                ],
            ];

            $html = EditorJsListRenderer::render($items, 'ordered');

            expect($html)->toContain('list-decimal');
        });

    });

    describe('format lama (string langsung)', function () {

        it('renders plain string items', function () {
            $items = ['Item A', 'Item B', 'Item C'];

            $html = EditorJsListRenderer::render($items, 'unordered');

            expect($html)
                ->toContain('Item A')
                ->toContain('Item B')
                ->toContain('Item C');
        });

    });

    it('returns empty string for empty items array', function () {
        expect(EditorJsListRenderer::render([], 'unordered'))->toBe('');
    });

});

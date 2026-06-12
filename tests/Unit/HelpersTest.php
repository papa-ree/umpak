<?php

use Bale\Umpak\DTOs\OptionData;

describe('helpers', function () {

    describe('cdn_asset()', function () {

        it('returns asset() path when cdn is disabled', function () {
            config()->set('umpak.cdn.enabled', false);

            expect(cdn_asset('images/foto.jpg'))
                ->toBe(asset('images/foto.jpg'));
        });

        it('is identical to cdn_url()', function () {
            config()->set('umpak.cdn.enabled', false);

            expect(cdn_asset('images/foto.jpg'))
                ->toBe(cdn_url('images/foto.jpg'));
        });

    });

    describe('cdn_enabled()', function () {

        it('returns false when cdn is disabled', function () {
            config()->set('umpak.cdn.enabled', false);

            expect(cdn_enabled())->toBeFalse();
        });

        it('returns true when cdn is active', function () {
            config()->set('umpak.cdn.enabled', true);
            config()->set('umpak.cdn.url', 'https://cdn.ponorogo.go.id');

            expect(cdn_enabled())->toBeTrue();
        });

    });

    describe('umpak_org()', function () {

        it('returns OptionData when called without argument', function () {
            expect(umpak_org())->toBeInstanceOf(OptionData::class);
        });

        it('returns null for org slug when options table is empty', function () {
            expect(umpak_org('slug'))->toBeNull();
        });

    });

    describe('umpak_option()', function () {

        it('returns null when key does not exist', function () {
            expect(umpak_option('key_tidak_ada'))->toBeNull();
        });

        it('returns default when key does not exist', function () {
            expect(umpak_option('key_tidak_ada', 'fallback'))->toBe('fallback');
        });

    });

    describe('umpak_config()', function () {

        it('reads from umpak config namespace', function () {
            config()->set('umpak.cdn.url', 'https://cdn.ponorogo.go.id');

            expect(umpak_config('cdn.url'))
                ->toBe('https://cdn.ponorogo.go.id');
        });

        it('returns default when key does not exist', function () {
            expect(umpak_config('key.yang.tidak.ada', 'fallback'))
                ->toBe('fallback');
        });

    });

});

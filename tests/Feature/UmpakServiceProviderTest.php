<?php

use Bale\Umpak\Facades\Umpak;
use Bale\Umpak\Services\CdnService;
use Bale\Umpak\Umpak as UmpakClass;

describe('UmpakServiceProvider', function () {

    describe('config', function () {

        it('merges umpak config', function () {
            expect(config('umpak'))->toBeArray();
        });

        it('has required config keys', function () {
            expect(config('umpak'))
                ->toHaveKeys([
                    'cdn',
                    'balystics_id',
                    'features',
                ]);
        });

        it('cdn config has required keys', function () {
            expect(config('umpak.cdn'))
                ->toHaveKeys(['enabled', 'url', 'prefix']);
        });

        it('features config has required keys', function () {
            expect(config('umpak.features'))
                ->toHaveKeys(['sitemap', 'robots', 'analytics']);
        });

    });

    describe('bindings', function () {

        it('resolves CdnService from container', function () {
            expect(app(CdnService::class))
                ->toBeInstanceOf(CdnService::class);
        });

        it('resolves CdnService as singleton', function () {
            $first  = app(CdnService::class);
            $second = app(CdnService::class);

            expect($first)->toBe($second);
        });

        it('resolves Umpak class from container', function () {
            expect(app(UmpakClass::class))
                ->toBeInstanceOf(UmpakClass::class);
        });

    });

    describe('facade', function () {

        it('resolves Umpak facade', function () {
            expect(Umpak::version())->toBe('0.1.0');
        });

    });

    describe('publishing', function () {

        it('registers umpak:config publish tag', function () {
            $publishes = \Illuminate\Support\ServiceProvider::pathsToPublish(
                \Bale\Umpak\UmpakServiceProvider::class,
                'umpak:config'
            );

            expect($publishes)->not->toBeEmpty();
        });

    });

});

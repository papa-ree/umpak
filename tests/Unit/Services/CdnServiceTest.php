<?php

use Bale\Umpak\Services\CdnService;

describe('CdnService', function () {

    describe('isEnabled()', function () {

        it('returns false when cdn.enabled is false', function () {
            config()->set('umpak.cdn.enabled', false);
            config()->set('umpak.cdn.url', 'https://cdn.example.com');

            $service = new CdnService();

            expect($service->isEnabled())->toBeFalse();
        });

        it('returns false when cdn.url is empty', function () {
            config()->set('umpak.cdn.enabled', true);
            config()->set('umpak.cdn.url', '');

            $service = new CdnService();

            expect($service->isEnabled())->toBeFalse();
        });

        it('returns true when cdn is enabled and url is set', function () {
            config()->set('umpak.cdn.enabled', true);
            config()->set('umpak.cdn.url', 'https://cdn.example.com');

            $service = new CdnService();

            expect($service->isEnabled())->toBeTrue();
        });

    });

    describe('url() — CDN tidak aktif', function () {

        it('falls back to asset() when cdn is disabled', function () {
            config()->set('umpak.cdn.enabled', false);

            $service = new CdnService();

            expect($service->url('images/foto.jpg'))
                ->toBe(asset('images/foto.jpg'));
        });

        it('falls back to asset() when cdn url is empty', function () {
            config()->set('umpak.cdn.enabled', true);
            config()->set('umpak.cdn.url', '');

            $service = new CdnService();

            expect($service->url('images/foto.jpg'))
                ->toBe(asset('images/foto.jpg'));
        });

    });

    describe('url() — shared path', function () {

        it('generates shared URL without org_slug segment', function () {
            config()->set('umpak.cdn.enabled', true);
            config()->set('umpak.cdn.url', 'https://cdn.ponorogo.go.id');
            config()->set('umpak.cdn.prefix', 'bale');

            $service = new CdnService();

            expect($service->url('shared/logo.png'))
                ->toBe('https://cdn.ponorogo.go.id/bale/shared/logo.png');
        });

    });

    describe('url() — trailing slash handling', function () {

        it('handles trailing slash on cdn url', function () {
            config()->set('umpak.cdn.enabled', true);
            config()->set('umpak.cdn.url', 'https://cdn.ponorogo.go.id/');
            config()->set('umpak.cdn.prefix', 'bale');

            $service = new CdnService();

            // orgSlug akan kosong karena tidak ada DB — test hanya trailing slash
            $result = $service->url('shared/logo.png');

            expect($result)->toBe('https://cdn.ponorogo.go.id/bale/shared/logo.png');
        });

        it('handles leading slash on path', function () {
            config()->set('umpak.cdn.enabled', false);

            $service = new CdnService();

            expect($service->url('/images/foto.jpg'))
                ->toBe(asset('images/foto.jpg'));
        });

    });

});

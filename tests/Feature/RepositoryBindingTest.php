<?php

use Bale\Umpak\Contracts\AgendaRepositoryInterface;
use Bale\Umpak\Contracts\AnnouncementRepositoryInterface;
use Bale\Umpak\Contracts\NavigationRepositoryInterface;
use Bale\Umpak\Contracts\OptionRepositoryInterface;
use Bale\Umpak\Contracts\PageRepositoryInterface;
use Bale\Umpak\Contracts\PostRepositoryInterface;
use Bale\Umpak\Contracts\SectionRepositoryInterface;
use Bale\Umpak\Exceptions\AgendaNotFoundException;
use Bale\Umpak\Exceptions\AnnouncementNotFoundException;
use Bale\Umpak\Exceptions\SectionNotFoundException;
use Bale\Umpak\Repositories\AgendaRepository;
use Bale\Umpak\Repositories\AnnouncementRepository;
use Bale\Umpak\Repositories\NavigationRepository;
use Bale\Umpak\Repositories\OptionRepository;
use Bale\Umpak\Repositories\PageRepository;
use Bale\Umpak\Repositories\PostRepository;
use Bale\Umpak\Repositories\SectionRepository;

describe('Repository bindings', function () {

    it('resolves SectionRepositoryInterface', function () {
        expect(app(SectionRepositoryInterface::class))
            ->toBeInstanceOf(SectionRepository::class);
    });

    it('resolves PostRepositoryInterface', function () {
        expect(app(PostRepositoryInterface::class))
            ->toBeInstanceOf(PostRepository::class);
    });

    it('resolves PageRepositoryInterface', function () {
        expect(app(PageRepositoryInterface::class))
            ->toBeInstanceOf(PageRepository::class);
    });

    it('resolves NavigationRepositoryInterface', function () {
        expect(app(NavigationRepositoryInterface::class))
            ->toBeInstanceOf(NavigationRepository::class);
    });

    it('resolves OptionRepositoryInterface', function () {
        expect(app(OptionRepositoryInterface::class))
            ->toBeInstanceOf(OptionRepository::class);
    });

    it('resolves AgendaRepositoryInterface', function () {
        expect(app(AgendaRepositoryInterface::class))
            ->toBeInstanceOf(AgendaRepository::class);
    });

    it('resolves AnnouncementRepositoryInterface', function () {
        expect(app(AnnouncementRepositoryInterface::class))
            ->toBeInstanceOf(AnnouncementRepository::class);
    });

});

describe('SectionRepository — exception handling', function () {

    it('throws SectionNotFoundException when slug not found', function () {
        expect(fn () => app(SectionRepositoryInterface::class)->getBySlug('tidak-ada'))
            ->toThrow(SectionNotFoundException::class);
    });

    it('findBySlug returns null when not found', function () {
        expect(app(SectionRepositoryInterface::class)->findBySlug('tidak-ada'))
            ->toBeNull();
    });

});

describe('AgendaRepository — exception handling', function () {

    it('throws AgendaNotFoundException when id not found', function () {
        expect(fn () => app(AgendaRepositoryInterface::class)->getById('tidak-ada'))
            ->toThrow(AgendaNotFoundException::class);
    });

    it('findById returns null when not found', function () {
        expect(app(AgendaRepositoryInterface::class)->findById('tidak-ada'))
            ->toBeNull();
    });

    it('upcoming() returns empty collection when no data', function () {
        expect(app(AgendaRepositoryInterface::class)->upcoming())->toBeEmpty();
    });

});

describe('AnnouncementRepository — exception handling', function () {

    it('throws AnnouncementNotFoundException when slug not found', function () {
        expect(fn () => app(AnnouncementRepositoryInterface::class)->getBySlug('tidak-ada'))
            ->toThrow(AnnouncementNotFoundException::class);
    });

    it('findBySlug returns null when not found', function () {
        expect(app(AnnouncementRepositoryInterface::class)->findBySlug('tidak-ada'))
            ->toBeNull();
    });

    it('pinned() returns empty collection when no data', function () {
        expect(app(AnnouncementRepositoryInterface::class)->pinned())->toBeEmpty();
    });

});

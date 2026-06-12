<?php

use Bale\Umpak\Contracts\NavigationRepositoryInterface;
use Bale\Umpak\Contracts\OptionRepositoryInterface;
use Bale\Umpak\Contracts\PageRepositoryInterface;
use Bale\Umpak\Contracts\PostRepositoryInterface;
use Bale\Umpak\Contracts\SectionRepositoryInterface;
use Bale\Umpak\Exceptions\SectionNotFoundException;
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

<?php

namespace Bale\Umpak\Repositories;

use Bale\Umpak\Contracts\PageRepositoryInterface;
use Bale\Umpak\DTOs\PageData;
use Bale\Umpak\Exceptions\PageNotFoundException;
use Bale\Umpak\Models\Page;
use Illuminate\Support\Collection;

class PageRepository implements PageRepositoryInterface
{
    public function getBySlug(string $slug): PageData
    {
        $page = $this->findBySlug($slug);

        if ($page === null) {
            throw PageNotFoundException::forSlug($slug);
        }

        return $page;
    }

    public function findBySlug(string $slug): ?PageData
    {
        return Page::where('slug', $slug)
            ->first()
            ?->toData();
    }

    public function allPublished(): Collection
    {
        return Page::all()
            ->map(fn (Page $page) => $page->toData());
    }
}

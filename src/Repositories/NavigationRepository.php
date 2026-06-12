<?php

namespace Bale\Umpak\Repositories;

use Bale\Umpak\Contracts\NavigationRepositoryInterface;
use Bale\Umpak\DTOs\NavigationData;
use Bale\Umpak\Models\Navigation;
use Illuminate\Support\Collection;

class NavigationRepository implements NavigationRepositoryInterface
{
    public function rootWithChildren(): Collection
    {
        return Navigation::root()
            ->active()
            ->with(['children' => fn ($q) => $q->active()->orderBy('order')])
            ->orderBy('order')
            ->get()
            ->map(fn (Navigation $nav) => $this->toData($nav));
    }

    private function toData(Navigation $nav): NavigationData
    {
        return new NavigationData(
            id: $nav->id,
            name: $nav->name,
            slug: $nav->slug,
            url: $nav->url,
            urlMode: $nav->url_mode,
            order: $nav->order,
            actived: $nav->actived,
            parentId: $nav->parent_id,
            pageSlug: $nav->page_slug,
            children: $nav->children
                ->map(fn (Navigation $child) => $this->toData($child))
                ->values()
                ->all(),
            resolvedUrl: $nav->resolveUrl(),
        );
    }
}

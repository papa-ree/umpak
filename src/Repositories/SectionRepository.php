<?php

namespace Bale\Umpak\Repositories;

use Bale\Umpak\Contracts\SectionRepositoryInterface;
use Bale\Umpak\DTOs\SectionData;
use Bale\Umpak\Exceptions\SectionNotFoundException;
use Bale\Umpak\Models\Section;
use Illuminate\Support\Collection;

class SectionRepository implements SectionRepositoryInterface
{
    public function getBySlug(string $slug): SectionData
    {
        $section = $this->findBySlug($slug);

        if ($section === null) {
            throw SectionNotFoundException::forSlug($slug);
        }

        return $section;
    }

    public function findBySlug(string $slug): ?SectionData
    {
        return Section::active()
            ->where('slug', $slug)
            ->first()
            ?->toData();
    }

    public function allForOrg(): Collection
    {
        return Section::active()
            ->get()
            ->map(fn (Section $section) => $section->toData());
    }
}

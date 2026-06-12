<?php

namespace Bale\Umpak\DTOs;

/**
 * Representasi data Navigation item.
 *
 * Mendukung nested menu via $children.
 * Penggunaan di Blade:
 *
 *   @foreach($umpakNav as $item)
 *     <a href="{{ $item->resolvedUrl }}">{{ $item->name }}</a>
 *     @if($item->hasChildren())
 *       @foreach($item->children as $child)
 *         <a href="{{ $child->resolvedUrl }}">{{ $child->name }}</a>
 *       @endforeach
 *     @endif
 *   @endforeach
 */
readonly class NavigationData
{
    /**
     * @param array<int, NavigationData> $children
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $url,
        public ?bool $urlMode,
        public int $order,
        public bool $actived,
        public ?string $parentId,
        public ?string $pageSlug,

        /**
         * Child items untuk dropdown/nested menu.
         *
         * @var array<int, NavigationData>
         */
        public array $children,

        /**
         * URL yang sudah di-resolve berdasarkan urlMode.
         * Siap dipakai langsung di href.
         */
        public string $resolvedUrl,
    ) {}

    /**
     * Cek apakah item ini memiliki child (dropdown).
     */
    public function hasChildren(): bool
    {
        return ! empty($this->children);
    }

    /**
     * Cek apakah ini root item (tidak punya parent).
     */
    public function isRoot(): bool
    {
        return $this->parentId === null;
    }
}

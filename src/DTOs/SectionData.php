<?php

namespace Bale\Umpak\DTOs;

/**
 * Representasi data Section yang dikembalikan ke landing page package.
 *
 * Struktur content.meta mengikuti config cms.sections:
 *
 * mandatory_meta (selalu ada di semua section):
 *   title, subtitle, buttons[], background{type, images[]}
 *
 * custom per section type:
 *   banner      → organization_name
 *   collection  → post_limit, show_excerpt
 *   link-group  → copyright, show_social
 *   blocks      → columns, icon_style
 *   metrics     → number_format
 *   highlight   → background_style
 *
 * Penggunaan di Blade:
 *   $section->meta('title')
 *   $section->meta('post_limit', 3)
 *   $section->meta('background.type', 'image')
 */
readonly class SectionData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $type,
        public string $usage,
        public bool $actived,

        /**
         * Meta section — mandatory + custom per section type.
         *
         * @var array<string, mixed>
         */
        public array $meta,

        /**
         * Items section — struktur bervariasi per section type.
         * blocks:  [{ title, description, icon }]
         * metrics: [{ label, value, suffix }]
         * dst.
         *
         * @var array<int, array<string, mixed>>
         */
        public array $items,
    ) {}

    /**
     * Ambil nilai dari meta berdasarkan key, dengan fallback.
     *
     * Mendukung dot notation untuk nested key:
     *   $section->meta('background.type', 'image')
     *   $section->meta('buttons.0.label')
     */
    public function meta(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta, $key, $default);
    }

    /**
     * Cek apakah section memiliki items.
     */
    public function hasItems(): bool
    {
        return ! empty($this->items);
    }

    /**
     * Ambil background images dari meta.
     *
     * @return array<int, array{path: string, url: string, disk: string}>
     */
    public function backgroundImages(): array
    {
        return $this->meta('background.images', []);
    }

    /**
     * Ambil buttons dari meta.
     *
     * @return array<int, array{label: string, url: string, show: bool, class: string}>
     */
    public function buttons(): array
    {
        return array_filter(
            $this->meta('buttons', []),
            fn ($btn) => $btn['show'] ?? true
        );
    }
}

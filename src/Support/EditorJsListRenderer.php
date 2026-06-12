<?php

namespace Bale\Umpak\Support;

class EditorJsListRenderer
{
    /**
     * Render nested list items menjadi HTML string.
     *
     * Mendukung dua format item dari EditorJS:
     * - Format lama: string langsung
     * - Format baru: array { content: string, items: array, meta: array }
     *
     * @param array<int, mixed> $items
     */
    public static function render(array $items, string $style): string
    {
        $html = '';

        foreach ($items as $item) {
            $content     = is_array($item) ? ($item['content'] ?? '') : $item;
            $nestedItems = is_array($item) ? ($item['items'] ?? []) : [];

            $html .= '<li class="leading-relaxed">'.$content;

            if (! empty($nestedItems)) {
                $tag   = $style === 'ordered' ? 'ol' : 'ul';
                $class = $style === 'ordered' ? 'list-decimal' : 'list-disc';
                $html .= '<'.$tag.' class="'.$class.' list-inside mt-2 space-y-2 ml-4">';
                $html .= self::render($nestedItems, $style);
                $html .= '</'.$tag.'>';
            }

            $html .= '</li>';
        }

        return $html;
    }
}

<?php

namespace Bale\Umpak\Support;

class Sanitizer
{
    /**
     * Allowed HTML tags.
     */
    protected static array $allowedTags = [
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'p', 'br', 'b', 'i', 'u', 'strong', 'em', 'code', 'pre', 'span', 'a',
        'ul', 'ol', 'li', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'figure', 'figcaption', 'img', 'iframe', 'div'
    ];

    /**
     * Allowed attributes globally.
     */
    protected static array $allowedAttributes = [
        'class' => true,
        'id' => true,
        'style' => true,
    ];

    /**
     * Allowed attributes per tag.
     */
    protected static array $allowedTagAttributes = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height'],
        'iframe' => ['src', 'width', 'height', 'frameborder', 'allow', 'allowfullscreen', 'scrolling'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan'],
    ];

    /**
     * Sanitasi string HTML.
     */
    public static function cleanHtml(?string $html, string $type = 'default'): string
    {
        if (empty($html)) {
            return '';
        }

        $dom = new \DOMDocument();
        
        // Mencegah error reporting libxml saat parsing elemen HTML5/format custom
        libxml_use_internal_errors(true);

        // Bungkus HTML dengan encoding UTF-8 dan wrapper div
        $wrappedHtml = '<div>' . $html . '</div>';
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $wrappedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $root = $dom->getElementsByTagName('div')->item(0);

        if ($root) {
            self::sanitizeNode($root, $type);
            
            // Satukan kembali HTML di dalam wrapper <div>
            $cleaned = '';
            foreach ($root->childNodes as $child) {
                $cleaned .= $dom->saveHTML($child);
            }
            libxml_clear_errors();
            return $cleaned;
        }

        libxml_clear_errors();
        return '';
    }

    /**
     * Sanitasi node secara rekursif.
     */
    protected static function sanitizeNode(\DOMNode $node, string $type): void
    {
        if ($node->hasChildNodes()) {
            $children = iterator_to_array($node->childNodes);
            foreach ($children as $child) {
                self::sanitizeNode($child, $type);
            }
        }

        if ($node->nodeType === XML_ELEMENT_NODE) {
            /** @var \DOMElement $element */
            $element = $node;
            $tagName = strtolower($element->tagName);

            // Jika tag tidak diizinkan, hapus tag tapi pertahankan konten teks / anak didalamnya
            if (!in_array($tagName, self::$allowedTags, true)) {
                while ($element->hasChildNodes()) {
                    $child = $element->firstChild;
                    $element->parentNode->insertBefore($child, $element);
                }
                $element->parentNode->removeChild($element);
                return;
            }

            // Proteksi iframe (hanya boleh YouTube, Vimeo, atau Google Maps Embed)
            if ($tagName === 'iframe') {
                $src = $element->getAttribute('src');
                if (!self::isValidEmbedUrl($src)) {
                    $element->parentNode->removeChild($element);
                    return;
                }
            }

            // Bersihkan atribut
            if ($element->hasAttributes()) {
                $attrs = iterator_to_array($element->attributes);
                foreach ($attrs as $attr) {
                    $attrName = strtolower($attr->name);

                    // Hapus event handlers (eg. onclick, onload, dsb)
                    if (str_starts_with($attrName, 'on')) {
                        $element->removeAttribute($attr->name);
                        continue;
                    }

                    // Hapus URI scheme berbahaya
                    if (in_array($attrName, ['href', 'src'], true)) {
                        $value = trim($attr->value);
                        if (preg_match('/^(javascript|data|vbscript):/i', $value)) {
                            $element->removeAttribute($attr->name);
                            continue;
                        }
                    }

                    // Hanya simpan atribut yang explicitly diperbolehkan
                    $isAllowedGlobal = isset(self::$allowedAttributes[$attrName]);
                    $allowedForTag = self::$allowedTagAttributes[$tagName] ?? [];
                    $isAllowedForTag = in_array($attrName, $allowedForTag, true);

                    if (!$isAllowedGlobal && !$isAllowedForTag) {
                        $element->removeAttribute($attr->name);
                    }
                }
            }
        }
    }

    /**
     * Sanitasi SVG string.
     */
    public static function cleanSvg(?string $svg): string
    {
        if (empty($svg)) {
            return '';
        }

        if (!str_contains($svg, '<svg')) {
            return '';
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);

        // Load dokumen XML
        $dom->loadXML('<?xml encoding="utf-8" ?>' . $svg, LIBXML_NOERROR | LIBXML_NOWARNING);
        
        $svgElements = $dom->getElementsByTagName('svg');
        if ($svgElements->count() > 0) {
            $svgNode = $svgElements->item(0);
            
            self::sanitizeSvgElement($svgNode);
            
            $cleaned = $dom->saveXML($svgNode);
            libxml_clear_errors();
            
            // Hilangkan XML declaration header hasil saveXML
            return preg_replace('/<\?xml[^>]*\?>/i', '', $cleaned);
        }

        libxml_clear_errors();
        return '';
    }

    protected static function sanitizeSvgElement(\DOMNode $node): void
    {
        $allowedElements = [
            'svg', 'path', 'g', 'defs', 'linearGradient', 'stop', 'rect',
            'circle', 'ellipse', 'line', 'polyline', 'polygon', 'text', 'tspan'
        ];

        if ($node->hasChildNodes()) {
            $children = iterator_to_array($node->childNodes);
            foreach ($children as $child) {
                self::sanitizeSvgElement($child);
            }
        }

        if ($node->nodeType === XML_ELEMENT_NODE) {
            /** @var \DOMElement $element */
            $element = $node;
            $tagName = strtolower($element->tagName);

            if (!in_array($tagName, $allowedElements, true)) {
                $element->parentNode->removeChild($element);
                return;
            }

            if ($element->hasAttributes()) {
                $attrs = iterator_to_array($element->attributes);
                foreach ($attrs as $attr) {
                    $attrName = strtolower($attr->name);

                    // Hapus event handlers
                    if (str_starts_with($attrName, 'on')) {
                        $element->removeAttribute($attr->name);
                        continue;
                    }

                    // Hapus skema link berbahaya pada SVG
                    if (in_array($attrName, ['href', 'xlink:href'], true)) {
                        $value = trim($attr->value);
                        if (preg_match('/^(javascript|data|vbscript):/i', $value)) {
                            $element->removeAttribute($attr->name);
                        }
                    }
                }
            }
        }
    }

    /**
     * Validasi URL Youtube/Vimeo/Google Maps yang aman untuk iframe.
     */
    protected static function isValidEmbedUrl(string $url): bool
    {
        $allowedPatterns = [
            '/^https?:\/\/(www\.)?youtube\.com\/embed\//',
            '/^https?:\/\/player\.vimeo\.com\/video\//',
            '/^https?:\/\/(www\.)?google\.com\/maps\/embed\//'
        ];

        foreach ($allowedPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }
}

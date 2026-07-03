@props(['content'])

@php
    use Bale\Umpak\Support\EditorJsListRenderer;
    use Bale\Umpak\Support\Sanitizer;

    $editorData = is_string($content) ? json_decode($content, true) : $content;
    $blocks     = $editorData['blocks'] ?? [];
@endphp

<style>
    .editorjs-content a:not(.group):not(.flex):not(.no-underline) {
        text-decoration: underline;
        text-underline-offset: 4px;
        text-decoration-thickness: 1px;
        transition: all 300ms ease-in-out;
    }
    .editorjs-content a:not(.group):not(.flex):not(.no-underline):hover {
        text-decoration: underline wavy;
        text-decoration-thickness: 1.5px;
        color: #3b82f6;
    }
</style>

<div class="editorjs-content">
    @foreach($blocks as $block)
        @php
            $type = strtolower($block['type'] ?? 'paragraph');
            $data = $block['data'] ?? [];
        @endphp

        @switch($type)

            @case('header')
                @php $level = $data['level'] ?? 2; $text = $data['text'] ?? ''; @endphp
                @if($level == 1)
                    <h1 class="text-4xl font-bold mb-6 mt-8 text-gray-900 dark:text-white">{!! Sanitizer::cleanHtml($text) !!}</h1>
                @elseif($level == 2)
                    <h2 class="text-3xl font-bold mb-5 mt-7 text-gray-900 dark:text-white">{!! Sanitizer::cleanHtml($text) !!}</h2>
                @elseif($level == 3)
                    <h3 class="text-2xl font-semibold mb-4 mt-6 text-gray-900 dark:text-white">{!! Sanitizer::cleanHtml($text) !!}</h3>
                @elseif($level == 4)
                    <h4 class="text-xl font-semibold mb-3 mt-5 text-gray-900 dark:text-white">{!! Sanitizer::cleanHtml($text) !!}</h4>
                @elseif($level == 5)
                    <h5 class="text-lg font-semibold mb-3 mt-4 text-gray-900 dark:text-white">{!! Sanitizer::cleanHtml($text) !!}</h5>
                @else
                    <h6 class="text-base font-semibold mb-2 mt-3 text-gray-900 dark:text-white">{!! Sanitizer::cleanHtml($text) !!}</h6>
                @endif
                @break

            @case('paragraph')
                @php $text = $data['text'] ?? ''; @endphp
                @if(! empty($text))
                    <p class="text-gray-700 dark:text-gray-300 mb-4 leading-relaxed text-lg text-justify">
                        {!! Sanitizer::cleanHtml($text) !!}
                    </p>
                @endif
                @break

            @case('list')
                @php
                    $style = $data['style'] ?? 'unordered';
                    $items = $data['items'] ?? [];
                @endphp

                {{--
                    Fix: EditorJS v2.26+ menggunakan style 'checklist' di dalam block type 'list'.
                    Block type 'checklist' terpisah sudah deprecated di versi baru.
                    Kedua format ditangani di sini.
                --}}
                @if($style === 'checklist')
                    <ul class="mb-6 space-y-3">
                        @foreach($items as $item)
                            @php
                                $checked = $item['meta']['checked'] ?? $item['checked'] ?? false;
                                $text    = $item['content'] ?? $item['text'] ?? '';
                            @endphp
                            <li class="flex items-start gap-3">
                                <span class="flex items-center h-6 shrink-0">
                                    @if($checked)
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </span>
                                <span class="text-gray-700 dark:text-gray-300 text-lg {{ $checked ? 'line-through opacity-60' : '' }}">
                                    {!! Sanitizer::cleanHtml($text) !!}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @elseif($style === 'ordered')
                    <ol class="list-decimal list-inside mb-6 space-y-2 text-gray-700 dark:text-gray-300 text-lg ml-4">
                        {!! EditorJsListRenderer::render($items, 'ordered') !!}
                    </ol>
                @else
                    <ul class="list-disc list-inside mb-6 space-y-2 text-gray-700 dark:text-gray-300 text-lg ml-4">
                        {!! EditorJsListRenderer::render($items, 'unordered') !!}
                    </ul>
                @endif
                @break

            {{-- Deprecated: block type 'checklist' terpisah (EditorJS < v2.26) --}}
            @case('checklist')
                @php $items = $data['items'] ?? []; @endphp
                <ul class="mb-6 space-y-3">
                    @foreach($items as $item)
                        @php
                            $checked = $item['checked'] ?? false;
                            $text    = $item['text'] ?? '';
                        @endphp
                        <li class="flex items-start gap-3">
                            <span class="flex items-center h-6 shrink-0">
                                @if($checked)
                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </span>
                            <span class="text-gray-700 dark:text-gray-300 text-lg {{ $checked ? 'line-through opacity-60' : '' }}">
                                {!! Sanitizer::cleanHtml($text) !!}
                            </span>
                        </li>
                    @endforeach
                </ul>
                @break

            @case('image')
                @php
                    $url            = $data['file']['url'] ?? '';
                    $caption        = $data['caption'] ?? '';
                    $stretched      = $data['stretched'] ?? false;
                    $withBorder     = $data['withBorder'] ?? false;
                    $withBackground = $data['withBackground'] ?? false;
                @endphp
                @if($url)
                    <figure class="mb-8 {{ $stretched ? 'w-full' : 'max-w-3xl mx-auto' }}">
                        <div class="overflow-hidden rounded-xl
                                    {{ $withBorder ? 'border border-gray-200 dark:border-gray-700' : '' }}
                                    {{ $withBackground ? 'bg-gray-100 dark:bg-gray-800 p-4' : '' }}">
                            {{--
                                Image URL dari EditorJS sudah full URL dari CDN.
                                Tidak perlu cdn_asset() — digunakan langsung.
                            --}}
                            <img src="{{ $url }}"
                                 alt="{{ $caption }}"
                                 class="w-full h-auto"
                                  loading="lazy"
                                 decoding="async">
                        </div>
                        @if($caption)
                            <figcaption class="text-center text-sm text-gray-500 dark:text-gray-400 mt-3">
                                {!! Sanitizer::cleanHtml($caption) !!}
                            </figcaption>
                        @endif
                    </figure>
                @endif
                @break

            @case('linktool')
                @php
                    $link        = $data['link'] ?? '';
                    $meta        = $data['meta'] ?? [];
                    $linkTitle   = $meta['title'] ?? $link;
                    $description = $meta['description'] ?? '';
                    $image       = $meta['image']['url'] ?? '';
                @endphp
                <div class="mb-6">
                    <a href="{{ $link }}"
                       target="_blank" rel="nofollow noopener"
                       class="flex flex-col md:flex-row
                              border border-gray-200 dark:border-gray-700
                              rounded-xl overflow-hidden
                              bg-white dark:bg-gray-800
                              shadow-sm hover:border-gray-300 dark:hover:border-gray-600
                              transition-colors group">
                        @if($image)
                            <div class="md:w-48 h-40 md:h-auto overflow-hidden shrink-0">
                                <img src="{{ $image }}" alt="{{ $linkTitle }}"
                                     class="w-full h-full object-cover
                                            group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy" decoding="async">
                            </div>
                        @endif
                        <div class="p-4 flex flex-col justify-center">
                            <h4 class="text-lg font-bold mb-2 line-clamp-1
                                       text-gray-900 dark:text-white
                                       group-hover:text-blue-600 dark:group-hover:text-blue-400
                                       transition-colors">
                                {{ $linkTitle }}
                            </h4>
                            @if($description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                                    {{ $description }}
                                </p>
                            @endif
                            <span class="text-xs text-blue-600 dark:text-blue-400 flex items-center gap-1 font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                {{ parse_url($link, PHP_URL_HOST) }}
                            </span>
                        </div>
                    </a>
                </div>
                @break

            @case('raw')
                <div class="raw-html-block mb-6">
                    {!! Sanitizer::cleanHtml($data['html'] ?? '', 'raw') !!}
                </div>
                @break

            @case('warning')
                @php
                    $warningTitle   = $data['title'] ?? '';
                    $warningMessage = $data['message'] ?? '';
                @endphp
                <div class="mb-6 p-4 rounded-xl
                            bg-yellow-50 dark:bg-yellow-900/20
                            border border-yellow-200 dark:border-yellow-800">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 shrink-0 mt-0.5"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            @if($warningTitle)
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-1">
                                    {!! Sanitizer::cleanHtml($warningTitle) !!}
                                </h4>
                            @endif
                            <p class="text-yellow-700 dark:text-yellow-400">
                                {!! Sanitizer::cleanHtml($warningMessage) !!}
                            </p>
                        </div>
                    </div>
                </div>
                @break

            @case('table')
                @php
                    $tableContent   = $data['content'] ?? [];
                    $withHeadings   = $data['withHeadings'] ?? false;
                    $tableStretched = $data['stretched'] ?? false;

                    /**
                     * Cek apakah sebuah cell mengandung tag <a> (hyperlink).
                     * Jika ya, cell tersebut akan dirender sebagai tombol bergaya.
                     */
                    $cellHasLink = fn(string $cell): bool => (bool) preg_match('/<a\s[^>]*href/i', $cell);

                    /**
                     * Tentukan indeks kolom terakhir dari baris pertama data
                     * untuk keperluan deteksi kolom "aksi" (paling kanan).
                     */
                    $lastColIndex = count($tableContent[0] ?? []) - 1;
                @endphp
                <div class="mb-8 overflow-x-auto rounded-xl
                            border border-gray-200 dark:border-gray-700
                            {{ $tableStretched ? 'w-full' : 'max-w-full' }}">
                    <table class="min-w-full border-collapse">
                        @if($withHeadings && count($tableContent) > 0)
                            <thead class="bg-gray-50 dark:bg-gray-800/50">
                                <tr>
                                    @foreach($tableContent[0] as $colIdx => $cell)
                                        <th class="px-6 py-4 text-sm font-bold uppercase tracking-wider
                                                   text-gray-900 dark:text-white
                                                   border-b border-gray-200 dark:border-gray-700
                                                   {{ $colIdx === $lastColIndex ? 'text-right' : 'text-left' }}">
                                            {!! Sanitizer::cleanHtml($cell) !!}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900">
                                @foreach(array_slice($tableContent, 1) as $row)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                        @foreach($row as $colIdx => $cell)
                                            @php
                                                $isLastCol  = $colIdx === $lastColIndex;
                                                $isLinkCell = $isLastCol && !empty($cell) && $cellHasLink($cell);
                                            @endphp
                                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300
                                                       border-b border-gray-100 dark:border-gray-800
                                                       {{ $isLastCol ? 'text-right' : '' }}">
                                                @if($isLinkCell)
                                                    @php
                                                        // Ekstrak href dan label dari tag <a> pertama yang ditemukan
                                                        preg_match('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $cell, $linkMatch);
                                                        $linkHref  = Sanitizer::safeUrl($linkMatch[1] ?? '');
                                                        $linkLabel = strip_tags($linkMatch[2] ?? 'Unduh');
                                                    @endphp
                                                    <a href="{{ $linkHref }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-2
                                                              px-5 py-2.5 rounded-xl font-semibold
                                                              text-xs md:text-sm
                                                              text-gray-700 dark:text-gray-300
                                                              bg-gray-200 dark:bg-slate-800/60
                                                              border border-transparent
                                                              hover:bg-gray-300 dark:hover:bg-slate-700
                                                              hover:border-gray-300 dark:hover:border-slate-600
                                                              transition-all duration-300
                                                              no-underline
                                                              group/tablelink">
                                                        <svg class="w-4 h-4 transition-transform group-hover/tablelink:translate-x-0.5 group-hover/tablelink:-translate-y-0.5"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                        <span>{{ $linkLabel }}</span>
                                                    </a>
                                                @else
                                                    {!! empty($cell) ? '&nbsp;' : Sanitizer::cleanHtml($cell) !!}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tbody class="bg-white dark:bg-gray-900">
                                @foreach($tableContent as $row)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                        @foreach($row as $colIdx => $cell)
                                            @php
                                                $isLastCol  = $colIdx === $lastColIndex;
                                                $isLinkCell = $isLastCol && !empty($cell) && $cellHasLink($cell);
                                            @endphp
                                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300
                                                       border-b border-gray-100 dark:border-gray-800
                                                       {{ $isLastCol ? 'text-right' : '' }}">
                                                @if($isLinkCell)
                                                    @php
                                                        preg_match('/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', $cell, $linkMatch);
                                                        $linkHref  = Sanitizer::safeUrl($linkMatch[1] ?? '');
                                                        $linkLabel = strip_tags($linkMatch[2] ?? 'Unduh');
                                                    @endphp
                                                    <a href="{{ $linkHref }}"
                                                       target="_blank"
                                                       rel="noopener noreferrer"
                                                       class="inline-flex items-center gap-2
                                                              px-5 py-2.5 rounded-xl font-semibold
                                                              text-xs md:text-sm
                                                              text-gray-700 dark:text-gray-300
                                                              bg-gray-200 dark:bg-slate-800/60
                                                              border border-transparent
                                                              hover:bg-gray-300 dark:hover:bg-slate-700
                                                              hover:border-gray-300 dark:hover:border-slate-600
                                                              transition-all duration-300
                                                              no-underline
                                                              group/tablelink">
                                                        {{-- icon --}}
                                                        {{-- <svg class="w-4 h-4 transition-transform group-hover/tablelink:translate-x-0.5 group-hover/tablelink:-translate-y-0.5"
                                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg> --}}
                                                        <span>{{ $linkLabel }}</span>
                                                    </a>
                                                @else
                                                    {!! empty($cell) ? '&nbsp;' : Sanitizer::cleanHtml($cell) !!}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>
                @break

            @case('embed')
                @php
                    $embedUrl     = $data['embed'] ?? '';
                    $embedCaption = $data['caption'] ?? '';
                @endphp
                @if($embedUrl)
                    <figure class="mb-8">
                        <div class="relative overflow-hidden rounded-xl"
                             style="padding-bottom: 56.25%;">
                            <iframe src="{{ $embedUrl }}"
                                    class="absolute top-0 left-0 w-full h-full"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write;
                                           encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                            </iframe>
                        </div>
                        @if($embedCaption)
                            <figcaption class="text-center text-sm text-gray-600 dark:text-gray-400 mt-3">
                                {!! Sanitizer::cleanHtml($embedCaption) !!}
                            </figcaption>
                        @endif
                    </figure>
                @endif
                @break

            @case('attaches')
                @php
                    $file      = $data['file'] ?? [];
                    $fileTitle = $data['title'] ?? '';
                @endphp
                <div class="mb-6 rounded-xl p-6
                            border border-gray-200 dark:border-gray-700
                            bg-gray-50 dark:bg-gray-800/50
                            hover:bg-gray-100 dark:hover:bg-gray-800
                            transition-colors">
                    <a href="{{ $file['url'] ?? '#' }}"
                       class="flex items-center gap-4" download>
                        <div class="shrink-0">
                            <svg class="w-12 h-12 text-blue-500"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="grow">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ $fileTitle ?: ($file['name'] ?? 'Download File') }}
                            </h4>
                            @if(isset($file['size']))
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ number_format($file['size'] / 1024, 2) }} KB
                                </p>
                            @endif
                        </div>
                        <div class="shrink-0">
                            <svg class="w-6 h-6 text-gray-400"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </div>
                    </a>
                </div>
                @break

            @default
                <div class="mb-4 p-4 rounded-lg
                            bg-gray-100 dark:bg-gray-800">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Unsupported block type:
                        <code class="text-xs px-2 py-1 rounded
                                     bg-gray-200 dark:bg-gray-700">
                            {{ $block['type'] ?? 'unknown' }}
                        </code>
                    </p>
                </div>

        @endswitch
    @endforeach
</div>

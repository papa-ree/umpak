@props([
    'items' => [],
])

@if(!empty($items))
    {{-- JSON-LD Structured Data untuk SEO --}}
    @php
        $breadcrumbList = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->map(fn($item, $index) => array_filter([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['label'],
                'item' => isset($item['url']) ? url($item['url']) : null,
            ]))->values()->all(),
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($breadcrumbList, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
    </script>

    <nav {{ $attributes->merge(['class' => '']) }} aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-1 text-sm text-slate-500 dark:text-slate-400">
            @foreach($items as $item)
                <li class="flex items-center gap-1">

                    @if(! $loop->first)
                        <svg class="w-3 h-3 text-slate-400 dark:text-slate-600 shrink-0"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif

                    @if(isset($item['url']) && ! $loop->last)
                        <a href="{{ $item['url'] }}"
                           class="hover:text-slate-700 dark:hover:text-slate-200
                                  transition-colors duration-200">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="{{ $loop->last
                            ? 'text-slate-700 dark:text-slate-200 font-medium'
                            : '' }}
                            truncate max-w-[200px]"
                            aria-current="{{ $loop->last ? 'page' : 'false' }}">
                            {{ $item['label'] }}
                        </span>
                    @endif

                </li>
            @endforeach
        </ol>
    </nav>
@endif

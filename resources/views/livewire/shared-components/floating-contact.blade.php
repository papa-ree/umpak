<div
    x-data="{
        showTop: false,
        showContact: false,
    }"
    x-init="window.addEventListener('scroll', () => { showTop = window.scrollY > 400 })"
    class="fixed bottom-6 right-6 z-500 flex flex-col items-end gap-3" x-cloak
>
    {{-- ── Contact Panel ────────────────────────────────────────── --}}
    @if ($hasContact)
        <div
            x-show="showContact"
            x-transition:enter="transition ease-out duration-300 origin-bottom-right"
            x-transition:enter-start="opacity-0 scale-90 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 origin-bottom-right"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-3"
            @click.outside="showContact = false"
            style="display:none;"
            class="w-72 rounded-2xl overflow-hidden shadow-2xl dark:border dark:border-white/10 bg-white dark:bg-slate-900"
        >
            {{-- ── Header Gradient ── --}}
            <div class="relative bg-linear-to-br from-slate-700 to-slate-900 dark:from-slate-800 dark:to-black px-5 pt-5 pb-8">
                {{-- Decorative circles --}}
                <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full bg-white/5"></div>
                <div class="absolute top-2 right-8 w-8 h-8 rounded-full bg-white/5"></div>

                {{-- Close button --}}
                <button
                    @click="showContact = false"
                    type="button"
                    class="absolute top-3 right-3 w-6 h-6 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors duration-200"
                    aria-label="Tutup"
                >
                    <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                {{-- Avatar / Icon --}}
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-3 shadow-inner">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>

                <p class="text-white font-bold text-base leading-tight">{{ $title }}</p>
                <p class="text-slate-300 text-xs mt-0.5">Kami siap membantu Anda</p>
            </div>

            {{-- ── Body ── --}}
            <div class="bg-white dark:bg-slate-800 px-4 pt-1 pb-4 -mt-4 rounded-t-2xl relative">

                {{-- Contact Infos --}}
                @if (!empty($contactInfos))
                    <div class="space-y-1.5 pt-3 text-left">
                        @foreach ($contactInfos as $field => $value)
                            @if ($field === 'email')
                                <a
                                    href="mailto:{{ $value }}"
                                    title="{{ $value }}"
                                    class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 group transition-all duration-200"
                                >
                                    <span class="shrink-0 w-8 h-8 rounded-lg bg-linear-to-br from-slate-500 to-slate-700 flex items-center justify-center shadow-sm shadow-slate-200 dark:shadow-slate-900 border border-slate-400/20">
                                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                            <polyline points="22,6 12,13 2,6"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Email</p>
                                        <p class="text-sm text-slate-700 dark:text-slate-200 font-semibold truncate group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $value }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 group-hover:translate-x-0.5 transition-all shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </a>
                            @elseif (in_array($field, ['phone', 'telepon', 'no_telepon', 'telp']))
                                <a
                                    href="tel:{{ $value }}"
                                    title="{{ $value }}"
                                    class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 group transition-all duration-200"
                                >
                                    <span class="shrink-0 w-8 h-8 rounded-lg bg-linear-to-br from-slate-500 to-slate-700 flex items-center justify-center shadow-sm shadow-slate-200 dark:shadow-slate-900 border border-slate-400/20">
                                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.02 1.18 2 2 0 012 .02h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">Telepon</p>
                                        <p class="text-sm text-slate-700 dark:text-slate-200 font-semibold truncate group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $value }}</p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300 dark:text-slate-600 group-hover:text-slate-500 group-hover:translate-x-0.5 transition-all shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </a>
                            @else
                                <div class="flex items-center gap-3 p-2.5 rounded-xl group transition-all duration-200">
                                    <span class="shrink-0 w-8 h-8 rounded-lg bg-linear-to-br from-slate-400 to-slate-600 flex items-center justify-center shadow-sm border border-slate-300/20">
                                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium uppercase tracking-wider">{{ str_replace('_', ' ', $field) }}</p>
                                        <p class="text-sm text-slate-700 dark:text-slate-200 font-semibold truncate">{{ $value }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- ── Social Media ── --}}
                @if (!empty($socialLinks))
                    <div class="flex items-center gap-2 my-3">
                        <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700/50"></div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider px-1">Sosial Media</span>
                        <div class="flex-1 h-px bg-slate-100 dark:bg-slate-700/50"></div>
                    </div>

                    <div class="flex flex-wrap gap-2.5 justify-center">
                        @foreach ($socialLinks as $key => $sm)
                            <a
                                href="{{ \Illuminate\Support\Str::startsWith($sm['url'], ['http://', 'https://']) ? $sm['url'] : 'https://' . $sm['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="{{ $sm['name'] }}"
                                class="group relative flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all duration-200 hover:scale-110 hover:-translate-y-0.5 hover:bg-slate-800 hover:text-white dark:hover:bg-white dark:hover:text-slate-900 shadow-sm border border-slate-200/50 dark:border-slate-600/50"
                            >
                                <span class="w-[18px] h-[18px] flex items-center justify-center [&>svg]:w-[18px] [&>svg]:h-[18px]">
                                    {!! $sm['icon'] !!}
                                </span>
                                {{-- Tooltip --}}
                                <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-semibold px-2 py-1 rounded-md whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none shadow-xl border border-white/10 z-10">
                                    {{ $sm['name'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Contact Toggle Button ────────────────────── --}}
        <button
            @click="showContact = !showContact"
            type="button"
            class="relative w-12 h-12 {{ $borderColor ? 'border-2 border-' . $borderColor : '' }} bg-slate-800 dark:bg-slate-100 text-white dark:text-slate-900 rounded-2xl shadow-xl hover:shadow-slate-500/30 hover:shadow-2xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-300 cursor-pointer group focus:outline-none focus:ring-2 focus:ring-slate-500/50"
            :aria-label="showContact ? 'Tutup kontak' : 'Tampilkan kontak'"
            :aria-expanded="showContact"
        >
            {{-- Ping animation when closed --}}
            <span x-show="!showContact" class="absolute -top-1 -right-1 flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500 shadow-sm"></span>
            </span>

            {{-- Icon: message ↔ close --}}
            <span class="absolute inset-0 flex items-center justify-center transition-all duration-300"
                :class="showContact ? 'opacity-100 rotate-0' : 'opacity-0 rotate-90'"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </span>
            <span class="absolute inset-0 flex items-center justify-center transition-all duration-300"
                :class="showContact ? 'opacity-0 -rotate-90' : 'opacity-100 rotate-0'"
            >
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </span>
        </button>
    @endif

    {{-- ── Back to Top Button ───────────────────────────────────── --}}
    <div
        x-show="showTop"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-75 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-75 translate-y-4"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="w-12 h-12 {{ $borderColor ? 'border border-' . $borderColor : '' }} bg-slate-800 dark:bg-slate-100 text-white dark:text-slate-900 rounded-2xl shadow-xl hover:shadow-slate-500/30 hover:shadow-2xl hover:-translate-y-0.5 active:translate-y-0 active:scale-95 transition-all duration-300 cursor-pointer group flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-slate-500/50"
        aria-label="Kembali ke Atas"
        style="display: none;"
    >
        <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform duration-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"/>
        </svg>
    </div>
</div>

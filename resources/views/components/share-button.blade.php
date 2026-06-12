@props([
    'url',
    'title',
    'text' => '',
])

{{--
    Print behavior didelegasikan ke landing page package.
    Tambahkan class print:hidden pada navbar, footer, dan elemen
    yang tidak perlu dicetak di markup masing-masing instansi.
--}}
<div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-3 print:hidden']) }}>

    {{-- Tombol Cetak --}}
    <button
        onclick="window.print()"
        type="button"
        class="inline-flex items-center gap-2 cursor-pointer
               px-5 py-2.5 rounded-xl font-semibold
               text-xs md:text-sm
               text-gray-700 dark:text-gray-300
               bg-gray-200 dark:bg-slate-800/60
               border border-transparent
               hover:bg-gray-300 dark:hover:bg-slate-700
               hover:border-gray-300 dark:hover:border-slate-600
               transition-all duration-300
               group/print">
        <svg class="w-4 h-4 transition-transform group-hover/print:scale-110"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        <span>Cetak</span>
    </button>

    {{-- Tombol Bagikan --}}
    <div x-data="{
            copied: false,
            share() {
                const shareTitle = @js($title);
                const shareText  = @js($text);
                const shareUrl   = @js($url);

                const apiText  = shareText
                    ? `${shareTitle}\n\n${shareText}`
                    : shareTitle;

                const clipText = shareText
                    ? `${shareTitle}\n\n${shareText}\n\nInfo selengkapnya: ${shareUrl}`
                    : `${shareTitle}\n\nInfo selengkapnya: ${shareUrl}`;

                if (navigator.share) {
                    navigator.share({
                        title: shareTitle,
                        text: apiText,
                        url: shareUrl,
                    }).catch(console.error);
                } else {
                    navigator.clipboard.writeText(clipText).then(() => {
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    });
                }
            }
        }">
        <button
            @click="share"
            type="button"
            class="inline-flex items-center gap-2 cursor-pointer
                   px-5 py-2.5 rounded-xl font-semibold
                   text-xs md:text-sm
                   text-gray-700 dark:text-gray-300
                   bg-gray-200 dark:bg-slate-800/60
                   border border-transparent
                   hover:bg-gray-300 dark:hover:bg-slate-700
                   hover:border-gray-300 dark:hover:border-slate-600
                   transition-all duration-300
                   group/share">
            <div class="relative w-4 h-4">
                <svg x-show="!copied"
                     class="w-4 h-4 transition-transform group-hover/share:rotate-12"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                <svg x-show="copied" x-cloak
                     class="w-4 h-4 text-green-500 animate-bounce"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span x-text="copied ? 'Tersalin!' : 'Bagikan'"></span>
        </button>
    </div>

</div>

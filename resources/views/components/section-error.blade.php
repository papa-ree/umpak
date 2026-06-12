@props([
    'title'   => 'Konten Tidak Ditemukan',
    'message' => 'Silakan konfigurasi section ini di panel admin.',
])

<section class="relative min-h-[60vh] flex items-center justify-center
                bg-slate-50 dark:bg-slate-900
                border-y border-slate-200 dark:border-slate-700">
    <div class="container mx-auto px-4 text-center">

        <div class="inline-flex items-center justify-center
                    w-20 h-20 rounded-full mb-6
                    bg-slate-100 dark:bg-slate-800">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-10 h-10 text-slate-400 dark:text-slate-500"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>

        <h2 class="text-2xl font-bold mb-2
                   text-slate-800 dark:text-slate-100">
            {{ $title }}
        </h2>

        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto">
            {{ $message }}
        </p>

    </div>
</section>

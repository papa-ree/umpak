@props([
    'path',
    'alt'      => '',
    'fallback' => null,
    'lazy'     => true,
])

{{--
    Wrapper gambar dengan CDN URL otomatis dan Alpine error fallback.

    Penggunaan:
        <x-umpak::cdn-img path="images/foto.jpg" alt="Foto" class="w-full" />
        <x-umpak::cdn-img path="images/foto.jpg" fallback="images/placeholder.jpg" />

    Catatan:
        - Gunakan path relatif. CDN URL digenerate otomatis via cdn_asset().
        - Jika path sudah full URL (http/https), digunakan langsung tanpa cdn_asset().
        - Fallback opsional — jika tidak diset, img tersembunyi saat error.
--}}
<img
    x-data="{
        error: false,
        onError() {
            this.error = true;
        }
    }"
    :class="{ 'hidden': error && {{ $fallback ? 'false' : 'true' }} }"
    x-on:error="onError"
    src="{{ str_starts_with($path, 'http') ? $path : cdn_asset($path) }}"
    @if($fallback)
        x-bind:src="error ? '{{ str_starts_with($fallback, 'http') ? $fallback : cdn_asset($fallback) }}' : '{{ str_starts_with($path, 'http') ? $path : cdn_asset($path) }}'"
    @endif
    alt="{{ $alt }}"
    @if($lazy) loading="lazy" decoding="async" @endif
    {{ $attributes }}
>

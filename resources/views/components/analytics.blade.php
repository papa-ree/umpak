@php $id = umpak_config('balystics_id'); @endphp

@if($id)
    <script
        defer
        src="https://balystics.ponorogo.go.id/script.js"
        data-website-id="{{ $id }}"
    ></script>
@endif

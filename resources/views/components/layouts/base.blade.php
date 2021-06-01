<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ trans('filament::layout.direction') == 'rtl' ? 'rtl' : 'ltr' }}"
    class="antialiased"
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <script src="https://unpkg.com/tailwindcss-jit-cdn"></script>

        @livewireStyles
    </head>

    <body>
        {{ $slot }}

        @livewireScripts
    </body>
</html>
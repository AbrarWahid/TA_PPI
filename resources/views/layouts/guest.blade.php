<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PBL Portal — AE Polman Bandung') }}</title>

        <script src="https://cdn.tailwindcss.com"></script>
        @include('layouts.partials.design-system')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-[#0b0b0c] antialiased bg-[#fafafa]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 p-6">
            <div class="mb-4">
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-[#013981] flex items-center justify-center text-white font-mono text-xs font-semibold">
                        AE
                    </div>
                    <span class="font-semibold text-base text-[#0b0b0c] tracking-tight">PBL Portal</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-7 py-6 bg-white border border-[#e4e4e7] shadow-surface-md overflow-hidden rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

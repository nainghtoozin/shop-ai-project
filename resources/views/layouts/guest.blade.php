<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ setting('site_name', config('app.name', 'Laravel')) }}</title>

        @php($favicon = setting('site_favicon'))
        @if ($favicon && \Illuminate\Support\Facades\Storage::disk('public')->exists($favicon))
            <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center pt-4 bg-light">
            <div class="mb-3">
                <a href="/">
                    <x-application-logo class="w-20 h-20 text-secondary" />
                </a>
            </div>

            <div class="w-100" style="max-width: 400px;">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="base-url" content="{{ url('/') }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- favicon -->
        <link rel="shortcut icon" href="{{ asset('public/images/logos/icon-logo.svg') }}" type="image/x-icon">

        <!-- Fonts -->
        {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap"> --}}

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('public/css/master.css') }}">


        <!-- Scripts -->
        @routes
        <script src="{{ asset('public/js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        @inertia

        {{-- @env ('local')
            <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
        @endenv --}}
    </body>
</html>

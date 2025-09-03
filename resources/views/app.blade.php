<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel OAuth PKCE SPA') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Vuetify CSS (if using CDN, optional) -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/vuetify@3.3.6/dist/vuetify.min.css" rel="stylesheet"> -->

    <!-- Your compiled CSS -->
    @vite('resources/js/app.js')
</head>
<body>
    <div id="app"></div>

    <!-- Vue & JS are compiled via Vite -->
    @vite('resources/js/app.js')
</body>
</html>
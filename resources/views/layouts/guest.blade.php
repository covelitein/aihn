<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite([
        'resources/css/app.scss',
        'resources/js/app.js',
        'resources/assets/vendor/scss/core.scss',
        'resources/assets/vendor/css/demo.css',
        'resources/assets/vendor/js/core.js',
    ])
</head>

<body class="">
    <div class="container-fluid">
        <div class="row d-flex justify-content-center align-items-center"
            style="min-height: 100vh; background-color: #047;">
            <div class="col-md-4 bg-white p-5 rounded">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>

</html>
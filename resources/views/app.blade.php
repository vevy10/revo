<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Revo est une application de gestion de station-service pour piloter les ventes carburant, la boutique, les encaissements et les accès par rôle.">
        <meta name="robots" content="index, follow">
        <meta name="theme-color" content="#1d4ed8">
        <meta name="application-name" content="{{ config('app.name', 'Revo') }}">
        <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Revo') }}">

        <link rel="canonical" href="{{ url()->current() }}">

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name', 'Revo') }}">
        <meta property="og:title" content="{{ config('app.name', 'Revo') }} - Gestion de station-service">
        <meta property="og:description" content="Gestion sécurisée des opérations de station-service avec rôles, invitations et activation de compte.">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ rtrim(config('app.url'), '/') }}/logo.png">
        <meta property="og:locale" content="fr_FR">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ config('app.name', 'Revo') }} - Gestion de station-service">
        <meta name="twitter:description" content="Gestion sécurisée des opérations de station-service avec rôles, invitations et activation de compte.">
        <meta name="twitter:image" content="{{ rtrim(config('app.url'), '/') }}/logo.png">

        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "SoftwareApplication",
                "name": "{{ config('app.name', 'Revo') }}",
                "applicationCategory": "BusinessApplication",
                "operatingSystem": "Web",
                "description": "Application de gestion de station-service avec authentification par rôles, invitations sécurisées et activation de compte.",
                "image": "{{ rtrim(config('app.url'), '/') }}/logo.png",
                "url": "{{ url()->current() }}"
            }
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'Laravel') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>

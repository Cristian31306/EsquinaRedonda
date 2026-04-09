<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        
        <!-- PWA Meta tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#0284c7">
        <link rel="apple-touch-icon" href="/favicon.png">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').then(registration => {
                        console.log('PWA ServiceWorker registrado correctamente:', registration.scope);
                    }).catch(error => {
                        console.log('Fallo al registrar PWA ServiceWorker:', error);
                    });
                });
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased text-slate-900 bg-slate-50">
        @inertia
    </body>
</html>

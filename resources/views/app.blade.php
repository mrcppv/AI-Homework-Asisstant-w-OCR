<!DOCTYPE html>
<html class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-16698646370"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'AW-16698646370');
    </script>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    {{-- Inertia --}}
    <script
        src="https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign"
        defer></script>

    {{-- EasyAce AI --}}
    <script src="https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?features=String.prototype.startsWith" defer>
    </script>

    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body class="font-sans leading-none text-gray-700 antialiased">
    @inertia
</body>

</html>

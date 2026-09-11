<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ariel Kid's — Boutique de mode pour enfants, femmes et hommes en Côte d'Ivoire.">
    <title inertia>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="/images/brand/logo-bag.png">
    @routes
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>

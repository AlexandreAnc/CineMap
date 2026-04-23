<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CineMap</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex flex-col items-center justify-center gap-6 bg-gray-100 text-gray-900">
    <h1 class="text-3xl font-semibold">CineMap</h1>
    <p class="text-gray-600">Lieux de tournage</p>
    <div class="flex gap-4">
        <a href="{{ route('login') }}" class="px-4 py-2 bg-white border border-gray-300 rounded shadow-sm hover:bg-gray-50">Connexion</a>
        <a href="{{ route('register') }}" class="px-4 py-2 bg-gray-800 text-white rounded shadow-sm hover:bg-gray-700">Inscription</a>
    </div>
</body>
</html>

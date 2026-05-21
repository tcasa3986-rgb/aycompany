<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    {{-- Tailwind por CDN (modo sin Node) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                farmacia: {50:'#ecfdf6',100:'#d1fae9',200:'#a7f0d4',300:'#6fe0bb',400:'#3fc99e',500:'#22b388',600:'#199172',700:'#16735c',800:'#155b4b',900:'#114a3e'},
                topbar:'#2a8f88', sidebar:'#46b8a4'
            } } }
        }
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .btn-primary { @apply inline-flex items-center justify-center rounded-lg bg-farmacia-500 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-farmacia-600 transition; }
            .input       { @apply block w-full rounded-lg border border-gray-200 bg-white text-sm shadow-sm focus:border-farmacia-400 focus:ring-farmacia-400 px-3 py-2; }
            .label       { @apply block text-sm font-medium text-gray-700 mb-1; }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-farmacia-500 via-farmacia-400 to-emerald-300 font-sans p-4">

    <div class="w-full max-w-md">
        <div class="text-center mb-6">
            <div class="inline-flex h-16 w-16 rounded-2xl bg-white/30 items-center justify-center text-3xl text-white font-bold backdrop-blur">
                F+
            </div>
            <h1 class="mt-4 text-2xl text-white font-semibold">{{ config('app.name') }}</h1>
            <p class="text-white/80 text-sm">Sistema de Gestión Farmacéutica</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Iniciar sesión</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-rose-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="label" for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="input" placeholder="usuario@farmacia.test">
                </div>

                <div>
                    <label class="label" for="password">Contraseña</label>
                    <input id="password" type="password" name="password" required class="input" placeholder="••••••••">
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded text-farmacia-500 focus:ring-farmacia-400">
                        <span class="ml-2">Recuérdame</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary w-full">Ingresar</button>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-xs text-gray-500">
                <p class="font-semibold text-gray-700 mb-1">Credenciales demo:</p>
                <p>admin@farmacia.test / password</p>
                <p>cajero@farmacia.test / password</p>
                <p>farmaceutico@farmacia.test / password</p>
            </div>
        </div>
    </div>

</body>
</html>

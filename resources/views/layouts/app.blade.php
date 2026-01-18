<!-- layouts/app.blade.php -->
<html class="dark">
<head>
    <title>Campaign Compendium</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans flex flex-col min-h-screen">
    @include('layouts.navigation')

    @if (session('success'))
        <div class="max-w-3xl mx-auto mt-4 mb-6 px-4">
            <div class="mb-4 p-3 bg-green-100 text-green-800 border border-green-300 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('info'))
        <div class="max-w-3xl mx-auto mb-6 px-4">
            <div class="mb-4 p-3 bg-blue-100 text-blue-800 border border-blue-300 rounded">
                {{ session('info') }}
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-3xl mx-auto mb-6 px-4">
            <div class="mb-4 p-3 bg-red-100 text-red-800 border border-red-300 rounded">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    @hasSection('hero')
        @yield('hero')
    @endif

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-12 py-6 text-center text-gray-500">
        © {{ date('Y') }} Campaign Compendium. All rights reserved.
    </footer>
</body>
</html>

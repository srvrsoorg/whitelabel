<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.title', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css'])
</head>
<body>
    <div id="app">
        <nav class="bg-white shadow">
            <div class="container mx-auto px-4 py-2 flex justify-between items-center">
                <a class="text-xl font-bold text-gray-800" href="{{ url('/') }}">
                    {{ config('app.title', 'Laravel') }}
                </a>
                <div class="flex items-center space-x-4">
                    @guest
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Login
                            </a>
                        @endif

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-gray-800 hover:text-gray-600 px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Register
                            </a>
                        @endif
                    @else
                        <div class="dropdown">
                            <button class="text-gray-800 hover:text-gray-600 font-medium px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu absolute right-0 mt-4 py-1 shadow-md rounded-md bg-white z-10" aria-labelledby="dropdownMenuButton1">
                                <li>
                                    <a href="{{ route('whitelabel.logout') }}" class="dropdown-item block text-sm px-4 py-2 text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('whitelabel.logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>

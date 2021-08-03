<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MS Graph API</title>
    <link rel='stylesheet' href="{{ asset('css/app.css') }}">
</head>
<body class="bg-gray-200">
  <nav class="p-6 bg-white flex justify-between mb-6">
    <ul class="flex item-center">
        <li>
            <a href="/" class="p-3">Home</a>
        </li>
        @auth
            <li>
                <a href="{{ route('dashboard') }}" class="p-3">Dashboard</a>
            </li>
            <li>
                <a href="{{ route('people') }}" class="p-3">People</a>
            </li>
            <li>
                <a href="{{ route('calendar') }}" class="p-3">Calendar</a>
            </li>
        @endauth
    </ul>

    <ul class="flex item-center">
        @auth
            <li>
                <a href="" class="p-3">{{ Auth()->user()->name }}</a>
            </li>
            <li>
                <form action="{{ route('logout') }}" method="post" class="p-3 inline">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        @endauth
        @guest
            
        
            <li>
                <a href="{{ route('login') }}" class="p-3">Login</a>
            </li>
    
        @endguest

    </ul>
  </nav>
    @yield('content')
</body> 
</html>
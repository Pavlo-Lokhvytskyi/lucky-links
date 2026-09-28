<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="page">
        <div class="brand">
            <span class="brand__icon">🍀</span>
            <h1>@yield('title', config('app.name'))</h1>
        </div>

        <div class="card">
            @if (session('status'))
                <div class="alert alert--status">{{ session('status') }}</div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>

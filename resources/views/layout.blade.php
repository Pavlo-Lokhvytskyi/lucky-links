<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', config('app.name'))</title>
</head>
<body>
    <h1>@yield('title', config('app.name'))</h1>

    @if (session('status'))
        <p><strong>{{ session('status') }}</strong></p>
    @endif

    @yield('content')
</body>
</html>

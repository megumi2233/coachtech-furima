<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="header-logo">
                <img src="{{ asset('images/logo.png') }}" alt="COACHTECH" class="header-logo-img">
            </a>
        </div>
    </header>

    <main class="main">
        <div class="main-inner">
            @if (isset($title))
                <h2 class="main-title">{{ $title }}</h2>
            @endif

            @yield('content')
        </div>
    </main>
</body>

</html>

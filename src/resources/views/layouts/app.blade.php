<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <a class="header-logo" href="/">
                <img src="{{ asset('images/logo.png') }}" alt="COACHTECH" class="header-logo-img">
            </a>

            <form class="header-search" action="/" method="get">
                @csrf
                <input class="search-input" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
            </form>

            <nav class="header-nav">
                <ul class="header-nav-list">
                    @guest
                        <li class="header-nav-item">
                            <a class="header-nav-link" href="/login">ログイン</a>
                        </li>
                    @endguest

                    @auth
                        <li class="header-nav-item">
                            <form action="/logout" method="post">
                                @csrf
                                <button class="header-nav-link btn-logout" type="submit">ログアウト</button>
                            </form>
                        </li>
                    @endauth

                    <li class="header-nav-item">
                        <a class="header-nav-link" href="/mypage">マイページ</a>
                    </li>

                    <li class="header-nav-item">
                        <a class="header-nav-button" href="/sell">出品</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main">
        @yield('content')
    </main>
</body>

</html>
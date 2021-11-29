<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->

    <style>
        .bordered_input input {
            border: 1px solid black;
        }

        .error {
            color: #bf0000;
            font-style: italic;
        }

        .flex-container {
            display: -webkit-flex;
            display: flex;
            -webkit-flex-direction: row;
            flex-direction: row;
            justify-content: flex-end;
            align-items: center;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

    </style>

</head>
<body>
@if (session()->get('suggest') === true)
    <script type="text/javascript">
        let isAccept = confirm("Хотите ли вы оставить обратную связь о нас?");
        if (isAccept) {
            let url = new URL("{{ route('appeal') }}");
            url.searchParams.set('accept', '1');
            window.location.href = url;
        }
    </script>
@endif
<div class="flex-container" style="background: black; height: 70px;">
    @if(auth()->user() === null)
        <div style="color: white; font-size: 20px; margin: 20px;"><a href="{{ route('login') }}">Вход</a></div>
        <div style="color: white; font-size: 20px; margin: 20px;"><a href="{{ route('registration') }}">Регистрация</a>
        </div>
    @else
        <div style="color: white; size: 20px; margin: 20px; font-size: 20px;"><a href="{{ route('profile') }}">Профиль</a></div>
        <div style="color: white; size: 20px; margin: 20px; font-size: 20px;"><a href="{{ route('logout') }}">Выход</a></div>
    @endif
</div>
@yield('content')
</body>
</html>

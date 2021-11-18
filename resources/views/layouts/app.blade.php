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
@yield('content')
</body>
</html>

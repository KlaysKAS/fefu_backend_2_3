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
<div>
    <h2>Обращение к нам</h2>
    @if ($success)
        <p>Обращение успешно отправлено!</p>
    @endif
    <form class="bordered_input" method="POST" action="{{ route('appeal') }}">
        @csrf
        <div>
            <label>Имя</label>
            <br>
            <input name="name" type="text" value="{{ request()->isMethod('post') ? old('name') : '' }}" maxlength="20"/>
            @if (isset($errors['name']))
                <p class="error">{{ $errors['name'] }}</p>
            @endif
        </div>
        <div>
            <label>Телефон в формате 89991110000</label>
            <br>
            <input name="phone" type="tel" value="{{ request()->isMethod('post') ? old('phone') : '' }}" maxlength="11"
                   pattern="8[0-9]{10}"/>
            @if (isset($errors['phone']))
                <p class="error">{{ $errors['phone'] }}</p>
            @endif
        </div>
        <div>
            <label>Почта</label>
            <br>
            <input name="email" type="email" value="{{ request()->isMethod('post') ? old('email') : '' }}"
                   maxlength="100"/>
            @if (isset($errors['email']))
                <p class="error">{{ $errors['email'] }}</p>
            @endif
        </div>
        <div>
            <label>Сообщение</label>
            <br>
            <textarea name="message" value="{{ request()->isMethod('post') ? old('message') : '' }}"
                      rows="5"></textarea>
            @if (isset($errors['message']))
                <p class="error">{{ $errors['message'] }}</p>
            @endif
        </div>

        <input type="submit"/>

    </form>
</div>
</body>
</html>

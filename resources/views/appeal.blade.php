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
            <input name="name" type="text" value="{{ $errors->any() ? old('name'): '' }}"/>
            @error('name')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Фамилия</label>
            <br>
            <input name="surname" type="text" value="{{ $errors->any() ? old('surname') : '' }}"/>
            @error('surname')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Отчество</label>
            <br>
            <input name="patronymic" type="text" value="{{ $errors->any() ? old('patronymic') : '' }}"/>
            @error('patronymic')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Возраст</label>
            <br>
            <input name="age" type="number" value="{{ $errors->any() ? old('age') : '' }}"/>
            @error('age')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Пол</label>
            <br>
            <select name="gender">
                <option value="0" {{ $errors->any() && old('gender') == 0 ? 'selected="selected"': ''}}>Male</option>
                <option value="1" {{ $errors->any() && old('gender') == 1 ? 'selected="selected"': ''}}>Female</option>
            </select>
            @error('gender')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Телефон в формате +7 (000) 00-00-000 или 8 (000) 00-00-000</label>
            <br>
            <input name="phone" type="text" value="{{ $errors->any() ? old('phone') : '' }}"/>
            @error('phone')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Почта</label>
            <br>
            <input name="email" type="email" value="{{ $errors->any() ? old('email') : '' }}"/>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Сообщение</label>
            <br>
            <textarea name="message" rows="5">{{ $errors->any() ? old('message') : '' }}</textarea>
            @error('message')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <input type="submit"/>

    </form>
</div>
</body>
</html>

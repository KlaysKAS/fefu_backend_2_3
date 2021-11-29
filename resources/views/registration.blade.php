@extends('layouts.app')

@section('content')
    <h2>Регистрация</h2>
    <form class="bordered_input" method="POST" action="{{ route('registration') }}">
        @csrf
        <div>
            <label>Имя (логин), будет использоваться при авторизации</label>
            <br>
            <input name="name" type="text" value="{{ $errors->any() ? old('name'): '' }}"/>
            @error('name')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label>Пароль</label>
            <br>
            <input name="password" type="password">
            @error('password')
            <p class="error">{{ $message }}</p>
            @enderror
        </div>
        <br>
        <input type="submit"/>

    </form>
@endsection

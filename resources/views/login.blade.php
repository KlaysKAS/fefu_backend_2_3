@extends('layouts.app')

@section('content')
    <h2>Логин</h2>
    <form class="bordered_input" method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label>Имя (логин)</label>
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
        <div>
            <label>Запомнить?</label>
            <input type="hidden" name="remember" value="0">
            <input type="checkbox" name="remember" value="1"/>
        </div>
        <br>
        <input type="submit"/>

    </form>
@endsection

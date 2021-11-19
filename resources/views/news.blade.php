@extends('layouts.app')

@section('content')
    <a href="{{ route('news_list') }}">Новости</a>
    <h1>{{ $news_item->title }}</h1>
    <p>{{ $news_item->published_at }}</p>
    <p>{{ $news_item->text }}</p>
@endsection

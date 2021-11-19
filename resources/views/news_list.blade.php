@extends('layouts.app')

@section('content')
    <h1>Список новостей</h1>
    @foreach($news as $i => $item )
        @if ($i > 0)
            <hr>
        @endif
        <a href="{{ route('news_item', ['slug' => $item->slug]) }}">{{ $item->title }}</a>
        <p>{{ $item->published_at }}</p>
        @if($item->description !== null)
            <p>{{ $item->description }}</p>
        @endif


    @endforeach
    {{ $news->links() }}
@endsection

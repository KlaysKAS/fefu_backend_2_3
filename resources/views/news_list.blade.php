<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->

</head>
<body>
<h1>Список новостей</h1>
@foreach($news as $i => $item )
    @if ($i > 0)
        <hr>
    @endif
    <a href="{{ route('news_item', ['slug' => $item->slug]) }}"><h2>{{ $item->title }}</h2></a>
    <p>{{ $item->published_at }}</p>
    @if($item->description !== null)
        <p>{{ $item->description }}</p>
    @endif
    

@endforeach
{{ $news->links() }}
</body>
</html>

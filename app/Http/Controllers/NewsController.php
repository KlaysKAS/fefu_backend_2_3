<?php

namespace App\Http\Controllers;

use App\Models\News;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function MongoDB\BSON\toJSON;

class NewsController extends Controller
{
    public function getList() {
        $news_list =  News::query()
            ->where('is_published', true)
            ->where('published_at', '<=', Carbon::now())
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->simplePaginate(5);

        return view('news_list', ['news' => $news_list]);
    }

    public function getDetails(string $slug) {
        $news = News::query()->where('slug', '=', $slug)
            ->where('is_published', '=', true)
            ->where('published_at', '<=', Carbon::now())
            ->first();
        if ($news === null)
            abort(404);
        return view('news', ['news_item' => $news]);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\SuggestSettings;
use Closure;
use Illuminate\Http\Request;

class SuggestAppeal
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $settings = app(SuggestSettings::class);

        if ($request->session()->missing('appeal')) {
            $request->session()->put('appeal', false);
        }

        if ($request->session()->missing('show_count')) {
            $request->session()->put('show_count', 0);
            $request->session()->put('time_to_show', 0);
        }

        if (!$request->session()->get('appeal') &&
            $request->session()->get('show_count') < $settings->maximum) {
            if ($request->session()->get('time_to_show') < $settings->frequency) {
                $request->session()->increment('time_to_show');
            }
            else {
                $request->session()->now('suggest', true);
                $request->session()->put('message', true);
                $request->session()->increment('show_count');
                $request->session()->put('time_to_show', 0);
            }
        }
        return $next($request);
    }
}

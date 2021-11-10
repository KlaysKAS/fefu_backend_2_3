<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;

class RedirectFromOldSlug
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $url = parse_url($request->url(), PHP_URL_PATH);

        $redirect = Redirect::where('old_slug', $url)->orderByDesc('created_at')->orderByDesc('id')->first();
        $redirectTo = null;

        while ($redirect !== null) {
            $redirectTo = $redirect;
            $redirect = Redirect::where('old_slug', $redirect->slug)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();
        }

        if ($redirectTo !== null) {
            return redirect($redirectTo->new_slug);
        }

        return $next($request);
    }
}

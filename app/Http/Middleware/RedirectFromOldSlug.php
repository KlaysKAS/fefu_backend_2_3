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
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $url = $request->url();
        $posLastSlash = strripos($url, '/', -1);
        $posPrevSlash = strripos($url, '/', $posLastSlash - strlen($url) - 1);
        $baseUrl = substr($url, $posPrevSlash + 1, $posLastSlash - $posPrevSlash - 1);
        $url = substr($url, $posLastSlash + 1);

        $is_redirecting = false;
        $current = Redirect::where('new_slug', $url)->orderByDesc('created_at')->orderByDesc('id')->first();
        $temp_redirect = Redirect::where('old_slug', $url)->orderByDesc('created_at')->orderByDesc('id')->first();
        if ($current !== null) {
            $redirect = $current;
        } else {
            $redirect = $temp_redirect;
        }

        while ($temp_redirect !== null && $temp_redirect->created_at >= $redirect->created_at) {
            $redirect = $temp_redirect;
            $is_redirecting = true;
            $temp_redirect = Redirect::where('old_slug', $redirect->slug)
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->first();
        }

        if ($redirect !== null && $is_redirecting) {
            return redirect($baseUrl . "/" . $redirect->new_slug);
        }

        return $next($request);
    }
}

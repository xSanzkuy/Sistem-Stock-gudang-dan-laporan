<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SavePaginationPage
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->has('page')) {
            session(['pagination_page' => $request->page]);
        }

        return $next($request);
    }
}

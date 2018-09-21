<?php

namespace Kodilab\LaravelI18n\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Callback
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response)
        {
            if ($request->has('_callback'))
            {
                return redirect($request->input('_callback'));
            }
        }

        return $response;
    }
}

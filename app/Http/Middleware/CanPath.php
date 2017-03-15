<?php

namespace App\Http\Middleware;

use Closure;
use Entrust;

class CanPath
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if ($request->is('api' . $permission) and Entrust::can($permission)) {
            return $next($request);
        }

        abort(403, '无权访问资源');
    }
}

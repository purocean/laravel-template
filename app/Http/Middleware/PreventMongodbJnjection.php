<?php

namespace App\Http\Middleware;

use Closure;

class PreventMongodbJnjection
{
    /**
     * The path that should not be handle.
     *
     * @var array
     */
    protected $except = [
        // 'api/test/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach ($this->except as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        $transform = function ($value) use (&$transform) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if (is_string($k)) {
                        unset($value[$k]);
                        $value[str_replace(['$', chr(0)], '', $k)] = $transform($v);
                    }
                }
            }

            return $value;
        };

        $request->replace($transform($request->all()));

        return $next($request);
    }
}

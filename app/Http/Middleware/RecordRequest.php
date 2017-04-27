<?php

namespace App\Http\Middleware;

use Closure;
use App\RequestLog;

class RecordRequest
{
    protected $except = [
        'api/qrcode',
        'api/limits',
        'api/qrcode',
        'api/qrlogin',
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
        $response = $next($request);

        $flag = true;
        foreach ($this->except as $path) {
            if ($request->is($path)) {
                $flag = false;
                break;
            }
        }

        if ($flag) {
            $model = new RequestLog;
            $model->url = $request->fullUrl();
            $model->data = $request->all();
            $model->user = $request->user()->attributesToArray();
            $model->save();
        }

        return $response;
    }
}

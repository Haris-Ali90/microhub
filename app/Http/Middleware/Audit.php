<?php

namespace App\Http\Middleware;

use App\ApiLogs;
use Closure;
use Illuminate\Support\Facades\Auth;


class Audit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */

    public static $method_type_t = '';

    public function handle($request, Closure $next, $method_type = '')
    {
        self::$method_type_t = $method_type;

        if (str_contains($_SERVER['REQUEST_URI'], 'logout')){
            $log = new ApiLogs();
            $log->user_id =  Auth::user()->id;
            $log->url = $request->url();
            $log->method = $request->method();
            $log->source_ip = $_SERVER['REMOTE_ADDR'];
            $log->destination_ip = $_SERVER['SERVER_ADDR'];
            $log->request_date = date('Y-m-d H:i:s');
            $log->request_data = json_encode($_REQUEST);
            $log->type = ApiLogs::RESPONSE_LOGOUT;
            $log->save();
        }


        return $next($request);

    }


/*    public function terminate($request,$response)
    {
        if (self::$method_type_t == $request->method()) {
            if (!str_contains($_SERVER['REQUEST_URI'], 'logout')) {
                $log = new ApiLogs();
                $log->user_id = Auth::user()->id;
                $log->url = $request->url();
                $log->method = $request->method();
                $log->source_ip = $_SERVER['REMOTE_ADDR'];
                $log->destination_ip = $_SERVER['SERVER_ADDR'];
                $log->request_date = date('Y-m-d H:i:s');
                $log->request_data = json_encode($_REQUEST);
                $log->type = ApiLogs::REQUEST_LOGIN;
                $log->save();
            }
        }
    }*/
}

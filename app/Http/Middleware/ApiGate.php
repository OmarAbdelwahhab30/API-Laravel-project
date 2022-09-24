<?php

namespace App\Http\Middleware;

use Closure;

class ApiGate // for check api password
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if( $request->api_password !== env('API_PASSWORD','jbfJtIAGwkc4QCmIhf6PkyOzFDMfns2LNMRhEI5hc')){
            return response()->json(['message' => 'Unauthenticated.']);
        }
        return $next($request);
    }
}

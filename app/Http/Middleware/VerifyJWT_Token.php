<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiHandler;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyJWT_Token
{
    use ApiHandler;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = $request->token;
            if (!empty($token)) {
                    $request->headers->set('token', (string)$token, true);
                    $request->headers->set('Authorization', 'Bearer ' . $token, true);
                    $user = JWTAuth::parseToken()->authenticate();
                }else{
                     return $this->returnError("",'Token is null');
                 }
            } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError("",'Token is Invalid');
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError("",'Token is Expired');
            }else{
                return $this->returnError("",'Authorization Token not found');
            }
        }
        return $next($request);
    }
}

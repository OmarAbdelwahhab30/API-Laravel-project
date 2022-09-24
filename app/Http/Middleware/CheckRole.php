<?php

namespace App\Http\Middleware;

use App\Http\Controllers\API\AuthController;
use App\Http\Requests\ValidateJWT_Token;
use App\Http\Traits\ApiHandler;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    use ApiHandler;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,...$roles)
    {
        $auth = new AuthController();
        $req = new ValidateJWT_Token();
        $req->token = $request->token;
        if (!$auth->CheckUserRole($req,$roles)){
             return $this->returnError("","unauthenticated");
        }
        return $next($request);
    }
}

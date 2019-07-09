<?php

namespace App\Http\Middleware;

use App\Common\Auth\JWTAuth;
use App\Http\Response\ResponseJson;
use Closure;

class JWTCheck
{
    use ResponseJson;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->input('token');
        if(!is_null($token)){
            $jwt = JWTAuth::getInstance();
            if(!$jwt->validate($token) || !$jwt->verify($token)) {
                $this->error('token过期');
            }
            return $next($request);
        }else{
            $this->error(1000,'token不能为空');
        }
    }
}

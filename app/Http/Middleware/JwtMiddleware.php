<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            $response = [
                'status' => false,
                'code' => 401,
                'message' => 'Token not valid !',
            ];
            return response()->json($response, 401);
        }

        return $next($request);
    }
}

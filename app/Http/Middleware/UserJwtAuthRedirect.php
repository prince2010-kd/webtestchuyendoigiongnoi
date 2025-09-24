<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Laravel\Prompts\Key;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserJwtAuthRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            $token = $request->cookie('token');
            if (!$token) {
                return redirect()->route('login');
            }
            $user = JWTAuth::setToken($token)->authenticate();
            if(!$user)
            {
                return redirect()->route('login');
            } 
            auth()->setUser($user);
            Log::info(JWTAuth::user());
        }catch(Exception $e)
        {
            return redirect()->route('login');
        }
        return $next($request);
    }
}

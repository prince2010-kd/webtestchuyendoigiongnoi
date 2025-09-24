<?php

namespace App\Http\Middleware;

use App\Models\RefreshToken;
use App\Models\User;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRefreshToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('token');
        $refreshToken = $request->cookie('refresh_token');
        if(!$token)
        {
            return redirect()->route('login');
        }

        try{
            //xác thực token
            JWTAuth::setToken($token)->authenticate();
        }
        catch(TokenExpiredException $e)
        {
            //Nếu token bị hết hạn, tạo mới  
            if(!$refreshToken)
            {
                return redirect()->route('login');
            }
            // Từ refresh token, tìm bản ghi để lấy ra userid
            $record = RefreshToken::where('token', $refreshToken)
                                    ->where('expires_at', '>', now())
                                    ->first();
            if(!$record) return redirect()->route('login');
            $user = User::find($record->user_id);
            if (!$user) {
                return redirect()->route('login');
            }
            //Từ user, tạo access token mới cho usẻ
            $newToken = JWTAuth::fromUser($user);
            $newAccessCookie = cookie('token', $newToken, 15, '/', null, false, true);
            $response = $next($request);
            return $response->withCookie($newAccessCookie);
        } catch(TokenInvalidException | JWTException $e)
        {
            Log::info("invalid token");
            // Token không hợp lệ hoặc không tồn tại
            return redirect()->route('login');
        }
        auth()->setUser(JWTAuth::user());
        return $next($request);
    }
}

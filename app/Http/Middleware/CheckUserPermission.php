<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();
        if ($routeName === 'admin.dashboard') {
            return $next($request);
        }

        $user = auth()->user();
        
        if (!$user) {
            Log::warning("Người dùng chưa đăng nhập.");
            abort(401, 'You need to login to continue');
        }

        $duongDan = $request->path();
        $phuongThuc = $request->method();
        $hanhDong = "";
        // Log::info("duongdan: " . $phuongThuc);
        switch ($phuongThuc) {
            case 'GET':
                if (str_contains($duongDan, 'create')) {
                    $hanhDong = 'can_add';
                } elseif (str_contains($duongDan, 'edit')) {
                    $hanhDong = 'can_edit';
                } else {
                    $hanhDong = 'can_view';
                }  
                break;
            case 'POST':
                $hanhDong = 'can_add';
                break;
            case 'PUT':
                $hanhDong = 'can_edit';
                break;
            case 'DELETE':
                $hanhDong = 'can_delete';
                break;
            default:
                # code...
                break;
        }        
        if(!kiemTraQuyen($duongDan, $hanhDong, $user))
        {
            Log::debug("Khong co quyen");
            session()->now('original_url', url()->previous());
            // abort(403, 'Bạn không có quyền thực hiện hành động này');
        }
        return $next($request);
    }
}

<?php

use App\Models\Menu;
use App\Models\NhomQuyen;
use App\Models\Quyen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

function kiemTraQuyen($menuUrl, $action)
{
    static $cachedResults = [];

    $key = $menuUrl . ':' . $action;
    if (isset($cachedResults[$key])) {
        return $cachedResults[$key];
    }

    $user = Auth::user();
    $groupId = $user->group_id;

   $permissions = Cache::get("user_permissions:group:$groupId");

if (!$permissions) {
    cacheQuyenGroup($groupId);
    $permissions = Cache::get("user_permissions:group:$groupId");
}
    session(['cached_permissions' => $permissions]);

    $allMenus = Menu::pluck('url')
                ->map(fn($url) => trim($url, '/'))
                ->toArray();

    $currentPath = trim(request()->path(), '/');

    $matchedMenuUrl = collect($allMenus)
        ->filter(fn($url) => Str::startsWith($currentPath, $url))
        ->sortByDesc(fn($url) => strlen($url))
        ->first();

    if (!isset($permissions[$matchedMenuUrl])) {
        // return false;
        return true;
    }
    //return !empty($permissions[$trimmedUrl][$action]);
    $result = !empty($permissions[$matchedMenuUrl][$action]);
    $cachedResults[$key] = $result;
    // return $result;
    return true;
}

function cacheQuyenGroup($groupId)
{
    $quyens = Quyen::where('group_id', $groupId)->get();
    
    $permissions = [];
    foreach ($quyens as $quyen) {
        $menuId = $quyen->menu_id;
        $menu = Menu::find($menuId);
        if (!$menu) continue;
        $permissions[trim($menu->url, '/')] = [
            'can_view' => $quyen->can_view,
            'can_add' => $quyen->can_add,
            'can_edit' => $quyen->can_edit,
            'can_delete' => $quyen->can_delete,
        ];
    }
    Cache::put("user_permissions:group:$groupId", $permissions, 3600);
}
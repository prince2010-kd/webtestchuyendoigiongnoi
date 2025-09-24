<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MenuHelper
{

    public static function dataMenuBackend($userId)
    {
        $groupId = optional(auth()->user())->group_id;
        if (!$groupId) {
            return [];
        }

       $permissions = Cache::get("user_permissions:group:$groupId");

if (!$permissions) {
    cacheQuyenGroup($groupId);
    $permissions = Cache::get("user_permissions:group:$groupId");
}

if (!$permissions) {
    return [];
}


        // Load tất cả menu
        $menus = DB::table('menus')
            ->where('active', 1)
            ->orderBy('stt', 'asc')
            ->get();

        // Tạo map id => menu
        $menuMap = $menus->keyBy('id');

        // Lấy các URL được phép xem (can_view = 1)
        $allowedUrls = collect($permissions)
            ->filter(fn($p) => !empty($p['can_view']))
            ->keys()
            ->map(fn($url) => trim($url, '/'))
            ->toArray();

        // Lọc menu có quyền
        $menusWithPermission = $menus->filter(function ($menu) use ($allowedUrls) {
            return in_array(trim($menu->url, '/'), $allowedUrls);
        });

        // Tập hợp menu cuối cùng bao gồm menu có quyền và cha của nó
        $finalMenus = collect();

        foreach ($menusWithPermission as $menu) {
            if (!$finalMenus->contains('id', $menu->id)) {
                $finalMenus->push($menu);
            }

            // Truy ngược lên cha
            $parentId = $menu->parent_id;
            while ($parentId && !$finalMenus->contains('id', $parentId)) {
                $parent = $menuMap->get($parentId);
                if ($parent) {
                    $finalMenus->push($parent);
                    $parentId = $parent->parent_id;
                } else {
                    break;
                }
            }
        }

        //     dd([
//      'group_id' => $groupId,
//      'permissions' => $permissions,
//      'all_menus' => $menus->pluck('url'),
//      'filtered' => $finalMenus->pluck('url'),
//  ]);

        // Trả về mảng đã chuẩn hoá
        return $finalMenus->map(function ($menu) {
            return (array) $menu;
        })->values()->all();
    }



    public static function build_menu_tree($menus, $parent_id = null, $visited = [])
    {
        $tree = [];

        if (empty($menus) || !is_array($menus)) {
            return $tree;
        }

        foreach ($menus as $menu) {
            if (!isset($menu['id'], $menu['parent_id'])) {
                continue;
            }

            if (in_array($menu['id'], $visited)) {
                continue;
            }

            // So sánh chuẩn null với null hoặc int với int
            $is_parent = (is_null($menu['parent_id']) && is_null($parent_id)) ||
                ($menu['parent_id'] === $parent_id);

            if ($is_parent) {
                $visited[] = $menu['id'];
                $children = self::build_menu_tree($menus, $menu['id'], $visited);
                if ($children) {
                    $menu['children'] = $children;
                }
                $tree[] = $menu;
            }
        }
        // print_r($tree);
        return $tree;
    }


    public static function renderMenuBackend($menu_tree, $currentURL, $level = 0)
    {
        $html = '';

        foreach ($menu_tree as $menu) {
            $has_children = isset($menu['children']) && is_array($menu['children']);

            // Chuẩn hóa URL
            $current = trim($currentURL, '/');
            $menuUrl = trim($menu['url'], '/');

            // Nếu menu hiện tại là active
           $is_current = $current === $menuUrl || \Illuminate\Support\Str::startsWith($current, $menuUrl . '/');

            // Nếu có con và một trong các con là active
            $has_active_child = $has_children && self::has_active_child($menu['children'], $currentURL);

            // Xác định class
            $classes = 'menu-item';
            if ($is_current)
                $classes .= ' active';
            if ($has_active_child)
                $classes .= ' open';

            $html .= '<li class="' . $classes . '">';

            // Link cha có children thì không có href thật
            $href = $has_children ? 'javascript:;' : url($menu['url']);
            $html .= '<a href="' . $href . '" class="menu-link px-3' . ($has_children ? ' menu-toggle' : '') . '">';

            // Icon chỉ menu cấp 1 mới hiển thị
            if ($level === 0) {
                $icon = isset($menu['icon']) && $menu['icon'] !== 'null' ? $menu['icon'] : 'bx bx-layout';
                $html .= '<i class="menu-icon tf-icons ' . $icon . '"></i>';
            }

            // Padding trái để thụt vào nếu là cấp con
            $paddingLeft = $level > 0 ? (20 + $level * 15) : 0;
            $style = $paddingLeft > 0 ? ' style="padding-left: ' . $paddingLeft . 'px; margin-left: 6px;"' : '';

            $html .= '<div data-i18n="' . $menu['title'] . '"' . $style . '>' . $menu['title'] . '</div>';
            $html .= '</a>';

            // Menu con
            if ($has_children) {
                $html .= '<ul class="menu-sub">';
                $html .= self::renderMenuBackend($menu['children'], $currentURL, $level + 1);
                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        return $html;
    }



    public static function has_active_child($children, $currentURL)
    {
        $current = trim($currentURL, '/');
        foreach ($children as $child) {
            $childUrl = trim($child['url'], '/');
            if ($current === $childUrl || \Illuminate\Support\Str::startsWith($current, $childUrl . '/')) {
                return true;
            }
            if (isset($child['children']) && self::has_active_child($child['children'], $currentURL)) {
                return true;
            }
        }
        return false;
    }

}

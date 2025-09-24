<?php

namespace App\Helpers;

use App\Models\MenuFrontend;

class MenuHelperFrontend
{
    public static function getMenus($position = 'main')
    {
        $level1 = MenuFrontend::where('position', $position)
            ->whereNull('parent_id')
            ->where('active', 1)
            ->orderBy('stt')
            ->get();

        $level2 = MenuFrontend::where('position', $position)
            ->whereNotNull('parent_id')
            ->where('active', 1)
            ->orderBy('stt')
            ->get()
            ->groupBy('parent_id');

        return [
            'level1' => $level1,
            'level2' => $level2,
        ];
    }

public static function getUrlBySlug($slug)
{
    // Nếu slug bạn truyền vào là **không có dấu "/"**, thì thêm vào
    if (strpos($slug, '/') !== 0) {
        $slug = '/' . $slug;
    }

    $menu = \App\Models\MenuFrontend::where('url', $slug)->first();

    return $menu ? url($menu->url) : '#';
}

}

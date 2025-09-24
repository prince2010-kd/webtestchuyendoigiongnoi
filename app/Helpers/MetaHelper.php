<?php

namespace App\Helpers;

use App\Models\General;

class MetaHelper
{
    public static function generateMetaTags($data = [], $pageKey = null)
    {
        // Trường hợp truyền model Posts
        if ($data instanceof \App\Models\Posts) {
            $post = $data;
            $meta_title = $post->meta_title ?: $post->title;
            $meta_description = $post->meta_description ?: mb_substr(strip_tags($post->short_description), 0, 200);
            $meta_keywords = $post->meta_keywords ?: 'Từ khóa mặc định';
            $meta_new_keyword = $post->meta_new_keyword ?: '';
            $meta_canonical = url()->current();
            $meta_image = $post->image ? asset('storage/' . $post->image) : asset('default-og-image.jpg');

            // Trường hợp truyền pageKey (slug/menu)
        } elseif ($pageKey) {
            $seoConfigs = General::where('group_conf', 'seo')
                ->where('page_key', $pageKey)
                ->pluck('val', 'keyword')
                ->toArray();

            $meta_title = $seoConfigs['meta_title'] ?? 'Tiêu đề mặc định';
            $meta_description = $seoConfigs['meta_description'] ?? 'Mô tả mặc định';
            $meta_keywords = $seoConfigs['meta_keywords'] ?? 'Từ khóa mặc định';
            $meta_new_keyword = $seoConfigs['meta_new_keyword'] ?? '';
            $meta_canonical = url()->current();
            $meta_image = $seoConfigs['meta_image'] ?? asset('default-og-image.jpg');

            // Trường hợp truyền mảng dữ liệu thô hoặc không truyền gì
        } else {
            // Nếu không truyền data mà cũng không có pageKey, giả định là trang chủ
            if (empty($data) && !$pageKey) {
                $pageKey = 'home';
                $seoConfigs = General::where('group_conf', 'seo')
                    ->where('page_key', $pageKey)
                    ->pluck('val', 'keyword')
                    ->toArray();

                $meta_title = $seoConfigs['meta_title'] ?? 'Tiêu đề mặc định';
                $meta_description = $seoConfigs['meta_description'] ?? 'Mô tả mặc định';
                $meta_keywords = $seoConfigs['meta_keywords'] ?? 'Từ khóa mặc định';
                $meta_new_keyword = $seoConfigs['meta_new_keyword'] ?? '';
                $meta_image = $seoConfigs['meta_image'] ?? asset('default-og-image.jpg');
            } else {
                $meta_title = $data['meta_title'] ?? 'Tiêu đề mặc định';
                $meta_description = $data['meta_description'] ?? 'Mô tả mặc định';
                $meta_keywords = $data['meta_keywords'] ?? 'Từ khóa mặc định';
                $meta_new_keyword = $data['meta_new_keyword'] ?? '';
                $meta_image = $data['meta_image'] ?? asset('default-og-image.jpg');
            }

            $meta_canonical = $data['meta_canonical'] ?? url()->current();
        }

        return [
            'meta_title' => $meta_title,
            'meta_description' => $meta_description,
            'meta_keywords' => $meta_keywords,
            'meta_new_keyword' => $meta_new_keyword,
            'meta_canonical' => $meta_canonical,
            'meta_image' => $meta_image,
        ];
    }
}


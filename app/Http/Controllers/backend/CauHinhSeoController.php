<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General;
use App\Models\MenuFrontend;

class CauHinhSeoController extends Controller
{
    public function index(Request $request)
    {
        $query = General::where('group_conf', 'seo')
            ->whereNotNull('page_key')
            ->selectRaw('page_key, MAX(created) as created_at, MAX(public) as public, MAX(stt) as stt')
            ->groupBy('page_key');

        // Lọc theo từ khóa
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('page_key', 'like', '%' . $keyword . '%');

        }

        // Sắp xếp theo STT
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('stt', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Phân trang
        $pageSize = $request->input('page_size', 10);
        $items = $query->paginate($pageSize)->appends($request->all());

        // Lấy các meta_title
        $metaTitles = General::where('group_conf', 'seo')
            ->where('keyword', 'meta_title')
            ->pluck('val', 'page_key')
            ->toArray();

        // Lấy tên menu (url => title)
        $menuNames = MenuFrontend::pluck('title', 'url')->toArray();

        if ($request->ajax()) {
            $table = view('admin.cauhinhseo.partials._table', compact('items', 'metaTitles', 'menuNames'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $items])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.cauhinhseo.list', compact('items', 'metaTitles', 'menuNames'));
    }


    // Lưu hoặc cập nhật cấu hình SEO
    public function create()
    {
        $menus = MenuFrontend::pluck('title', 'url'); // ['gioi-thieu' => 'Giới thiệu']

        return view('admin.cauhinhseo.form', [
            'formAction' => route('admin.cauhinhseo.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới cấu hình SEO',
            'formTitle' => 'Thêm SEO theo menu',
            'fields' => [],
            'menus' => $menus,
            'page_key' => ''
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_key' => 'required|string',
            'fields.meta_title' => 'required|string',
            'fields.meta_description' => 'nullable|string',
            'fields.meta_keywords' => 'nullable|string',
        ]);

        $data = $request->input('fields');
        $pageKey = ltrim($request->input('page_key', 'home'), '/');


        foreach ($data as $key => $value) {
            General::updateOrCreate(
                [
                    'keyword' => $key,
                    'group_conf' => 'seo',
                    'page_key' => $pageKey
                ],
                [
                    'val' => $value,
                    'label' => ucfirst(str_replace('_', ' ', $key)),
                    'created' => now(),
                    'stt' => General::max('stt') + 1,
                    'public' => 1,
                    'type' => $key === 'meta_description' ? 'textarea' : 'text',
                ]
            );
        }

        return redirect()->route('admin.cauhinhseo.list')->with('success', 'Đã lưu cấu hình SEO thành công.');
    }

    public function edit($id)
    {
        $pageKey = urldecode($id); // Giải mã
        $configs = General::where('group_conf', 'seo')
            ->where('page_key', $pageKey)
            ->get();

        $fields = $configs->pluck('val', 'keyword')->toArray();

        $menus = MenuFrontend::all()->mapWithKeys(function ($item) {
            return [ltrim($item->url, '/') => $item->title];
        })->toArray();

        return view('admin.cauhinhseo.form', [
            'formAction' => route('admin.cauhinhseo.update', $pageKey),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật cấu hình SEO',
            'formTitle' => 'Cập nhật SEO cho menu',
            'fields' => $fields,
            'menus' => $menus,
            'page_key' => $pageKey,
        ]);
    }


    public function update(Request $request, $pageKey)
    {
        $request->validate([
            'fields.meta_title' => 'required|string',
            'fields.meta_description' => 'nullable|string',
            'fields.meta_keywords' => 'nullable|string',
        ]);

        $pageKey = ltrim($pageKey ?: 'home', '/');

        $data = $request->input('fields');

        foreach ($data as $key => $value) {
            General::updateOrCreate(
                [
                    'keyword' => $key,
                    'group_conf' => 'seo',
                    'page_key' => $pageKey,
                ],
                [
                    'val' => $value,
                    'label' => ucfirst(str_replace('_', ' ', $key)),
                    'created' => now(),
                    'stt' => General::max('stt') + 1,
                    'public' => 1,
                    'type' => $key === 'meta_description' ? 'textarea' : 'text',
                ]
            );
        }

        return redirect()->route('admin.cauhinhseo.list')->with('success', 'Cập nhật cấu hình SEO thành công.');
    }

    public function thaydoitrangthai(Request $request, $pageKey)
    {
        $active = $request->input('active') ? 1 : 0;

        // Cập nhật tất cả các bản ghi theo page_key
        General::where('page_key', $pageKey)->update(['public' => $active]);

        return response()->json([
            'message' => $active ? 'Đã bật hiển thị' : 'Đã tắt hiển thị'
        ]);
    }
}

<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuFrontend;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\SortOrderHelper;
class MenuFrontendController extends Controller
{
        public function index(Request $request)
    {
        $query = MenuFrontend::query();

        // Tìm kiếm theo keyword (trên name hoặc des)
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('title', 'like', '%' . $keyword . '%');
        }


        // Sắp xếp theo name hoặc mặc định theo stt
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') == 'desc' ? 'desc' : 'asc';
            $query->orderBy('title', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $menuTree1 = $query->paginate($pageSize)->appends($request->all());

        // Nếu là AJAX thì trả về HTML table + pagination
        if ($request->ajax()) {
            $table = view('admin.menufrontend.partials._table', compact('menuTree1'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $menuTree1])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.menufrontend.list', compact('menuTree1'));
    }

    public function create()
    {
        $parents = MenuFrontend::whereNull('parent_id')->get();
        return view('admin.menufrontend.form', [
            'formAction' => route('admin.menufrontend.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm menu mới',
            'parents' => $parents,
            'fields' => [],
        ]);
    }

//     public function store(Request $request)
// {
//     $request->validate([
//         'title' => 'required|string|max:255',
//         'url' => 'nullable|string|max:255',
//         'parent_id' => 'nullable|exists:menus_frontend,id',
//         'position' => 'nullable|string|max:255',
//         'stt' => 'nullable|integer',
//         'active' => 'nullable|boolean',
//     ]);

//     // Tự động tạo URL nếu chưa có
//     $url = $request->url ?: Str::slug($request->title);

//     // Tự động tăng số thứ tự
//     $maxStt = MenuFrontend::max('stt');
//     $nextStt = $maxStt ? $maxStt + 1 : 1;

//     $data = [
//         'title' => $request->title,
//         'url' => $url,
//         'parent_id' => $request->parent_id,
//         'position' => $request->position,
//         'stt' => $request->stt ?? $nextStt,
//         'active' => $request->has('active') ? $request->input('active') : 1,
//     ];

//     MenuFrontend::create($data);

//     return redirect()->route('admin.menufrontend.list')->with('success', 'Thêm menu thành công!');
// }

public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'parent_id' => 'nullable|exists:menus_frontend,id',
        'position' => 'nullable|array', 
        'stt' => 'nullable|integer',
        'active' => 'nullable|boolean',
        'footer_column' => 'nullable|integer|in:2,3',
    ]);

    // Tự động tạo URL nếu chưa có
    $url = $request->url ?: Str::slug($request->title);

    // Tự động tăng số thứ tự
    $maxStt = MenuFrontend::max('stt');
    $nextStt = $maxStt ? $maxStt + 1 : 1;

    $data = [
        'title' => $request->title,
        'url' => $url,
        'parent_id' => $request->parent_id,
        'position' => is_array($request->position) ? implode(',', $request->position) : $request->position, // CHỈNH
        'footer_column' => $request->footer_column,
        'stt' => $request->stt ?? $nextStt,
        'active' => $request->has('active') ? $request->input('active') : 1,
    ];

    MenuFrontend::create($data);

    return redirect()->route('admin.menufrontend.list')->with('success', 'Thêm menu thành công!');
}



    public function edit($id)
    {
        $menu = MenuFrontend::findOrFail($id);
        $menu->position = explode(',', $menu->position);
        $parents = MenuFrontend::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('admin.menufrontend.form', [
            'formAction' => route('admin.menufrontend.update', $menu->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa menu',
            'parents' => $parents,
            'menu' => $menu,
            'fields' => $menu->toArray(),
        ]);
    }

    // public function update(Request $request, $id)
    // {
    //     $menu = MenuFrontend::findOrFail($id);

    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //         'url' => 'nullable|string|max:255',
    //         'parent_id' => 'nullable|exists:menus_frontend,id',
    //         'position' => 'nullable|string|max:255',
    //         'stt' => 'nullable|integer',
    //         'active' => 'nullable|boolean',
    //     ]);

    //     $data = $request->only(['title', 'url', 'parent_id', 'position', 'stt', 'active']);
    //     $data['url'] = $data['url'] ?: Str::slug($data['title']);
    //     $data['active'] = $request->has('active') ? $request->input('active') : 1;

    //     $menu->update($data);

    //     return redirect()->route('admin.menufrontend.list')->with('success', 'Cập nhật menu thành công!');
    // }

    public function update(Request $request, $id)
{
    $menu = MenuFrontend::findOrFail($id);

    $request->validate([
        'title' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'parent_id' => 'nullable|exists:menus_frontend,id',
        'position' => 'nullable|array',
        'footer_column' => 'nullable|integer|in:2,3',
        'stt' => 'nullable|integer',
        'active' => 'nullable|boolean',
    ]);

    $data = $request->only(['title', 'url', 'parent_id', 'stt', 'active']);
    $data['position'] = is_array($request->position) ? implode(',', $request->position) : $request->position;
    $data['url'] = $request->url ?: Str::slug($request->title);
    $data['footer_column'] = $request->footer_column;
    $data['active'] = $request->has('active') ? $request->input('active') : 1;

    $menu->update($data);

    return redirect()->route('admin.menufrontend.list')->with('success', 'Cập nhật menu thành công!');
}


    public function destroy($id)
    {
        $menu = MenuFrontend::findOrFail($id);
        $menu->delete();
        return redirect()->route('admin.menufrontend.list')->with('success', 'Xóa menu thành công!');
    }

    public function show($id)
    {
        $menu = MenuFrontend::findOrFail($id);
        return view('admin.menufrontend.form', [
            'formTitle' => 'Chi tiết menu',
            'formAction' => '#',
            'formMethod' => 'GET',
            'fields' => $menu->toArray(),
            'parents' => MenuFrontend::whereNull('parent_id')->get(),
            'readOnly' => true,
        ]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
    }

    try {
        // Xóa mềm các bài viết theo danh sách ID
        MenuFrontend::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    } catch (\Exception $e) {
        // Ghi log lỗi (tùy chọn)
        // \Log::error('Lỗi khi xóa nhiều bài viết: ' . $e->getMessage());

        return response()->json(['message' => 'Lỗi server'], 500);
    }
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(MenuFrontend::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

     public function thaydoitrangthai(Request $request, $id)
{
     $menu = MenuFrontend::findOrFail($id);
    $menu->active = $request->input('active') ? 1 : 0;
    $menu->save();

    $message = $menu->active ? 'Bật thành công!' : 'Tắt thành công!';

    return response()->json(['message' => $message]);
}

public function apiIndex(Request $request)
{
    $menus = MenuFrontend::whereNull('parent_id')
        ->where('active', 1)
        ->orderBy('stt')
        ->with('subMenus')
        ->get();

    return response()->json($menus);
}
}

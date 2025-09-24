<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Permission;
use App\Http\Controllers\Controller;
use App\Helpers\SortOrderHelper;
class PhanQuyenController extends Controller
{
    public function index()
    {
        $roles = [];
        $menus = Menu::with(['childrenAll'])
            ->where('parent_id', 0)
            ->get();

        $permissons = [];

        return view('admin.phanquyen.list', compact('roles', 'menus', 'permissons'));
    }

    // Thêm mới
    public function create()
    {
        $menus = Menu::with('childrenRecursive')
            ->where('active', 1)
            ->where('parent_id', 0)
            ->get();
        $menu = null;
        return view('admin.phanquyen.form', [
            'menu' => $menu,
            'menus' => $menus,
            'formAction' => route('admin.phanquyen.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới menu'
        ]);
    }

    // lưu trữ
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:255',
        ]);

        $maxStt = Menu::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        $menu = Menu::create([
            'title' => $request->title,
            'url' => $request->url,
            'parent_id' => $request->parent_id ?: 0,
            'active' => 1,
            'icon' => $request->icon?: null,
            'stt' => $nextStt,
        ]);

        // Permission::create([
        //     'group_id' => 1,
        //     'menu_id' => $menu->id,
        //     'can_view' => 1,
        //     'can_add' => 0,
        //     'can_edit' => 0,
        //     'can_delete' => 0,
        //     'can_export' => 0,
        // ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Thêm mới menu thành công!']);
        } else {
            return redirect()->route('admin.phanquyen.list')
                ->with('success', 'Thêm mới menu thành công!');
        }
    }

    // Chỉnh sửa
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $menus = Menu::with('childrenRecursive')
            ->where('active', 1)
            ->where('parent_id', 0)
            ->get();

        return view('admin.phanquyen.form', [
            'menu' => $menu,
            'menus' => $menus,
            'formAction' => route('admin.phanquyen.update', $menu->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa menu'
        ]);
    }

    // Xóa
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();

        return response()->json(['success' => true, 'message' => 'Xóa menu thành công!']);
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:menus,id',
            'icon' => 'nullable|string|max:255',
        ]);

        $menu = Menu::findOrFail($id);
        $menu->update([
            'title' => $request->title,
            'url' => $request->url,
            'parent_id' => $request->parent_id ?: 0,
            'icon' => $request->icon ?: null,
            'active' => 1,
        ]);

        return redirect()->route('admin.phanquyen.list')
            ->with('success', 'Cập nhật thành công!');
    }

    // Xem chi tiết
    public function show($id)
    {
        $menu = Menu::findOrFail($id);
        $menus = Menu::all();
        $formTitle = 'Xem chi tiết menu';
        $formAction = '#';
        $submitButton = 'Lưu';
        $formMethod = null;
        $readonly = true;

        return view('admin.phanquyen.form', compact('menu', 'menus', 'formTitle', 'formAction', 'submitButton', 'formMethod', 'readonly'));
    }

    // Update số thứ tự
    public function updateStt(Request $request, $id)
    {
        $request->validate([
        'stt' => 'required|integer|min:1',
    ]);

    $result = SortOrderHelper::updateStt(Menu::class, $id, $request->stt);

    return response()->json(['message' => $result['message']]);
    }

    // Chuyển đổi trạng thái
    public function thaydoitrangthai(Request $request, $id)
{
    $menu = Menu::findOrFail($id);
    $menu->active = $request->input('active') ? 1 : 0;
    $menu->save();

    $message = $menu->active ? 'Bật thành công!' : 'Tắt thành công!';

    return response()->json(['message' => $message]);
}

}

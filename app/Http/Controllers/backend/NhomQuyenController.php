<?php

namespace App\Http\Controllers\backend;

use App\Enums\FormMode;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\NhomQuyen;
use App\Models\Permission;
use App\Models\User;
use Elastic\Elasticsearch\Endpoints\Cat;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NhomQuyenController extends Controller
{
    public function index(Request $request)
    {
        $nhomQuyen = NhomQuyen::latest()->paginate(5);
        if ($request->ajax()) {
            $table = view('user.nhomquyen.list', compact('nhomQuyen'))->render();
            $pagination = view('admin.component.pagination', compact('nhomQuyen'))->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
            ]);
        }
        $pagination = view('user.nhomquyen.list', compact('nhomQuyen'))->render();
        return view('user.nhomquyen.list', compact('nhomQuyen'));
    }

    // public function demo(Request $request)
    // {
    //     $nhomQuyen = NhomQuyen::latest()->paginate(5);
    //     $column = [
    //         [
    //             'label' => 'Tiêu đề',
    //             'name' => 'name',
    //         ]
    //     ];        

    //     if ($request->ajax()) {
    //         $table = view('user.nhomquyen.list-demo', compact('nhomQuyen', 'column'))->render();
    //         $pagination = view('admin.component.pagination', compact('nhomQuyen'))->render();
    //         return response()->json([
    //             'table' => $table,
    //             'pagination' => $pagination,
    //         ]);
    //     }
    //     return view('user.nhomquyen.list-demo', compact('nhomQuyen', 'column'));
    // }

    public function create()
    {
        $menus = Menu::with('children')->where('parent_id', 0)->get();
        $mode = FormMode::FORM_CREATE;
        return view('user.nhomquyen.form', compact('menus', 'mode'));
    }

    public function store(Request $request)
    {
        $newNhomQuyen = new NhomQuyen();
        $nhomQuyenData = $request->only($newNhomQuyen->getFillable());
        $nhomQuyenData['stt'] = NhomQuyen::laySttTiepTheo();
        $nhomQuyenData['active'] = 1;
        $userGroup = NhomQuyen::create($nhomQuyenData);

        $newQuyen = new Permission();
        $quyenData = $request->only($newQuyen->getFillable());

        $menuPermissions = [];
        $permissions = $request->input('permissions', []);
        foreach ($permissions as $permType => $menus) {
            foreach ($menus as $menuId => $value) {
                if (!isset($menuPermissions[$menuId])) {
                    $menuPermissions[$menuId] = [
                        'group_id' => $userGroup->id,
                        'menu_id' => $menuId,
                        'can_view' => 0,
                        'can_add' => 0,
                        'can_edit' => 0,
                        'can_delete' => 0,
                        'can_export' => 0
                    ];
                }
                $menuPermissions[$menuId][$permType] = 1;
            }
        }

        foreach ($menuPermissions as $permissionData) {
            Permission::create($permissionData);
        }

        return response()->json([
            'message' => 'Tạo nhóm quyền thành công',
            'data' => $permissionData
        ]);
    }

    public function edit($id)
    {
        $nhomQuyen = NhomQuyen::find($id);
        $menus = Menu::with('children')->where('parent_id', 0)->get();
        $permissions = Permission::where('group_id', $id)->get();
        $permissionsMap = $permissions->keyBy('menu_id');
        // $zipped = array_map(null, $menus->toArray(), $permissions->toArray());
        $mode = FormMode::FORM_EDIT;
        return view('user.nhomquyen.form', compact('menus', 'permissionsMap', 'nhomQuyen', 'mode'));
    }

    public function update(Request $request, $id)
    {
        $nhomQuyen = NhomQuyen::find($id);
        if (!$nhomQuyen) {
            return response()->json([
                'message' => 'Not found'
            ], 401);
        }
        $data = $request->only($nhomQuyen->getFillable());
        $nhomQuyen->update($data);

        $oldMenus = Permission::where('group_id', $id)
            ->pluck('menu_id')
            ->toArray();
        $permissions = $request->input('permissions');
        $menuPermissions = [];
        $currentMenus = [];
        if (!$permissions)
            return;
        foreach ($permissions as $permType => $menus) {
            foreach ($menus as $menuId => $value) {
                array_push($currentMenus, $menuId);
            }
        }

        $changedMenuIds = array_unique(array_merge($oldMenus, $currentMenus));
        foreach (['can_add', 'can_edit', 'can_delete', 'can_view', 'can_export'] as $permKey) {
            if (!isset($permissions[$permKey]))
                continue;

            foreach ($permissions[$permKey] as $menuId => $value) {
                $menuPermissions[$menuId][$permKey] = 1;
            }
        }


        foreach ($menuPermissions as $menuId => &$perms) {
            foreach (['can_add', 'can_edit', 'can_delete', 'can_view', 'can_export'] as $permKey) {
                if (!isset($perms[$permKey])) {
                    $perms[$permKey] = 0;
                }
            }
        }

        foreach ($changedMenuIds as $menuId) {
            $perms = [];
            foreach (['can_add', 'can_edit', 'can_delete', 'can_view', 'can_export'] as $key) {
                $perms[$key] = isset($permissions[$key][$menuId]) ? 1 : 0;
            }
            $result = Permission::updateOrCreate(
                [
                    'group_id' => $id,
                    'menu_id' => $menuId,
                ],
                $perms
            );
        }

        $users = User::where('group_id', $nhomQuyen->id)->get()->toArray();
        $coUserHienTaiKhong = array_filter($users, function ($item) {
            return $item['id'] == auth()->id();
        });
        if ($coUserHienTaiKhong) {
            cacheQuyenGroup($id);
        }
        return response()->json([
            'message' => 'Cập nhật thành công',
            'data' => $nhomQuyen
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            Log::info("deleting...");
            $nhomQuyen = NhomQuyen::findOrFail($id);
            $nhomQuyen->delete();
            $nhomQuyenPermiss = Permission::where('group_id', $id)
                ->get();
            foreach ($nhomQuyenPermiss as $item) {
                $item->delete();
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Xóa menu thành công!']);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Group does not exist.'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }

    }

    public function toggleStatus(Request $request, $id)
    {

        try {
            $nhomQuyen = NhomQuyen::findOrFail($id);
            $nhomQuyen->active = $request->input('active');
            $nhomQuyen->save();
            return response()->json(['success' => true, 'message' => 'Update status successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Group does not exist.'], 404);
        } catch (Exception $e) {
            Log::error('Exception message: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }

    public function updateStt(Request $request, $id)
    {
        try {
            $nhomQuyen = NhomQuyen::findOrFail($id);
            $nhomQuyen->stt = $request->input('stt');
            $nhomQuyen->save();
            Log::info("Update nhomquyen: " . $nhomQuyen->stt);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Group does not exist.'], 404);
        } catch (Exception $e) {
            Log::error('Exception message: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong'], 500);
        }
    }
}
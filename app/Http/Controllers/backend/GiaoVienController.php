<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\GiaoVien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SortOrderHelper;
class GiaoVienController extends Controller
{
    public function index(Request $request)
    {

        $query = GiaoVien::query();

        // Tìm kiếm theo keyword (trên name hoặc des)
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('name', 'like', '%' . $keyword . '%');
        }


        // Sắp xếp theo name hoặc mặc định theo stt
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') == 'desc' ? 'desc' : 'asc';
            $query->orderBy('name', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $giaoviens = $query->paginate($pageSize)->appends($request->all());

        // Nếu là AJAX thì trả về HTML table + pagination
        if ($request->ajax()) {
            $table = view('admin.giaovien.partials._table', compact('giaoviens'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $giaoviens])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.giaovien.list', compact('giaoviens'));
    }

    public function create()
    {
        return view('admin.giaovien.form', [
            'formTitle' => 'Thêm giáo viên',
            'formAction' => route('admin.giaovien.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facebook_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
        ]);

        $maxStt = GiaoVien::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        $data = array_merge(
            $request->only(['name', 'position', 'facebook_url', 'linkedin_url']),
            [
                'active' => $request->has('active') ? 1 : 0,
                'stt' => $nextStt
            ]
        );


        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('giaovien', 'public');
        }

        GiaoVien::create($data);

        return redirect()->route('admin.giaovien.list')->with('success', 'Thêm giáo viên thành công!');
    }

    public function edit($id)
    {
        $giaovien = GiaoVien::findOrFail($id);

        return view('admin.giaovien.form', [
            'formTitle' => 'Chỉnh sửa giáo viên',
            'formAction' => route('admin.giaovien.update', $giaovien->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'fields' => [
                'name' => $giaovien->name,
                'position' => $giaovien->position,
                'facebook_url' => $giaovien->facebook_url,
                'linkedin_url' => $giaovien->linkedin_url,
                'image' => $giaovien->image ? asset('storage/' . $giaovien->image) : null,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $giaovien = GiaoVien::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facebook_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
        ]);

        $data = $request->only(['name', 'position', 'facebook_url', 'linkedin_url']);

        if ($request->hasFile('image')) {
            if ($giaovien->image) {
                Storage::disk('public')->delete($giaovien->image);
            }
            $data['image'] = $request->file('image')->store('giaovien', 'public');
        }

        $giaovien->update($data);

        return redirect()->route('admin.giaovien.list')->with('success', 'Cập nhật giáo viên thành công!');
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        try {
            $giaovien = GiaoVien::findOrFail($id);
            $giaovien->active = $request->input('active') ? 1 : 0;
            $giaovien->save();

            $message = $giaovien->active ? 'Bật thành công!' : 'Tắt thành công!';
            return response()->json(['message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi thay đổi trạng thái!'], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $giaovien = GiaoVien::findOrFail($id);
            $giaovien->delete();

            return response()->json(['message' => 'Xóa giáo viên thành công!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi xóa giáo viên!'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

        if (!is_array($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        try {
            GiaoVien::whereIn('id', $ids)->delete();

            return response()->json(['message' => 'Xóa thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi server'], 500);
        }
    }

    // Cập nhật STT
    public function updateStt(Request $request, $id)
    {
        $request->validate([
        'stt' => 'required|integer|min:1',
    ]);

    $result = SortOrderHelper::updateStt(GiaoVien::class, $id, $request->stt);

    return response()->json(['message' => $result['message']]);
    }
}

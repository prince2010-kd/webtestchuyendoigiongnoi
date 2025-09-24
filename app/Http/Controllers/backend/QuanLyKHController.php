<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\KhoaHoc;
use Illuminate\Http\Request;

class QuanLyKHController extends Controller
{
    public function index(Request $request)
    {
        $query = KhoaHoc::query();

        // Tìm kiếm theo từ khóa (trên tiêu đề)
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('tieu_de', 'like', '%' . $keyword . '%');
        }

        // Sắp xếp theo tiêu đề nếu có, mặc định theo stt
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') == 'desc' ? 'desc' : 'asc';
            $query->orderBy('tieu_de', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Số bản ghi mỗi trang (mặc định 10)
        $pageSize = $request->input('page_size', 10);
        $khoahocs = $query->paginate($pageSize)->appends($request->all());

        // Trả về HTML nếu là AJAX
        if ($request->ajax()) {
            $table = view('admin.quanlykhoahoc.partials._table', compact('khoahocs'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $khoahocs])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.quanlykhoahoc.list', compact('khoahocs'));
    }

    public function create()
    {
        return view('admin.quanlykhoahoc.form', [
            'formTitle' => 'Thêm mới khóa học',
            'formAction' => route('admin.khoahoc.store'),
            'formMethod' => 'POST',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {

        $maxStt = KhoaHoc::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        $data = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:khoa_hocs,slug',
            'mo_ta_ngan' => 'nullable|string',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image',
            'noi_dung' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_new_keyword' => 'nullable|string',
            'active' => 'boolean',

        ]);

        $data['stt'] = $nextStt;

        // Xử lý upload hình ảnh đại diện
        if ($request->hasFile('hinh_anh')) {
            $data['hinh_anh'] = $request->file('hinh_anh')->store('uploads', 'public');
        }

        // Xử lý sections
        $sections = [];
        if ($request->has('sections')) {
            foreach ($request->input('sections') as $index => $section) {
                $imagePath = null;

                // Nếu có file ảnh cho section này
                if ($request->hasFile("sections.$index.image")) {
                    $imagePath = $request->file("sections.$index.image")->store('uploads', 'public');
                }

                $sections[] = [
                    'title' => $section['title'] ?? '',
                    'description' => $section['description'] ?? '',
                    'image' => $imagePath,
                ];
            }

            // Lưu JSON
            $data['sections'] = json_encode($sections);
        }

        // Tạo bản ghi
        KhoaHoc::create($data);

        return redirect()->route('admin.khoahoc.list')->with('success', 'Đã thêm khóa học');
    }


    // Hiển thị form sửa
    public function edit($id)
    {
        $khoaHoc = KhoaHoc::findOrFail($id);

        return view('admin.quanlykhoahoc.form', [
            'formTitle' => 'Cập nhật khóa học',
            'formAction' => route('admin.khoahoc.update', $khoaHoc->id),
            'formMethod' => 'PUT',
            'fields' => $khoaHoc,
        ]);
    }

    // Lưu cập nhật
    public function update(Request $request, $id)
    {
        $khoaHoc = KhoaHoc::findOrFail($id);

        $data = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:khoa_hocs,slug,' . $khoaHoc->id,
            'mo_ta_ngan' => 'nullable|string',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image',
            'noi_dung' => 'nullable|string',
            'sections' => 'nullable|array',
            'meta_title' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_new_keyword' => 'nullable|string',
            'active' => 'boolean',
            'stt' => 'integer',
        ]);

        if ($request->hasFile('hinh_anh')) {
            $data['hinh_anh'] = $request->file('hinh_anh')->store('uploads', 'public');
        } else {
            $data['hinh_anh'] = $khoaHoc->hinh_anh;
        }

       if ($request->has('sections')) {
    $oldSections = is_array($khoaHoc->sections)
        ? $khoaHoc->sections
        : json_decode($khoaHoc->sections ?? '[]', true);

    $sections = $request->input('sections');
    $newSections = [];

    foreach ($sections as $index => $section) {
        $newSection = [
            'title' => $section['title'] ?? '',
            'description' => $section['description'] ?? '',
        ];

        if ($request->hasFile("sections.$index.image")) {
            $file = $request->file("sections.$index.image");
            $newSection['image'] = $file->store('uploads', 'public');
        } else {
            $newSection['image'] = $oldSections[$index]['image'] ?? null;
        }

        $newSections[] = $newSection; // push vào mảng mới
    }

    $data['sections'] = json_encode($newSections, JSON_UNESCAPED_UNICODE);
}



        $khoaHoc->update($data);

        return redirect()->route('admin.khoahoc.list')->with('success', 'Cập nhật thành công');
    }

    // Xóa khóa học
    public function destroy(string $id)
    {
        $menu = KhoaHoc::findOrFail($id);
        $menu->delete();

        return response()->json(['success' => true, 'message' => 'Xóa bài viết thành công!']);
    }

    // Xóa nhiều
    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        try {
            // Xóa mềm các bài viết theo danh sách ID
            KhoaHoc::whereIn('id', $ids)->delete();

            return response()->json(['message' => 'Xóa thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi server'], 500);
        }
    }

    // Chuyển đổi trạng thái
    public function thaydoitrangthai(Request $request, $id)
    {
        $menu = KhoaHoc::findOrFail($id);
        $menu->active = $request->input('active') ? 1 : 0;
        $menu->save();

        $message = $menu->active ? 'Bật thành công!' : 'Tắt thành công!';

        return response()->json(['message' => $message]);
    }

}

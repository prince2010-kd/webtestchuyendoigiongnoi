<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\HoatDongImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Helpers\SortOrderHelper;
class HinhAnhHdongController extends Controller
{
    public function index(Request $request)
    {
        $query = HoatDongImage::query();

        // Tìm kiếm theo alt_text
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('alt_text', 'like', '%' . $keyword . '%');
        }

        // Sắp xếp theo alt_text hoặc mặc định theo stt
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') == 'desc' ? 'desc' : 'asc';
            $query->orderBy('alt_text', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $images = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            $table = view('admin.hinhanhhoatdong.partials._table', compact('images'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $images])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.hinhanhhoatdong.list', compact('images'));
    }

    public function create()
    {
        return view('admin.hinhanhhoatdong.form', [
            'formAction' => route('admin.hinhanhhdong.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm hình ảnh hoạt động',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Tăng STT tự động
        $maxStt = HoatDongImage::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        // Xử lý ảnh
        $imageFile = $request->file('image');
        $filename = time() . '_' . $imageFile->getClientOriginalName();
        $folder = 'hoatdong';

        // Resize và encode ảnh
        $img = Image::make($imageFile)->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('jpg', 80); // encode để lưu dạng file binary

        // Lưu ảnh vào storage disk 'public'
        Storage::disk('public')->put("{$folder}/{$filename}", $img);

        // Dữ liệu lưu
        $data = [
            'alt_text' => $request->alt_text,
            'image_path' => "storage/{$folder}/{$filename}", // public/storage/...
            'stt' => $nextStt,
            'active' => 1,
        ];

        HoatDongImage::create($data);

        return redirect()->route('admin.hinhanhhdong.list')->with('success', 'Thêm hình ảnh thành công!');
    }

    public function edit(string $id)
    {
        $image = HoatDongImage::findOrFail($id);

        return view('admin.hinhanhhoatdong.form', [
            'image' => $image,
            'formAction' => route('admin.hinhanhhdong.update', $image->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa hình ảnh hoạt động',
            'fields' => [
                'image' => $image->image_path,
                'alt_text' => $image->alt_text,
                'active' => $image->active,
            ],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $image = HoatDongImage::findOrFail($id);

        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = [
            'alt_text' => $request->alt_text,
        ];

        // Nếu có ảnh mới thì thay ảnh
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($image->image_path) {
                Storage::disk('public')->delete(str_replace('storage/', '', $image->image_path));
            }

            $imageFile = $request->file('image');
            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $folder = 'hoatdong';

            // Resize và encode
            $img = Image::make($imageFile)->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->encode('jpg', 80); // Dạng binary

            // Lưu vào disk public
            Storage::disk('public')->put("{$folder}/{$filename}", $img);

            $data['image_path'] = "storage/{$folder}/{$filename}";
        }

        $image->update($data);

        return redirect()->route('admin.hinhanhhdong.list')->with('success', 'Cập nhật hình ảnh thành công!');
    }


    public function destroy(string $id)
    {
        $image = HoatDongImage::findOrFail($id);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa hình ảnh!']);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        try {
            // Xóa mềm các bài viết theo danh sách ID
            HoatDongImage::whereIn('id', $ids)->delete();

            return response()->json(['message' => 'Xóa thành công']);
        } catch (\Exception $e) {
            // Ghi log lỗi (tùy chọn)
            // \Log::error('Lỗi khi xóa nhiều bài viết: ' . $e->getMessage());

            return response()->json(['message' => 'Lỗi server'], 500);
        }
    }

    // Cập nhật STT
    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(HoatDongImage::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

    // Chuyển đổi trạng thái
    public function thaydoitrangthai(Request $request, $id)
    {
        $menu = HoatDongImage::findOrFail($id);
        $menu->active = $request->input('active') ? 1 : 0;
        $menu->save();

        $message = $menu->active ? 'Bật thành công!' : 'Tắt thành công!';

        return response()->json(['message' => $message]);
    }


}

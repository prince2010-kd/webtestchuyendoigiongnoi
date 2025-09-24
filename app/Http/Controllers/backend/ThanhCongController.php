<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Helpers\SortOrderHelper;
use App\Models\SuccessStory;

class ThanhCongController extends Controller
{
    public function index(Request $request)
    {
        $query = SuccessStory::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('sortBy')) {
            $sort = $request->sortBy === 'desc' ? 'desc' : 'asc';
            $query->orderBy('name', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        $pageSize = $request->input('page_size', 10);
        $stories = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            $table = view('admin.cauchuyenthanhcong.partials._table', compact('stories'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $stories])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.cauchuyenthanhcong.list', compact('stories'));
    }

    public function create()
    {
        return view('admin.cauchuyenthanhcong.form', [
            'formAction' => route('admin.thanhcong.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm câu chuyện thành công',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'school' => 'required|string|max:255',
            'ielts_score' => 'required|string|max:10',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'content' => 'required|string',
        ]);

        // Tăng STT tự động
        $maxStt = SuccessStory::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        // Xử lý ảnh
        $imageFile = $request->file('image');
        $filename = time() . '_' . $imageFile->getClientOriginalName();
        $folder = 'success_stories';

        // Resize ảnh bằng Intervention và lưu bằng Storage
        $img = Image::make($imageFile)->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode(); // Trả về binary để lưu

        // Lưu ảnh vào storage disk
        Storage::disk('public')->put("{$folder}/{$filename}", $img);

        // Dữ liệu lưu
        $data = [
            'name' => $request->name,
            'school' => $request->school,
            'ielts_score' => $request->ielts_score,
            'content' => $request->content,
            'image' => "storage/{$folder}/{$filename}",
            'stt' => $nextStt,
            'active' => 1,
        ];

        SuccessStory::create($data);

        return redirect()->route('admin.thanhcong.list')->with('success', 'Thêm mới thành công!');
    }

    public function edit($id)
    {
        $story = SuccessStory::findOrFail($id);

        return view('admin.cauchuyenthanhcong.form', [
            'formAction' => route('admin.thanhcong.update', $story->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Cập nhật câu chuyện thành công',
            'fields' => [
                'name' => $story->name,
                'school' => $story->school,
                'ielts_score' => $story->ielts_score,
                'content' => $story->content,
                'image' => $story->image,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $story = SuccessStory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'school' => 'required|string|max:255',
            'ielts_score' => 'required|string|max:10',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'content' => 'required|string',
        ]);

        $data = $request->only(['name', 'school', 'ielts_score', 'content']);

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($story->image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $story->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $folder = 'success_stories';

            // Resize và lưu ảnh mới bằng Storage
            $img = Image::make($file)->resize(800, 600, function ($c) {
                $c->aspectRatio();
                $c->upsize();
            })->encode(); // Trả về binary

            Storage::disk('public')->put("{$folder}/{$filename}", $img);

            $data['image'] = "storage/{$folder}/{$filename}";
        }

        $story->update($data);

        return redirect()->route('admin.thanhcong.list')->with('success', 'Cập nhật thành công!');
    }


    public function destroy($id)
    {
        $story = SuccessStory::findOrFail($id);
        $story->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa thành công']);
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(SuccessStory::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        $story = SuccessStory::findOrFail($id);
        $story->active = $request->input('active') ? 1 : 0;
        $story->save();

        return response()->json([
            'message' => $story->active ? 'Bật thành công!' : 'Tắt thành công!',
        ]);
    }
}

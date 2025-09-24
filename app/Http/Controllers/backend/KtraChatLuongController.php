<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\QualityCheck;
use App\Models\QuestionSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use App\Helpers\SortOrderHelper;
class KtraChatLuongController extends Controller
{
    public function index(Request $request)
    {
        $query = QualityCheck::with('questionSet'); // <-- thêm eager load quan hệ

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('stt', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        $pageSize = $request->input('page_size', 10);
        $items = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            $table = view('admin.ktrachatluong.partials._table', compact('items'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $items])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.ktrachatluong.list', compact('items'));
    }


    public function create()
    {
        $questionSets = QuestionSet::orderBy('stt')->pluck('title', 'id');
        return view('admin.ktrachatluong.form', [
            'formAction' => route('admin.quality.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm nội dung kiểm tra',
            'fields' => [],
            'questionSets' => $questionSets,
        ]);
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'title'            => 'required|string|max:255',
        'slug'             => 'nullable|string|max:255|unique:quality_checks,slug',
        'content'          => 'nullable|string',
        'image'            => 'nullable|image|max:2048',
        'stt'              => 'nullable|integer|min:0',
        'active'           => 'nullable|boolean',
        'question_set_id'  => 'nullable|exists:question_sets,id',
    ]);

    // Nếu không nhập slug thì tạo từ title
    $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

    // Đảm bảo slug là duy nhất (trường hợp người dùng không nhập, hoặc nhập trùng)
    $originalSlug = $data['slug'];
    $i = 1;
    while (QualityCheck::where('slug', $data['slug'])->exists()) {
        $data['slug'] = $originalSlug . '-' . $i++;
    }

    // Xử lý ảnh nếu có
    if ($request->hasFile('image')) {
        $imageFile = $request->file('image');
        $filename = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.jpg';
        $folder = 'quality';

        $img = Image::make($imageFile)->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('jpg', 80);

        Storage::disk('public')->put("{$folder}/{$filename}", $img);
        $data['image'] = "storage/{$folder}/{$filename}";
    }

    // Gán mặc định nếu chưa có
    $data['active'] = $data['active'] ?? 1;
    $data['stt'] = $data['stt'] ?? (QualityCheck::max('stt') + 1);

    QualityCheck::create($data);

    return redirect()->route('admin.quality.list')->with('success', 'Thêm thành công!');
}

    public function edit($id)
    {
        $item = QualityCheck::findOrFail($id);
        $questionSets = QuestionSet::orderBy('stt')->pluck('title', 'id');

        return view('admin.ktrachatluong.form', [
            'formAction' => route('admin.quality.update', $item->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa nội dung',
            'fields' => $item,
            'questionSets' => $questionSets,
        ]);
    }

    public function update(Request $request, $id)
{
    $item = QualityCheck::findOrFail($id);

    $data = $request->validate([
        'title'           => 'required|string|max:255',
        'slug'            => 'nullable|string|max:255|unique:quality_checks,slug,' . $id,
        'content'         => 'nullable|string',
        'image'           => 'nullable|image|max:2048',
        'stt'             => 'nullable|integer|min:0',
        'active'          => 'nullable|boolean',
        'question_set_id' => 'nullable|exists:question_sets,id',
    ]);

    // Nếu không nhập slug thì tạo từ title
    $data['slug'] = $data['slug'] ?? Str::slug($data['title']);

    // Đảm bảo slug là duy nhất (ngoại trừ bản ghi hiện tại)
    $originalSlug = $data['slug'];
    $i = 1;
    while (
        QualityCheck::where('slug', $data['slug'])
            ->where('id', '!=', $id)
            ->exists()
    ) {
        $data['slug'] = $originalSlug . '-' . $i++;
    }

    // Xử lý ảnh nếu có
    if ($request->hasFile('image')) {
        if ($item->image) {
            Storage::disk('public')->delete(str_replace('storage/', '', $item->image));
        }

        $imageFile = $request->file('image');
        $filename = time() . '_' . Str::slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME)) . '.jpg';
        $folder = 'quality';

        $img = Image::make($imageFile)->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('jpg', 80);

        Storage::disk('public')->put("{$folder}/{$filename}", $img);
        $data['image'] = "storage/{$folder}/{$filename}";
    }

    $item->update($data);

    return redirect()->route('admin.quality.list')->with('success', 'Cập nhật thành công!');
}


    public function destroy($id)
    {
        $set = QualityCheck::findOrFail($id);
        $set->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa bộ đề']);
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        $item = QualityCheck::findOrFail($id);
        $item->active = $request->input('active') ? 1 : 0;
        $item->save();

        return response()->json(['message' => $item->active ? 'Đã bật hiển thị' : 'Đã tắt hiển thị']);
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(QualityCheck::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        QualityCheck::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
}

<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChuongTrinhAnhNgu;
use Illuminate\Support\Facades\Storage;
use App\Helpers\SortOrderHelper;

class ChuongTrinhAnhNguController extends Controller
{
    public function index(Request $request)
    {
        $query = ChuongTrinhAnhNgu::query();

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->input('keyword') . '%');
        }

        if ($request->filled('sortBy')) {
            $query->orderBy('title', $request->input('sortBy') === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('stt', 'asc');
        }

        $pageSize = $request->input('page_size', 10);
        $items = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.chuongtrinhanhngu.partials._table', compact('items'))->render(),
                'pagination' => view('admin.component.pagination', ['posts' => $items])->render(),
                'status' => true,
            ]);
        }

        return view('admin.chuongtrinhanhngu.list', compact('items'));
    }

    public function create()
    {
        return view('admin.chuongtrinhanhngu.form', [
            'formTitle' => 'Thêm chương trình Anh ngữ',
            'formAction' => route('admin.anhngu.store'),
            'formMethod' => 'POST',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $maxStt = ChuongTrinhAnhNgu::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'active' => 'boolean',
            'sections' => 'nullable|array',
        ]);

        $data['stt'] = $nextStt;

        //  Ảnh chính
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('uploads', 'public');
        }

        //  Sections
        $sections = $request->input('sections', []);
        foreach ($sections as $index => &$section) {
            $section['title'] = $section['title'] ?? '';
            $section['slug'] = $section['slug'] ?? '';
            $section['image'] = null;

            if ($request->hasFile("sections.$index.image")) {
                $section['image'] = $request->file("sections.$index.image")->store('uploads', 'public');
            }
        }
        unset($section);

        $data['sections'] = json_encode($sections, JSON_UNESCAPED_UNICODE);

        ChuongTrinhAnhNgu::create($data);

        return redirect()->route('admin.anhngu.list')->with('success', 'Đã thêm chương trình Anh ngữ');
    }

    public function edit($id)
    {
        $item = ChuongTrinhAnhNgu::findOrFail($id);

        return view('admin.chuongtrinhanhngu.form', [
            'formTitle' => 'Cập nhật chương trình',
            'formAction' => route('admin.anhngu.update', $item->id),
            'formMethod' => 'PUT',
            'fields' => $item,
        ]);
    }

    public function update(Request $request, $id)
{
    $item = ChuongTrinhAnhNgu::findOrFail($id);

    $data = $request->validate([
        'title' => 'required|string|max:255',
        'sections' => 'nullable|array',
        'image' => 'nullable|image',
    ]);

    // Xử lý ảnh chính
    if ($request->hasFile('image')) {
        // Xóa ảnh cũ nếu có
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        $data['image'] = $request->file('image')->store('uploads', 'public');
    } else {
        $data['image'] = $item->image; // giữ nguyên nếu không thay
    }

    // Xử lý sections nếu có
    if ($request->has('sections')) {
        $inputSections = $request->input('sections');
        $oldSections = is_array($item->sections)
            ? $item->sections
            : json_decode($item->sections ?? '[]', true);

        $newSections = [];

        foreach ($inputSections as $index => $section) {
            $newSection = [
                'title' => $section['title'] ?? '',
                'slug' => $section['slug'] ?? '',
            ];

            // Xử lý ảnh cho từng mục section
            if ($request->hasFile("sections.$index.image")) {
                $newSection['image'] = $request->file("sections.$index.image")->store('uploads', 'public');
            } else {
                // Giữ lại ảnh cũ nếu không có ảnh mới
                $newSection['image'] = $oldSections[$index]['image'] ?? null;
            }

            $newSections[] = $newSection;
        }

        $data['sections'] = json_encode($newSections, JSON_UNESCAPED_UNICODE);
    }

    // Cập nhật vào DB
    $item->update($data);

    return redirect()->route('admin.anhngu.list')->with('success', 'Cập nhật thành công');
}



    public function destroy($id)
    {
        $item = ChuongTrinhAnhNgu::findOrFail($id);
        $item->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa chương trình']);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        ChuongTrinhAnhNgu::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        $item = ChuongTrinhAnhNgu::findOrFail($id);
        $item->active = $request->input('active') ? 1 : 0;
        $item->save();

        return response()->json(['message' => $item->active ? 'Đã bật hiển thị' : 'Đã tắt hiển thị']);
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(ChuongTrinhAnhNgu::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }
}

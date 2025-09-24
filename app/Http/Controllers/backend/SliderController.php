<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use App\Helpers\SortOrderHelper;
class SliderController extends Controller
{
    public function index(Request $request)
    {
        $query = Slider::query();

        // Tìm kiếm theo keyword (trên title hoặc des)
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('title', 'like', '%' . $keyword . '%');
        }


        // Sắp xếp theo title hoặc mặc định theo stt
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') == 'desc' ? 'desc' : 'asc';
            $query->orderBy('title', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $sliders = $query->paginate($pageSize)->appends($request->all());

        // Nếu là AJAX thì trả về HTML table + pagination
        if ($request->ajax()) {
            $table = view('admin.slider.partials._table', compact('sliders'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $sliders])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.slider.list', compact('sliders'));
    }

    public function create()
    {
        $slider = Slider::orderBy('title')->get();

        return view('admin.slider.form', [
            'slider' => $slider,
            'formAction' => route('admin.slider.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới slider',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'content' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $url = $request->url ?: Str::slug($request->title);


        $maxStt = Slider::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        $data = array_merge([
            'title' => $request->title,
            'stt' => $nextStt,
            'active' => 1,
            'content' => $request->content,
            'image' => null,

        ], );

        // Xử lý ảnh
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
            $data['image'] = $path;

        }

        // Lưu vào CSDL
        Slider::create($data);

        return redirect()->route('admin.slider.list')->with('success', 'Thêm slider thành công!');
    }

    public function edit(string $id)
    {
        $sliderItem = Slider::findOrFail($id);

        return view('admin.slider.form', [
            'slider' => Slider::orderBy('title')->get(),
            'formAction' => route('admin.slider.update', $sliderItem->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa slider',
            'fields' => [
                'title' => $sliderItem->title,
                'content' => $sliderItem->content,
                'image' => $sliderItem->image,
                'url' => $sliderItem->url,
            ],
            
        ]);
    }

    public function update(Request $request, string $id)
    {
        $sliderItem = Slider::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'content' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'url' => $request->url ?: Str::slug($request->title),
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('sliders', 'public');
            $data['image'] = $path;
            $data['meta_image'] = asset('storage/' . $path);
        }

        $sliderItem->update($data);

        return redirect()->route('admin.slider.list')->with('success', 'Cập nhật slider thành công!');
    }

    public function destroy(string $id)
    {
        try {
            $slider = Slider::findOrFail($id);
            $slider->delete();

            return response()->json(['message' => 'Xóa slider thành công!']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi xóa slider!'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

        if (!is_array($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        try {
            Slider::whereIn('id', $ids)->delete();

            return response()->json(['message' => 'Xóa thành công']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi server'], 500);
        }
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        try {
            $slider = Slider::findOrFail($id);
            $slider->active = $request->input('active') ? 1 : 0;
            $slider->save();

            $message = $slider->active ? 'Bật thành công!' : 'Tắt thành công!';
            return response()->json(['message' => $message]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi khi thay đổi trạng thái!'], 500);
        }
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(Slider::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

}

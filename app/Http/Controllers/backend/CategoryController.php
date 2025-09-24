<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Category::query();

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
        $categories = $query->paginate($pageSize)->appends($request->all());

        // Nếu là AJAX thì trả về HTML table + pagination
        if ($request->ajax()) {
            $table = view('admin.category.partials._table', compact('categories'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $categories])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.category.list', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.form', [
            'formAction' => route('admin.category.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới danh mục'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:category,title',
            'image' => 'nullable|string',
            'content' => 'nullable|string'
        ]);

        // Tạo slug từ tiêu đề
        $seoName = Str::slug($request->title);

        // Tìm stt lớn nhất hiện tại
        $maxStt = Category::max('stt') ?? 0;
        $stt = $maxStt + 1;

        // Thêm mới
        Category::create([
            'title' => $request->title,
            'seo-name' => $seoName,
            'image' => null,
            'content' => $request->input('content'),
            'stt' => $stt,
            'active' => 1,
        ]);

        return redirect()->route('admin.category.list')->with('success', 'Thêm danh mục thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        $formTitle = 'Xem chi tiết danh mục';
        $formAction = '#';
        $submitButton = 'Lưu';
        $formMethod = null;
        $readonly = true;

        return view('admin.category.form', compact('category', 'formTitle', 'formAction', 'submitButton', 'formMethod', 'readonly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);

        return view('admin.category.form', [
            'formAction' => route('admin.category.update', $category->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa danh mục',
            'category' => $category,
            'fields' => [
                'title' => $category->title,
                'content' => $category->content,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category', 'title')->ignore($category->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'content' => 'nullable|string',
        ]);

        $title = $request->input('title');
        $seoName = Str::slug($title);

        $data = [
            'title' => $title,
            'seo-name' => $seoName,
            'content' => $request->input('content'),
        ];

        // Nếu có file mới thì xử lý
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            // Lưu ảnh mới
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.category.list')->with('success', 'Cập nhật thành công!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Category::findOrFail($id);
        $menu->delete();

        return response()->json(['success' => true, 'message' => 'Xóa danh mục thành công!']);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

    if (!is_array($ids) || empty($ids)) {
        return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
    }

    try {
        // Xóa mềm các bài viết theo danh sách ID
        Category::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Lỗi server'], 500);
    }
    }

    public function thaydoitrangthai(Request $request, $id)
{
     $menu = Category::findOrFail($id);
    $menu->active = $request->input('active') ? 1 : 0;
    $menu->save();

    $message = $menu->active ? 'Bật thành công!' : 'Tắt thành công!';

    return response()->json(['message' => $message]);
}
public function apiIndex()
{
    return response()->json(Category::all());
}
}

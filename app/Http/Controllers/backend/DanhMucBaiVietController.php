<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Posts;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Helpers\MetaHelper;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SortOrderHelper;
use Intervention\Image\Facades\Image;
class DanhMucBaiVietController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Posts::query();

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
        $danhmucbaiviet = $query->paginate($pageSize)->appends($request->all());

        // Nếu là AJAX thì trả về HTML table + pagination
        if ($request->ajax()) {
            $table = view('admin.danhmucbaiviet.partials._table', compact('danhmucbaiviet'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $danhmucbaiviet])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.danhmucbaiviet.list', compact('danhmucbaiviet'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $danhmucbaiviet = Category::where('active', 1)
            ->orderBy('title')
            ->get();

        return view('admin.danhmucbaiviet.form', [
            'danhmucbaiviet' => $danhmucbaiviet,
            'formAction' => route('admin.danhmucbaiviet.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới bài viết',
            'fields' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'created_at' => 'nullable|date',
            'category_id' => 'nullable|exists:category,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'short_description' => 'nullable|string',
            'content' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_new_keyword' => 'nullable|string',
            'type' => 'nullable|string|max:255',
            'youtube_url' => 'nullable|url|max:255',
        ], [
            'title.unique' => 'Tiêu đề bài viết này đã tồn tại. Bạn vui lòng nhập lại tiêu đề khác.',
        ]);

        $url = $request->url ?: Str::slug($request->title);

        $meta = MetaHelper::generateMetaTags([
            'title' => $request->title,
            'short_description' => $request->short_description,
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'meta_new_keyword' => $request->meta_new_keyword,
            'meta_image' => $request->hasFile('image') ? asset('storage/' . $request->file('image')->hashName()) : null,
            'type' => $request->type,
        ]);
        $maxStt = Posts::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;
        $user = Auth::user();
        $data = array_merge([
            'title' => $request->title,
            'url' => $url,
            'category_id' => $request->category_id,
            'short_description' => $request->short_description,
            'content' => $request->content,
            'created_at' => $request->created_at ?: now(),
            'stt' => $nextStt,
            'active' => 1,
            'user_id' => $user?->id,
        ], $meta, [
            'type' => $request->type // đảm bảo cái này được ưu tiên
        ]);

        if ($request->youtube_url) {
    preg_match("/(?:youtu\.be\/|youtube\.com\/(?:watch\\?v=|embed\/|.*v=))([\w\-]+)/", $request->youtube_url, $matches);
    $videoId = $matches[1] ?? null;

    if (!$videoId) {
        return back()->withInput()->withErrors(['youtube_url' => 'Không thể lấy video ID từ link YouTube.']);
    }

    $data['youtube_url'] = $request->youtube_url;
    $data['youtube_id'] = $videoId;
}

        // Xử lý ảnh
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $filename = time() . '_' . $imageFile->getClientOriginalName();
            $folder = 'posts';

            // Resize ảnh về tối đa 800x600 (giữ nguyên tỉ lệ)
            $img = Image::make($imageFile)->resize(800, 600, function ($constraint) {
                $constraint->aspectRatio(); // giữ nguyên tỉ lệ gốc
                $constraint->upsize();      // không phóng to ảnh nhỏ
            })->encode('jpg', 80); // nén ảnh JPG với chất lượng 80%

            // Lưu ảnh vào thư mục storage/app/public/posts
            Storage::disk('public')->put("{$folder}/{$filename}", $img);

            $data['image'] = "{$folder}/{$filename}";
            $data['meta_image'] = asset('storage/' . $folder . '/' . $filename);
        }


        // Lưu vào CSDL
        $post = Posts::create($data);

        return redirect()->route('admin.danhmucbaiviet.list')->with('success', 'Thêm bài viết thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Posts::findOrFail($id);

        return view('admin.danhmucbaiviet.form', [
            'formTitle' => 'Chi tiết bài viết',
            'formAction' => '#',
            'formMethod' => 'GET',
            'fields' => $post->toArray(),
            'danhmucbaiviet' => Category::orderBy('title')->get(),
            'readOnly' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Posts::findOrFail($id);
        $danhmucbaiviet = Category::where('active', 1)
            ->orderBy('title')
            ->get();

        return view('admin.danhmucbaiviet.form', [
            'danhmucbaiviet' => $danhmucbaiviet,
            'post' => $post,
            'formAction' => route('admin.danhmucbaiviet.update', $post->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa bài viết',
            'fields' => [
                'logo' => $post->image,
                'title' => $post->title,
                'url' => $post->url,
                'category_id' => $post->category_id,
                'short_description' => $post->short_description,
                'content' => $post->content,
                'meta_title' => $post->meta_title,
                'meta_keywords' => $post->meta_keywords,
                'meta_description' => $post->meta_description,
                'meta_new_keyword' => $post->meta_new_keyword,
                'created_at' => $post->created_at,
                'type' => $post->type,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
{
    $post = Posts::findOrFail($id);

    $request->validate([
        'title' => 'required|string|max:255|unique:posts,title,' . $id,
        'url' => 'nullable|string|max:255',
        'created_at' => 'nullable|date',
        'category_id' => 'nullable|exists:category,id',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'short_description' => 'nullable|string',
        'content' => 'nullable|string',
        'meta_title' => 'nullable|string|max:255',
        'meta_keywords' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string',
        'meta_new_keyword' => 'nullable|string|max:255',
        'type' => 'nullable|string|max:255',
        'youtube_url' => 'nullable|url|max:255',
    ], [
        'title.unique' => 'Tiêu đề bài viết này đã tồn tại. Bạn vui lòng nhập lại tiêu đề khác.',
    ]);

    $url = $request->url ?: Str::slug($request->title);
    $meta_title = $request->meta_title ?: $request->title;

    if ($request->meta_description) {
        $meta_description = $request->meta_description;
    } else if ($request->short_description) {
        $meta_description = Str::limit(strip_tags($request->short_description), 200);
    } else if ($request->content) {
        $meta_description = Str::limit(strip_tags($request->content), 200);
    } else {
        $meta_description = '';
    }

    $meta_keywords = $request->meta_keywords ?: implode(',', explode(' ', $request->title));
    $meta_new_keyword = $request->meta_new_keyword;

    $data = [
        'title' => $request->title,
        'url' => $url,
        'category_id' => $request->category_id,
        'short_description' => $request->short_description,
        'content' => $request->content,
        'meta_title' => $meta_title,
        'meta_description' => $meta_description,
        'meta_keywords' => $meta_keywords,
        'meta_new_keyword' => $meta_new_keyword,
        'created_at' => $request->created_at ?: $post->created_at,
        'type' => $request->type,
    ];

    // ✅ Thêm xử lý YouTube URL & ID
    if ($request->youtube_url) {
        preg_match("/(?:youtu\.be\/|youtube\.com\/(?:watch\\?v=|embed\/|.*v=))([\w\-]+)/", $request->youtube_url, $matches);
        $videoId = $matches[1] ?? null;

        if (!$videoId) {
            return back()->withInput()->withErrors(['youtube_url' => 'Không thể lấy video ID từ link YouTube.']);
        }

        $data['youtube_url'] = $request->youtube_url;
        $data['youtube_id'] = $videoId;
    } else {
        $data['youtube_url'] = null;
        $data['youtube_id'] = null;
    }

    // Xử lý ảnh mới nếu có
    if ($request->hasFile('image')) {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $imageFile = $request->file('image');
        $filename = time() . '_' . $imageFile->getClientOriginalName();
        $folder = 'posts';

        $img = Image::make($imageFile)->resize(800, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('jpg', 80);

        Storage::disk('public')->put("{$folder}/{$filename}", $img);

        $data['image'] = "{$folder}/{$filename}";
    }

    $post->update($data);

    return redirect()->route('admin.danhmucbaiviet.list')->with('success', 'Cập nhật bài viết thành công!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $menu = Posts::findOrFail($id);
        $menu->delete();

        return response()->json(['success' => true, 'message' => 'Xóa bài viết thành công!']);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids;

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        try {
            // Xóa mềm các bài viết theo danh sách ID
            Posts::whereIn('id', $ids)->delete();

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

        $result = SortOrderHelper::updateStt(Posts::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

    // Chuyển đổi trạng thái
    public function thaydoitrangthai(Request $request, $id)
    {
        $menu = Posts::findOrFail($id);
        $menu->active = $request->input('active') ? 1 : 0;
        $menu->save();

        $message = $menu->active ? 'Bật thành công!' : 'Tắt thành công!';

        return response()->json(['message' => $message]);
    }

    public function thaydoinoibat(Request $request, $id)
    {
        $post = Posts::findOrFail($id);
        $isFeatured = $request->input('is_featured') ? 1 : 0;

        if ($isFeatured) {
            // Tắt nổi bật của tất cả các bài khác
            Posts::where('id', '!=', $id)->update(['is_featured' => 0]);
        }

        $post->is_featured = $isFeatured;
        $post->save();

        return response()->json([
            'message' => $isFeatured ? 'Đã bật nổi bật bài viết!' : 'Đã tắt nổi bật!',
            'id' => $post->id,
        ]);
    }

}

<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SanPhamController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('name', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        $pageSize = $request->input('page_size', 10);
        $products = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            $table = view('admin.sanpham.partials._table', compact('products'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $products])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.sanpham.list', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('title')->get();
        return view('admin.sanpham.form', [
            'formAction' => route('admin.sanpham.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm sản phẩm',
            'formTitle' => 'Thêm sản phẩm',
            'product' => new \App\Models\Product(),
            'fields' => [],
            
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_id' => 'required|exists:category,id',
        ]);

        $slug = $request->slug ?: Str::slug($request->name);
        $data = $request->except(['main_image']);
        $data['slug'] = $slug;
        $data['sku'] = $request->input('sku');
        $data['stock'] = $request->input('stock');
        $data['color'] = $request->input('color');
        $data['size'] = $request->input('size');
        $data['original_price'] = str_replace('.', '', $request->input('original_price'));
        $data['sale_price'] = str_replace('.', '', $request->input('sale_price'));
        $data['category_id'] = $request->input('category_id');
        // STT tự động
        $maxStt = Product::max('stt');
        $data['stt'] = $maxStt ? $maxStt + 1 : 1;

        // Xử lý ảnh chính
        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $path = $file->store('products', 'public');
            $data['main_image'] = $path;
        }

        $product = Product::create($data);

       if ($request->hasFile('gallery')) {
    Log::info('Có file gallery gửi lên', [
        'files' => $request->file('gallery')
    ]);

    foreach ($request->file('gallery') as $file) {
        if ($file->isValid()) {
            $path = $file->store('products/gallery', 'public');

            $image = ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
            ]);

            Log::info('Đã lưu ảnh vào product_images', [
                'product_id' => $product->id,
                'path' => $path,
                'record' => $image
            ]);
        } else {
            Log::warning('Ảnh không hợp lệ', [
                'file' => $file
            ]);
        }
    }
} else {
    Log::warning('Không có file gallery nào được gửi');
}

        return redirect()->route('admin.sanpham.list')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit($id)
{
    $product = Product::with('galleryImages')->findOrFail($id);

Log::info('Ảnh gallery:', [
    'product_id' => $product->id,
    'gallery' => $product->galleryImages
]);

    $categories = Category::orderBy('title')->get();

    return view('admin.sanpham.form', [
    'formAction' => route('admin.sanpham.update', $product->id),
    'formMethod' => 'PUT',
    'submitButton' => 'Cập nhật sản phẩm',
    'formTitle' => 'Chỉnh sửa sản phẩm',
    'product' => $product,
    'fields' => $product->toArray(), 
    'categories' => $categories,
]);
}

public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:products,slug,' . $id,
        'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        'category_id' => 'required|exists:category,id',
    ]);

    $slug = $request->slug ?: Str::slug($request->name);

    $data = $request->except(['main_image', 'gallery']);
    $data['slug'] = $slug;
    $data['category_id'] = $request->input('category_id');

    // Xử lý main_image nếu có
    if ($request->hasFile('main_image')) {
        $file = $request->file('main_image');
        $path = $file->store('products', 'public');
        $data['main_image'] = $path;
    }

    // Làm sạch giá trị tiền nếu có dấu chấm
    $data['original_price'] = str_replace('.', '', $request->input('original_price'));
    $data['sale_price'] = str_replace('.', '', $request->input('sale_price'));

    $product->update($data);

    // ✅ Lưu ảnh gallery nếu có
    if ($request->hasFile('gallery')) {
    Log::info('Cập nhật: Có file gallery gửi lên', [
        'files' => $request->file('gallery')
    ]);

    foreach ($request->file('gallery') as $file) {
        if ($file->isValid()) {
            $path = $file->store('products/gallery', 'public');

            $image = ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
            ]);

            Log::info('Cập nhật: Đã lưu ảnh vào product_images', [
                'product_id' => $product->id,
                'path' => $path,
                'record' => $image
            ]);
        } else {
            Log::warning('Ảnh không hợp lệ khi update', [
                'file' => $file
            ]);
        }
    }
} else {
    Log::warning('Cập nhật: Không có file gallery nào được gửi');
}


    return redirect()->route('admin.sanpham.list')->with('success', 'Cập nhật sản phẩm thành công!');
}

public function getByCategory($category_id)
{
    $products = Product::where('category_id', $category_id)
        ->where('trangthai', 1)
        ->orderBy('stt')
        ->get();

    return response()->json($products);
}

public function show($id)
{
    $product = Product::with('galleryImages')->findOrFail($id);
    return response()->json($product);
}
}

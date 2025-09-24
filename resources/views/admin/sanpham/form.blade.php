@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-3 px-4">
            <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" {{ isset($readonly) && $readonly ? 'onsubmit=return false;' : '' }}>
                @csrf
                @if ($formMethod === 'PUT')
                    @method('PUT')
                @endif

                <ul class="nav nav-tabs mb-3" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                            type="button" role="tab">
                            Thông tin sản phẩm
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button"
                            role="tab">
                            Thông tin SEO
                        </button>
                    </li>
                </ul>

                <div class="tab-content border p-3" id="productTabContent">
                    {{-- Tab 1: Thông tin sản phẩm --}}
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        {{-- Tên sản phẩm --}}
                        <div class="custom-form-row mb-3">
                            <label for="name">Tên sản phẩm</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $fields['name'] ?? '') }}" required {{ isset($readonly) && $readonly ? 'readonly' : '' }}>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="custom-form-row mb-3">
                            <label for="slug">Slug / SEO URL</label>
                            <input type="text" name="slug" id="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $fields['slug'] ?? '') }}">
                            @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- SKU --}}
                        <div class="custom-form-row mb-3">
                            <label for="sku">Mã sản phẩm (SKU)</label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                value="{{ old('sku', $fields['sku'] ?? '') }}">
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Kho hàng --}}
                        <div class="custom-form-row mb-3">
                            <label for="stock">Số lượng trong kho</label>
                            <input type="number" name="stock" class="form-control"
                                value="{{ old('stock', $fields['stock'] ?? 0) }}">
                        </div>

                        {{-- Danh mục --}}
                        <div class="custom-form-row mb-3">
                            <label for="category_id">Danh mục</label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $fields['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Giá gốc và KM --}}
                        <div class="custom-form-row mb-3">
                            
                                <label for="original_price">Giá gốc</label>
                                <input type="text" name="original_price" class="form-control"
                                    value="{{ old('original_price', number_format($fields['original_price'] ?? 0, 0, '', '.')) }}">
                            
                        </div>

                        <div class="custom-form-row mb-3">
                                <label for="sale_price">Giá khuyến mãi</label>
                                <input type="text" name="sale_price" class="form-control"
                                    value="{{ old('sale_price', number_format($fields['sale_price'] ?? 0, 0, '', '.')) }}">

                        </div>


                        {{-- Ảnh đại diện --}}
                        <div class="custom-form-row mb-3">
                            <label for="image">Ảnh đại diện</label>
                            <input type="file" name="main_image" class="form-control @error('image') is-invalid @enderror"
                                onchange="previewImage(event)">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="mt-2">
                                <img id="image-preview"
                                    src="{{ !empty($fields['main_image']) ? asset('storage/' . $fields['main_image']) : '#' }}"
                                    alt="Ảnh sản phẩm"
                                    style="max-width: 200px; {{ empty($fields['main_image']) ? 'display:none;' : '' }}">

                            </div>
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="gallery">Chi tiết ảnh</label>
                            <input type="file" name="gallery[]" class="form-control" multiple
                                onchange="previewGalleryImages(event)">

                            {{-- Preview ảnh mới chọn --}}
                            <div id="gallery-preview" class="mt-3 d-flex flex-wrap gap-2"></div>

                            {{-- Preview ảnh đã lưu (từ CSDL) --}}
                            @if ($product->galleryImages && $product->galleryImages->count())
                                <div class="mt-3 d-flex flex-wrap gap-2">
                                    @foreach ($product->galleryImages as $img)
                                        <div class="position-relative me-2 mb-2">
                                            <img src="{{ asset('storage/' . $img->image) }}" alt="Ảnh phụ"
                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc;">
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="status">Tình trạng hàng hóa</label>
                            <select name="status" id="status" class="form-select">
                                <option value="con_hang" {{ old('status', $fields['status'] ?? '') == 'con_hang' ? 'selected' : '' }}>Còn hàng</option>
                                <option value="het_hang" {{ old('status', $fields['status'] ?? '') == 'het_hang' ? 'selected' : '' }}>Hết hàng</option>
                                <option value="sap_ve" {{ old('status', $fields['status'] ?? '') == 'sap_ve' ? 'selected' : '' }}>Sắp về</option>
                            </select>
                        </div>

                        {{-- Chi tiết sản phẩm --}}
                        <div class="custom-form-row mb-3">
                            <label for="description">Chi tiết sản phẩm</label>
                            <x-tinymce id="description" name="description" class="tinymce-editor">
                                {{ old('description', $fields['description'] ?? '') }}
                            </x-tinymce>
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-success" id="next-to-meta">
                                Tiếp tục
                            </button>
                            <a href="{{ route('admin.sanpham.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                        </div>
                    </div>

                    {{-- Tab 2: SEO --}}
                    <div class="tab-pane fade" id="seo" role="tabpanel">
                        <div class="custom-form-row mb-3">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" class="form-control"
                                value="{{ old('meta_title', $fields['meta_title'] ?? '') }}">
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="meta_description">Meta Description</label>
                            <textarea name="meta_description" rows="3"
                                class="form-control">{{ old('meta_description', $fields['meta_description'] ?? '') }}</textarea>
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control"
                                value="{{ old('meta_keywords', $fields['meta_keywords'] ?? '') }}">
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" id="back-to-general">Quay lại thông tin sản phẩm</button>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                            
                        </div>


                    </div>
                </div>

                
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }

        // Tự tạo slug từ tên sản phẩm
        document.addEventListener('DOMContentLoaded', function () {
            const titleInput = document.querySelector('input[name="name"]');
            const slugInput = document.getElementById('slug');
            const metaTitleInput = document.getElementById('meta_title');

            function slugify(text) {
                return text.toString().toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/[^a-z0-9 -]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            titleInput.addEventListener('input', function () {
                slugInput.value = slugify(this.value);
                if (metaTitleInput) {
                    metaTitleInput.value = this.value;
                }
            });

            document.getElementById('next-to-seo').addEventListener('click', function () {
                const tab = new bootstrap.Tab(document.getElementById('seo-tab'));
                tab.show();
            });

            document.getElementById('back-to-general').addEventListener('click', function () {
                const tab = new bootstrap.Tab(document.getElementById('general-tab'));
                tab.show();
            });
        });

        let selectedGalleryFiles = [];

        function previewGalleryImages(event) {
            const newFiles = Array.from(event.target.files);
            selectedGalleryFiles = selectedGalleryFiles.concat(newFiles); // Gộp thêm ảnh mới vào mảng cũ

            // Reset lại input file để có thể chọn lại cùng ảnh nếu muốn

            // Hiển thị lại tất cả ảnh
            const previewContainer = document.getElementById('gallery-preview');
            previewContainer.innerHTML = '';

            selectedGalleryFiles.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('me-2', 'mb-2');

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.border = '1px solid #ccc';
                    img.style.borderRadius = '4px';

                    wrapper.appendChild(img);
                    previewContainer.appendChild(wrapper);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
@endpush
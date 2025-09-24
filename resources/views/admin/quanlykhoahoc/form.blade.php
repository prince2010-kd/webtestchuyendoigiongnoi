@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-2 pt-3 px-4">
            <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" {{ isset($readonly) && $readonly ? 'onsubmit=return false;' : '' }}>
                @csrf
                @if($formMethod === 'PUT')
                    @method('PUT')
                @endif
                <ul class="nav nav-tabs" id="postTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general"
                            type="button" role="tab">
                            Thông tin chung
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="meta-tab" data-bs-toggle="tab" data-bs-target="#meta" type="button"
                            role="tab">
                            Meta Options
                        </button>
                    </li>
                </ul>

                <div class="tab-content border p-3">
                    <!-- Thông tin chung -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="custom-form-row mb-3">
                            <label for="title">Tiêu đề bài viết</label>
                            <input type="text" name="tieu_de" id="tieu_de"
                                class="form-control @error('tieu_de') is-invalid @enderror"
                                value="{{ old('tieu_de', $fields['tieu_de'] ?? '') }}" {{ isset($readonly) ? 'readonly' : '' }} required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="slug">SEO URL</label>
                            <input type="text" name="slug" id="slug"
                                class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug', $fields['slug'] ?? '') }}">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="image">Ảnh đại diện</label>
                            <input type="file" name="hinh_anh" id="hinh_anh"
                                class="form-control @error('hinh_anh') is-invalid @enderror" accept="image/*"
                                onchange="previewLogo(event)">
                            <div class="logo-preview-container mt-2" style="max-width: 300px;">
                                <img id="logo-preview"
                                    src="{{ isset($fields['hinh_anh']) ? asset('storage/' . $fields['hinh_anh']) : '#' }}"
                                    style="{{ empty($fields['hinh_anh']) ? 'display:none;' : '' }}">
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="excerpt">Mô tả ngắn</label>
                            <textarea name="mo_ta_ngan" id="mo_ta_ngan"
                                class="form-control @error('mo_ta_ngan') is-invalid @enderror"
                                rows="3">{{ old('mo_ta_ngan', $fields['mo_ta_ngan'] ?? '') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Khu vực thêm cột -->
                        <hr>
                        <h5 class="fw-bold">Nội dung chi tiết theo cột</h5>
                        @php
                            $sections = is_array($fields['sections'] ?? null) ? $fields['sections'] : json_decode($fields['sections'] ?? '[]', true);
                        @endphp

                        <div id="section-container" class="d-flex flex-column gap-4">
                            @if (!empty($sections))
                                @foreach ($sections as $index => $section)
                                    <div class="border rounded p-3 bg-light-subtle shadow-sm">
                                        <div class="custom-form-row mb-3">
                                            <label class="form-label">Tiêu đề mục</label>
                                            <input type="text" name="sections[{{ $index }}][title]" class="form-control"
                                                value="{{ $section['title'] ?? '' }}" />
                                        </div>
                                        <div class="custom-form-row mb-3">
                                            <label class="form-label">Mô tả</label>
                                            <textarea name="sections[{{ $index }}][description]" class="form-control"
                                                rows="3">{{ $section['description'] ?? '' }}</textarea>
                                        </div>
                                        <div class="custom-form-row mb-3">
                                            <label class="form-label">Ảnh mục</label>
                                            <input type="file" name="sections[{{ $index }}][image]"
                                                class="form-control section-image-input" accept="image/*" />
                                            <div class="logo-preview-container mt-2" style="max-width: 300px;">
                                                <img class="section-img-preview"
                                                    src="{{ !empty($section['image']) ? asset('storage/' . $section['image']) : '#' }}"
                                                    style="{{ !empty($section['image']) ? '' : 'display: none;' }}; width: 100%;" />
                                            </div>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-sm btn-danger remove-section">Xóa mục</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" class="btn btn-outline-primary mt-3" id="add-section-btn">
                            + Thêm mục nội dung
                        </button>

                        <div class="custom-form-row mb-3 mt-4">
                            <label for="content" class="form-label-cus col-sm-2">Nội dung</label>
                            <x-tinymce id="noi_dung" name="noi_dung" class="tinymce-editor">
                                {{ old('noi_dung', $fields['noi_dung'] ?? '') }}
                            </x-tinymce>
                        </div>

                        <div class="d-flex justify-content-center gap-2 mt-3">
                            <button type="button" class="btn btn-success" id="next-to-meta">
                                Tiếp tục
                            </button>
                            <a href="{{ route('admin.danhmucbaiviet.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                        </div>
                    </div>

                    <!-- Meta Options -->
                    <div class="tab-pane fade" id="meta" role="tabpanel">
                        <div class="custom-form-row mb-3">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title"
                                class="form-control @error('meta_title') is-invalid @enderror"
                                value="{{ old('meta_title', $fields['meta_title'] ?? '') }}">
                            @error('meta_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="custom-form-row mb-3">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" name="meta_keywords" id="meta_keywords"
                                class="form-control @error('meta_keywords') is-invalid @enderror"
                                value="{{ old('meta_keywords', $fields['meta_keywords'] ?? '') }}">
                            @error('meta_keywords')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="custom-form-row mb-3">
                            <label for="meta_description">Meta Description</label>
                            <textarea name="meta_description" id="meta_description"
                                class="form-control @error('meta_description') is-invalid @enderror"
                                rows="3">{{ old('meta_description', $fields['meta_description'] ?? '') }}</textarea>
                            @error('meta_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="custom-form-row mb-3">
                            <label for="meta_new_keyword">Meta New Keyword</label>
                            <input type="text" name="meta_new_keyword" id="meta_new_keyword"
                                class="form-control @error('meta_new_keyword') is-invalid @enderror"
                                value="{{ old('meta_new_keyword', $fields['meta_new_keyword'] ?? '') }}">
                            @error('meta_new_keyword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" id="back-to-general">Quay lại thông tin
                                chung</button>
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
        function slugify(text) {
            return text.toString().toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')  // Loại bỏ dấu
                .replace(/đ/g, 'd')
                .replace(/\//g, '-')
                .replace(/[^\w\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function previewLogo(event) {
            const input = event.target;
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '{{ isset($fields["hinh_anh"]) ? asset("storage/" . $fields["hinh_anh"]) : "#" }}';
                preview.style.display = '{{ empty($fields["hinh_anh"]) ? "none" : "block" }}';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const titleInput = document.getElementById('tieu_de');
            const slugInput = document.getElementById('slug');
            const metaTitleInput = document.getElementById('meta_title');
            const excerptInput = document.getElementById('mo_ta_ngan');
            const metaDescriptionInput = document.getElementById('meta_description');
            const sectionContainer = document.getElementById('section-container');
            const addSectionBtn = document.getElementById('add-section-btn');

            // Tự động sinh slug và meta_title từ tiêu đề
            if (titleInput) {
                titleInput.addEventListener('input', function () {
                    const value = this.value;
                    if (slugInput) slugInput.value = slugify(value);
                    if (metaTitleInput) metaTitleInput.value = value;
                });
            }

            // Tự động cập nhật meta_description từ mô tả ngắn
            if (excerptInput && metaDescriptionInput) {
                excerptInput.addEventListener('input', function () {
                    const text = this.value.trim().substring(0, 200);
                    metaDescriptionInput.value = text;
                });
            }

            // Thêm mục nội dung động
            if (addSectionBtn && sectionContainer) {
                addSectionBtn.addEventListener('click', function () {
                    const index = sectionContainer.children.length;
                    const html = `
            <div class="border rounded p-3 bg-light-subtle shadow-sm">
                <div class="custom-form-row mb-3">
                    <label class="form-label">Tiêu đề mục</label>
                    <input type="text" name="sections[${index}][title]" class="form-control" />
                </div>
                <div class="custom-form-row mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="sections[${index}][description]" class="form-control" rows="3"></textarea>
                </div>
                <div class="custom-form-row mb-3">
                    <label class="form-label">Ảnh mục</label>
                    <input type="file" name="sections[${index}][image]" class="form-control section-image-input" accept="image/*" />
                     <div class="logo-preview-container mt-2" style="max-width: 300px;">
                        <img class="section-img-preview" style="display: none; width: 100%;" />
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-sm btn-danger remove-section">Xóa mục</button>
                </div>
            </div>
        `;

                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html;
                    sectionContainer.appendChild(wrapper);

                    // Xử lý nút "Xóa mục"
                    wrapper.querySelector('.remove-section').addEventListener('click', function () {
                        wrapper.remove();
                    });

                    // Xử lý hiển thị preview ảnh mục
                    const imageInput = wrapper.querySelector('.section-image-input');
                    const preview = wrapper.querySelector('.section-img-preview');
                    imageInput.addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (event) {
                                preview.src = event.target.result;
                                preview.style.display = 'block';
                            };
                            reader.readAsDataURL(file);
                        } else {
                            preview.src = '';
                            preview.style.display = 'none';
                        }
                    });
                });

            }

            // Chuyển tab
            document.getElementById('next-to-meta')?.addEventListener('click', function () {
                const metaTab = new bootstrap.Tab(document.getElementById('meta-tab'));
                metaTab.show();
            });

            document.getElementById('back-to-general')?.addEventListener('click', function () {
                const generalTab = new bootstrap.Tab(document.getElementById('general-tab'));
                generalTab.show();
            });
        });

        // Preview ảnh cho các mục đã có sẵn
document.querySelectorAll('.section-image-input').forEach(function (input) {
    const preview = input.closest('.custom-form-row').querySelector('.section-img-preview');
    input.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                preview.src = event.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    });
});
// Gắn sự kiện XÓA MỤC cho các phần đã render sẵn từ server
document.querySelectorAll('.remove-section').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const block = this.closest('.border.rounded');
        if (block) block.remove();
    });
});

    </script>
@endpush
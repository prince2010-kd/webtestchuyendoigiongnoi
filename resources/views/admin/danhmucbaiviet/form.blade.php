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
                            <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $fields['title'] ?? '') }}" {{ isset($readonly) && $readonly ? 'readonly' : '' }} required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="slug">SEO URL</label>
                            <input type="text" name="url" id="slug" class="form-control @error('url') is-invalid @enderror"
                                value="{{ old('url', $fields['url'] ?? '') }}">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="created_at">Ngày tạo</label>
                            <input type="date" name="created_at" id="created_at"
                                class="form-control @error('created_at') is-invalid @enderror"
                                value="{{ old('created_at', isset($fields['created_at']) ? \Carbon\Carbon::parse($fields['created_at'])->format('Y-m-d') : now()->format('Y-m-d')) }}">
                            @error('created_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="category_id">Chọn danh mục</label>
                            <select name="category_id" id="category_id"
                                class="form-select @error('category_id') is-invalid @enderror">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach ($danhmucbaiviet as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $fields['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="type" class="form-label">Loại bài viết</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">-- Chọn loại bài viết --</option>
                                <option value="tin-tuc" {{ old('type', $fields['type'] ?? '') == 'tin-tuc' ? 'selected' : '' }}>Tin tức</option>
                                <option value="su-kien" {{ old('type', $fields['type'] ?? '') == 'su-kien' ? 'selected' : '' }}>Sự kiện</option>
                                <option value="blog" {{ old('type', $fields['type'] ?? '') == 'blog' ? 'selected' : '' }}>Blog</option>
                                <option value="uu-dai" {{ old('type', $fields['type'] ?? '') == 'uu-dai' ? 'selected' : '' }}>Ưu đãi</option>
                                <option value="kinh-nghiem-hoc-ielts" {{ old('type', $fields['type'] ?? '') == 'kinh-nghiem-hoc-ielts' ? 'selected' : '' }}>Kinh nghiệm học ielts</option>
                            </select>
                        </div>


                        <div class="custom-form-row mb-3">
                            <label for="image">Ảnh đại diện</label>
                            <input type="file" name="image" id="image"
                                class="form-control @error('image') is-invalid @enderror" accept="image/*"
                                onchange="previewLogo(event)">
                            <div class="logo-preview-container mt-2" style="max-width: 300px;">
                                <img id="logo-preview"
                                    src="{{ !empty($fields['logo']) ? asset('storage/' . $fields['logo']) : '#' }}"
                                    alt="Ảnh đại diện"
                                    style="max-width: 100%; max-height: 100%; {{ empty($fields['logo']) ? 'display:none;' : '' }}">


                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3">
                            <label for="excerpt">Mô tả ngắn</label>
                            <textarea name="short_description" id="excerpt"
                                class="form-control @error('short_description') is-invalid @enderror"
                                rows="3">{{ old('short_description', $fields['short_description'] ?? '') }}</textarea>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="custom-form-row mb-3 video-youtube">
                            <label for="youtube_url" class="form-label">Link YouTube</label>
                            <input type="text" name="youtube_url" id="youtube_url"
                                class="form-control @error('youtube_url') is-invalid @enderror"
                                value="{{ old('youtube_url', $fields['youtube_url'] ?? '') }}">
                            @error('youtube_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            {{-- Preview --}}
                            <div class="mt-2 position-relative" id="youtube-preview-container"
                                style="max-width: 100%; max-height: 400px;">
                                <div id="youtube-thumbnail-wrapper" style="position: relative; display: none;">
                                    <img id="youtube-thumbnail-preview"
                                        src="{{ !empty($fields['youtube_url']) && preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|.*v=))([\w\-]+)/', $fields['youtube_url'], $m) ? 'https://img.youtube.com/vi/' . $m[1] . '/hqdefault.jpg' : '' }}"
                                        alt="Thumbnail"
                                        style="width: 100%; max-height: 400px; object-fit: cover; cursor: pointer;">
                                    <div id="play-button-overlay" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                        background-color: rgba(0, 0, 0, 0.5); border-radius: 50%; padding: 20px; cursor: pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#fff"
                                            class="bi bi-play-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M11.596 8.697l-6.363 3.692A.5.5 0 0 1 4 11.985V4.015a.5.5 0 0 1 .76-.424l6.363 3.692a.5.5 0 0 1 0 .848z" />
                                        </svg>
                                    </div>
                                </div>

                                <div id="youtube-video-wrapper" style="display: none;">
                                    <iframe id="youtube-iframe" width="100%" height="400" frameborder="0"
                                        allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>


                        <div class="custom-form-row mb-3">
                            <label for="content" class="form-label-cus col-sm-2">Nội dung</label>
                            <x-tinymce id="content" name="content" class="tinymce-editor" required style="flex:1;">
                                {{ old('content', $fields['content'] ?? '') }}
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
        // Tự động cập nhật meta title từ tiêu đề
        document.getElementById('title').addEventListener('input', function () {
            document.getElementById('meta_title').value = this.value;
        });

        // Tự động cập nhật meta description từ mô tả ngắn
        document.getElementById('excerpt').addEventListener('input', function () {
            let text = this.value.trim().substring(0, 200);
            document.getElementById('meta_description').value = text;
        });

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
                preview.src = '{{ isset($fields["logo"]) ? asset("storage/" . $fields["logo"]) : "#" }}';
                preview.style.display = '{{ empty($fields["logo"]) ? "none" : "block" }}';

            }
        }

        document.getElementById('next-to-meta').addEventListener('click', function () {
            const metaTabButton = document.getElementById('meta-tab');
            const metaTab = new bootstrap.Tab(metaTabButton);
            metaTab.show();
        });

        document.getElementById('next-to-meta').addEventListener('click', function () {
            const metaTabButton = document.getElementById('meta-tab');

            // Gỡ bỏ class 'disabled'
            metaTabButton.classList.remove('disabled');

            // Chuyển sang tab Meta
            const metaTab = new bootstrap.Tab(metaTabButton);
            metaTab.show();
        });

        document.getElementById('back-to-general').addEventListener('click', function () {
            const generalTabButton = document.getElementById('general-tab');
            const generalTab = new bootstrap.Tab(generalTabButton);
            generalTab.show();
        });

        function slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .normalize('NFD')                         // Bỏ dấu tiếng Việt
                .replace(/[\u0300-\u036f]/g, '')          // Loại bỏ phần dấu
                .replace(/đ/g, 'd')                       // Chuyển đ -> d
                .replace(/\//g, '-')                      // Chuyển / thành -
                .replace(/[^\w\s-]/g, '')                 // Loại bỏ ký tự đặc biệt, giữ a-z, 0-9, _, khoảng trắng, -
                .trim()
                .replace(/\s+/g, '-')                     // Chuyển khoảng trắng thành dấu -
                .replace(/-+/g, '-')                      // Loại bỏ dấu - thừa
                .replace(/^-+|-+$/g, '');                 // Xóa dấu - ở đầu và cuối
        }

        document.addEventListener('DOMContentLoaded', function () {
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');
            const metaTitleInput = document.getElementById('meta_title'); // Gắn meta_title nếu có

            if (titleInput && slugInput) {
                titleInput.addEventListener('input', function () {
                    const slug = slugify(this.value);
                    slugInput.value = slug;
                    if (metaTitleInput) {
                        metaTitleInput.value = this.value;
                    }
                });
            }
        });


        function extractYouTubeId(url) {
            const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|.*v=))([\w\-]+)/);
            return match ? match[1] : null;
        }

        function updateThumbnail(videoId) {
            const thumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
            const img = new Image();
            img.onload = function () {
                $('#youtube-thumbnail-preview').attr('src', thumbnail).show();
                $('#play-button-overlay').show();
                $('#youtube-thumbnail-wrapper').show();
                $('#youtube-video-wrapper').hide();
                $('#youtube-iframe').attr('src', '');
            };
            img.onerror = function () {
                resetPreview();
            };
            img.src = thumbnail;
        }

        function resetPreview() {
            $('#youtube-thumbnail-preview').hide().attr('src', '');
            $('#play-button-overlay').hide();
            $('#youtube-thumbnail-wrapper').hide();
            $('#youtube-video-wrapper').hide();
            $('#youtube-iframe').attr('src', '');
        }

        $(document).ready(function () {
            $('#youtube_url').on('input', function () {
                const url = $(this).val();
                const videoId = extractYouTubeId(url);
                if (videoId) {
                    updateThumbnail(videoId);
                } else {
                    resetPreview();
                }
            });

            $('#play-button-overlay, #youtube-thumbnail-preview').on('click', function () {
                const url = $('#youtube_url').val();
                const videoId = extractYouTubeId(url);
                if (videoId) {
                    const iframeSrc = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                    $('#youtube-iframe').attr('src', iframeSrc);
                    $('#youtube-thumbnail-wrapper').hide();
                    $('#youtube-video-wrapper').show();
                } else {
                    alert('Vui lòng nhập link YouTube hợp lệ!');
                }
            });

            // Nếu đã có giá trị ban đầu (edit form)
            const initUrl = $('#youtube_url').val();
            const initId = extractYouTubeId(initUrl);
            if (initId) {
                updateThumbnail(initId);
            } else {
                resetPreview();
            }
        });
    </script>
@endpush
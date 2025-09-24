@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-2 pt-3">
            <form id="categoryForm" action="{{ $formAction }}" method="POST">
                @csrf
                @if($formMethod === 'PUT')
                    @method('PUT')
                @endif

                <div class="custom-form-row mb-3">
                    <label for="title" class="form-label-cus  col-sm-2">Tiêu đề</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="title" name="title"
                            value="{{ old('title', $category->title ?? '') }}" required @if(!empty($readonly)) disabled
                            @endif>
                    </div>
                </div>
                <div class="custom-form-row mb-3">
                    <label for="slug">SEO name</label>
                    <input type="text" name="seo-name" id="slug"
                        class="form-control @error('seo-name') is-invalid @enderror"
                        value="{{ old('seo-name', $category->{"seo-name"} ?? '') }}">
                    @error('seo-name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="custom-form-row mb-3">
                    <label for="content" class="form-label-cus col-sm-2">Nội dung</label>
                    <x-tinymce id="content" name="content" class="tinymce-editor" required style="flex:1;">
                        {{ old('content', $fields['content'] ?? '') }}
                    </x-tinymce>

                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                    <a href="{{ route('admin.category.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function slugify(str) {
            str = str.toLowerCase();

            // Thay thế ngày có dấu / thành -
            str = str.replace(/\//g, '-');

            // Bỏ dấu tiếng Việt
            str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            // Chuyển đ -> d
            str = str.replace(/đ/g, 'd');

            // Loại bỏ ký tự không mong muốn (trừ dấu gạch ngang)
            str = str.replace(/[^a-z0-9\s-]/g, '');

            // Chuyển khoảng trắng thành -
            str = str.trim().replace(/\s+/g, '-');

            // Bỏ gạch ngang thừa
            return str.replace(/-+/g, '-');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const titleInput = document.getElementById('title');
            const slugInput = document.getElementById('slug');

            titleInput.addEventListener('input', function () {
                slugInput.value = slugify(titleInput.value);
            });
        });
    </script>

@endpush
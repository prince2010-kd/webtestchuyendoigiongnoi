@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-2 pt-3">
            <form id="sliderform" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($formMethod === 'PUT')
                    @method('PUT')
                @endif

                <div class="custom-form-row mb-3">
                    <label for="title">Tiêu đề</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $fields['title'] ?? '') }}" {{ isset($readonly) && $readonly ? 'readonly' : '' }} required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @php
                    $imagePath = $fields['image'] ?? null;
                    $imageUrl = $imagePath ? asset('storage/' . $imagePath) : '#';
                    $imageStyle = $imagePath ? '' : 'display: none;';
                @endphp

                <div class="custom-form-row mb-3" style="align-items: center;">
                    <label for="image">Hình ảnh</label>
                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror"
                        accept="image/*" onchange="previewLogo(event)" style="flex:1;" @if($formMethod === 'POST') required @endif>
                    <div class="logo-preview-container mt-2" style="max-width: 300px;">
                        <img id="logo-preview"
                            src="{{ $imageUrl }}"
                            alt="Logo Preview"
                            style="max-width: 100%; max-height: 100%; {{ $imageStyle }}">
                    </div>
                    @error('image')
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
                    <button type="submit" class="btn btn-primary">Lưu</button>
                    <a href="{{ route('admin.slider.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewLogo(event) {
            const input = event.target;
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush

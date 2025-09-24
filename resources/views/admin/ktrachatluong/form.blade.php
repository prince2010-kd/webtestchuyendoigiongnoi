@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-3">
            <form id="qualityCheckForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($formMethod === 'PUT')
                    @method('PUT')
                @endif

                <!-- Tiêu đề -->
                <div class="custom-form-row mb-3">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $fields['title'] ?? '') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="custom-form-row mb-3">
                    <label for="slug" class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug', $fields['slug'] ?? '') }}">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Hình ảnh -->
                <div class="custom-form-row mb-3" style="align-items: center;">
                    <label for="image" class="form-label">Hình ảnh</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*"
                        onchange="previewImage(event)" style="flex:1;" {{ empty($fields['image']) ? 'required' : '' }}>
                    <div class="logo-preview-container mt-2">
                        <img id="logo-preview" src="{{ !empty($fields['image']) ? asset($fields['image']) : '#' }}"
                            alt="Hình ảnh xem trước"
                            style="max-width: 100%; max-height: 250px; {{ empty($fields['image']) ? 'display:none;' : '' }}">
                    </div>
                </div>

                <!-- Nội dung -->
                <div class="custom-form-row mb-3">
                    <label for="content" class="form-label">Nội dung</label>
                    <x-tinymce id="content" name="content" class="tinymce-editor">
                        {{ old('content', $fields['content'] ?? '') }}
                    </x-tinymce>
                </div>

                <div class="custom-form-row mb-3">
                    <label for="question_set_id" class="form-label">Chọn đề thi</label>
                    <select name="question_set_id" id="question_set_id" class="form-select">
                        <option value="">-- Không chọn --</option>
                        @foreach($questionSets as $id => $title)
                            <option value="{{ $id }}" {{ old('question_set_id', $fields['question_set_id'] ?? '') == $id ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                    <a href="{{ route('admin.quality.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
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
                preview.src = '{{ isset($fields["image"]) ? asset($fields["image"]) : "#" }}';
                preview.style.display = '{{ empty($fields["image"]) ? "none" : "block" }}';
            }
        }
    </script>
@endpush
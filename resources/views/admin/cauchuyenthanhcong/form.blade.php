@extends('layouts.user')

@section('content')
<div class="container-xxl my-4">
    <h3>{{ $formTitle }}</h3>
    <div class="card p-3">
        <form id="successStoryForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($formMethod === 'PUT')
                @method('PUT')
            @endif

            <!-- Tên học viên -->
            <div class="custom-form-row mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" name="name" id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $fields['name'] ?? '') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Trường -->
            <div class="custom-form-row mb-3">
                <label for="school" class="form-label">Trường</label>
                <input type="text" name="school" id="school"
                       class="form-control @error('school') is-invalid @enderror"
                       value="{{ old('school', $fields['school'] ?? '') }}">
                @error('school')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Điểm IELTS -->
            <div class="custom-form-row mb-3">
                <label for="ielts_score" class="form-label">Điểm IELTS</label>
                <input type="text" name="ielts_score" id="ielts_score"
                       class="form-control @error('ielts_score') is-invalid @enderror"
                       value="{{ old('ielts_score', $fields['ielts_score'] ?? '') }}">
                @error('ielts_score')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nội dung -->
            <div class="custom-form-row mb-3">
    <label for="content" class="form-label-cus col-sm-2">Nội dung</label>
    <x-tinymce id="content" name="content" class="tinymce-editor" required style="flex:1;">
        {{ old('content', $fields['content'] ?? '') }}
    </x-tinymce>
    @error('content')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

            <!-- Hình ảnh -->
            <div class="custom-form-row mb-3">
                <label for="image" class="form-label">Hình ảnh (ảnh tròn)</label>
                <input type="file" name="image" id="image"
                       class="form-control @error('image') is-invalid @enderror"
                       accept="image/*" onchange="previewLogo(event)" {{ empty($fields['image']) ? 'required' : '' }}>
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="logo-preview-container mt-2">
                    <img id="logo-preview"
                         src="{{ !empty($fields['image']) ? asset($fields['image']) : '#' }}"
                         alt="Image Preview"
                         style="max-width: 150px; max-height: 150px; border-radius: 50%; {{ empty($fields['image']) ? 'display:none;' : '' }}">
                </div>
            </div>

            <!-- Submit -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                <a href="{{ route('admin.thanhcong.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
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
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '{{ isset($fields["image"]) ? asset($fields["image"]) : "#" }}';
            preview.style.display = '{{ empty($fields["image"]) ? "none" : "block" }}';
        }
    }
</script>
@endpush

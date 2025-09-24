@extends('layouts.user')

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3>{{ $formTitle }}</h3>
    <div class="card p-3">
        <form id="giaovienForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($formMethod === 'PUT')
                @method('PUT')
            @endif

            <!-- Họ tên -->
            <div class="custom-form-row mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" name="name" id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $fields['name'] ?? '') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Chức vụ -->
            <div class="custom-form-row mb-3">
                <label for="position" class="form-label">Chức vụ</label>
                <input type="text" name="position" id="position"
                    class="form-control @error('position') is-invalid @enderror"
                    value="{{ old('position', $fields['position'] ?? '') }}" required>
                @error('position')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Facebook -->
            <div class="custom-form-row mb-3">
                <label for="facebook_url" class="form-label">Facebook</label>
                <input type="url" name="facebook_url" id="facebook_url"
                    class="form-control @error('facebook_url') is-invalid @enderror"
                    value="{{ old('facebook_url', $fields['facebook_url'] ?? '') }}">
                @error('facebook_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- LinkedIn -->
            <div class="custom-form-row mb-3">
                <label for="linkedin_url" class="form-label">LinkedIn</label>
                <input type="url" name="linkedin_url" id="linkedin_url"
                    class="form-control @error('linkedin_url') is-invalid @enderror"
                    value="{{ old('linkedin_url', $fields['linkedin_url'] ?? '') }}">
                @error('linkedin_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Hình ảnh -->
             <div class="custom-form-row" style="align-items: center;">
                    <label for="logo">Hình ảnh</label>
                    <input type="file" name="image" id="image" class="form-control" accept="image/*"
                        onchange="previewLogo(event)" style="flex:1;" required>
                    <div class="logo-preview-container">
                        <img id="logo-preview" src="{{ $fields['image'] ?? '#' }}" alt="Logo Preview"
                            style="max-width: 100%; max-height: 100%; {{ empty($fields['image']) ? 'display:none;' : '' }}">
                    </div>
            </div>

            <!-- Nút submit -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                <a href="{{ route('admin.giaovien.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
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
                preview.src = '{{ isset($fields["image"]) ? asset("storage/" . $fields["image"]) : "#" }}';
                preview.style.display = '{{ empty($fields["image"]) ? "none" : "block" }}';
            }
        }
    </script>
@endpush

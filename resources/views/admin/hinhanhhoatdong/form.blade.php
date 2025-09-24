@extends('layouts.user')

@section('content')
<div class="container-xxl my-4">
    <h3>{{ $formTitle }}</h3>
    <div class="card p-3">
        <form id="hoatdongForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($formMethod === 'PUT')
                @method('PUT')
            @endif

            <!-- Alt text -->
            <div class="custom-form-row mb-3">
                <label for="alt_text" class="form-label">Alt text (mô tả hình)</label>
                <input type="text" name="alt_text" id="alt_text"
                       class="form-control @error('alt_text') is-invalid @enderror"
                       value="{{ old('alt_text', $fields['alt_text'] ?? '') }}">
                @error('alt_text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Hình ảnh -->
            <div class="custom-form-row" style="align-items: center;">
                <label for="image" class="form-label">Hình ảnh</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*"
                       onchange="previewLogo(event)" style="flex:1;" {{ empty($fields['image']) ? 'required' : '' }}>
                <div class="logo-preview-container mt-2">
                    <img id="logo-preview"
                         src="{{ !empty($fields['image']) ? asset($fields['image']) : '#' }}"
                         alt="Logo Preview"
                         style="max-width: 100%; max-height: 100%; {{ empty($fields['image']) ? 'display:none;' : '' }}">
                </div>
            </div>

            <!-- Submit -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                <a href="{{ route('admin.hinhanhhdong.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
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

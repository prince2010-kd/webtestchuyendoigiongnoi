@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-3">
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($formMethod === 'PUT')
                    @method('PUT')
                @endif

                <!-- Tiêu đề -->
                <div class="custom-form-row mb-3">
                    <label for="tieu_de" class="form-label">Tiêu đề</label>
                    <input type="text" name="tieu_de" id="tieu_de"
                        class="form-control @error('tieu_de') is-invalid @enderror"
                        value="{{ old('tieu_de', $fields['tieu_de'] ?? '') }}" required>
                    @error('tieu_de') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Mô tả -->
                <div class="custom-form-row mb-3">
                    <label for="content" class="form-label-cus col-sm-2">Nội dung</label>
                    <x-tinymce id="content" name="content" class="tinymce-editor" required style="flex:1;">
                        {{ old('content', $fields['content'] ?? '') }}
                    </x-tinymce>

                </div>

                <!-- Loại -->
                <div class="custom-form-row mb-3">
                    <label for="loai_id" class="form-label">Loại khóa học</label>
                    <select name="loai_id" id="loai_id" class="form-control">
                        <option value="">-- Chọn loại khóa học --</option>
                        @foreach($dsLoai as $loai)
                            <option value="{{ $loai->id }}" {{ old('loai_id', $fields['loai_id'] ?? '') == $loai->id ? 'selected' : '' }}>
                                {{ $loai->ten }}
                            </option>
                        @endforeach
                    </select>
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
                    <button type="submit" class="btn btn-primary">{{ $submitButton }}</button>
                    <a href="{{ route('admin.khoahoc.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            const preview = document.getElementById('preview');
            if (event.target.files.length > 0) {
                preview.src = URL.createObjectURL(event.target.files[0]);
                preview.style.display = 'block';
            }
        }
    </script>
@endpush
@extends('layouts.user')

@section('content')
    @php
        $selectedPositions = old('position') ?? ($fields['position'] ?? []);
    @endphp

    <div class="container-xxl my-4 vh-100">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-3">
            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if($formMethod === 'PUT')
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

                <!-- URL -->
                <div class="custom-form-row mb-3">
                    <label for="url" class="form-label">URL</label>
                    <input type="text" name="url" id="url" class="form-control @error('url') is-invalid @enderror"
                        value="{{ old('url', $fields['url'] ?? '') }}">
                    @error('url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Vị trí -->
                <!-- Vị trí hiển thị -->
                <div class="custom-form-row mb-3">
                    <label for="position" class="form-label" style="cursor: pointer">Vị trí hiển thị</label>
                    <select name="position[]" id="position" class="form-select" multiple>
                        <option value="top" {{ in_array('top', $selectedPositions) ? 'selected' : '' }}>Menu trên</option>
                        <option value="footer" {{ in_array('footer', $selectedPositions) ? 'selected' : '' }}>Menu chân
                            trang</option>
                        <option value="sidebar" {{ in_array('sidebar', $selectedPositions) ? 'selected' : '' }}>Menu bên
                        </option>
                        <option value="mobile" {{ in_array('mobile', $selectedPositions) ? 'selected' : '' }}>Menu di động
                        </option>
                    </select>
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Menu cha -->
                <div class="custom-form-row mb-3">
                    <label for="parent_id" class="form-label">Menu cha</label>
                    <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                        <option value="">-- Không chọn --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id', $fields['parent_id'] ?? '') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->title }}
                            </option>

                            @foreach($parent->children as $child)
                                <option value="{{ $child->id }}" {{ old('parent_id', $fields['parent_id'] ?? '') == $child->id ? 'selected' : '' }}>
                                    -- {{ $child->title }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="custom-form-row mb-3">
                    <label for="footer_column" class="form-label">Cột hiển thị ở footer</label>
                    <select name="footer_column" id="footer_column"
                        class="form-select @error('footer_column') is-invalid @enderror">
                        <option value="">-- Không hiển thị hoặc không chia cột --</option>
                        <option value="2" {{ old('footer_column', $fields['footer_column'] ?? '') == 2 ? 'selected' : '' }}>
                            Cột 2</option>
                        <option value="3" {{ old('footer_column', $fields['footer_column'] ?? '') == 3 ? 'selected' : '' }}>
                            Cột 3</option>
                    </select>
                    @error('footer_column')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <!-- Nút submit -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                    <a href="{{ route('admin.menufrontend.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Choices('#position', {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Chọn vị trí hiển thị',
                searchEnabled: false,
            });
        });
    </script>

@endpush
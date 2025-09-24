@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-3">
            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if ($formMethod === 'PUT')
                    @method('PUT')
                @endif

                {{-- Tiêu đề bộ đề --}}
                <div class="custom-form-row mb-3">
                    <label for="title" class="form-label">Tên bộ đề</label>
                    <input type="text" name="title" id="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $fields['title'] ?? '') }}">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mô tả --}}
                <div class="custom-form-row mb-3">
                    <label for="description" class="form-label">Mô tả (tuỳ chọn)</label>
                    <textarea name="description" id="description" rows="3"
                        class="form-control">{{ old('description', $fields['description'] ?? '') }}</textarea>
                </div>

                {{-- Thời gian làm bài --}}
                <div class="custom-form-row mb-3">
                    <label for="duration" class="form-label">Thời gian làm bài (phút)</label>
                    <input type="number" name="duration" id="duration"
                        class="form-control" min="1"
                        value="{{ old('duration', $fields['duration'] ?? '') }}">
                </div>

                {{-- Nút submit --}}
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                    <a href="{{ route('admin.questionset.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

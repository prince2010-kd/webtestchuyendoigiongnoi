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

            {{-- Câu hỏi liên kết --}}
            <div class="custom-form-row mb-3">
                <label for="question_id" class="form-label">Thuộc câu hỏi</label>
                <select name="question_id" id="question_id" class="form-select @error('question_id') is-invalid @enderror">
                    <option value="">-- Chọn câu hỏi --</option>
                    @foreach($questions as $question)
                        <option value="{{ $question->id }}"
                            {{ old('question_id', $fields['question_id'] ?? '') == $question->id ? 'selected' : '' }}>
                            {{ Str::limit($question->content, 100) }}
                        </option>
                    @endforeach
                </select>
                @error('question_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nhãn đáp án (A, B, C...) --}}
            <div class="custom-form-row mb-3">
                <label for="label" class="form-label">Nhãn đáp án (A, B, C...)</label>
                <input type="text" name="label" id="label" maxlength="1"
                       class="form-control @error('label') is-invalid @enderror"
                       value="{{ old('label', $fields['label'] ?? '') }}">
                @error('label')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nội dung đáp án --}}
            <div class="custom-form-row mb-3">
                <label for="text" class="form-label">Nội dung đáp án</label>
                <textarea name="text" id="text" rows="3"
                          class="form-control @error('text') is-invalid @enderror">{{ old('text', $fields['text'] ?? '') }}</textarea>
                @error('text')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nút submit --}}
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                <a href="{{ route('admin.questionoption.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection

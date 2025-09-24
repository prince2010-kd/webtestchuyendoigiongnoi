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

                @php
                    $selectedId = old('question_set_id', $fields['question_set_id'] ?? '');
                @endphp

                <div class="custom-form-row mb-3">
                    <label for="question_set_id" class="form-label">Thuộc bộ đề</label>
                    <select name="question_set_id" id="question_set_id" class="form-select">
                        <option value="">-- Chọn bộ đề --</option>
                        @foreach($questionSets as $set)
                            <option value="{{ $set->id }}" {{ (string) $selectedId === (string) $set->id ? 'selected' : '' }}>
                                {{ $set->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Loại câu hỏi --}}
                <div class="custom-form-row mb-3">
                    <label for="type" class="form-label">Loại câu hỏi</label>
                    <select name="type" id="question_type" class="form-select">
                        <option value="multiple_choice" {{ old('type', $fields['type'] ?? '') === 'multiple_choice' ? 'selected' : '' }}>Chọn đáp án</option>
                        <option value="short_answer" {{ old('type', $fields['type'] ?? '') === 'short_answer' ? 'selected' : '' }}>Điền từ</option>
                    </select>
                </div>

                <div class="custom-form-row mb-3">
                    <label for="cau" class="form-label">Câu hỏi số</label>
                    <input type="number" name="cau" id="cau" class="form-control" min="1"
                        value="{{ old('cau', $fields['cau'] ?? '') }}">
                </div>


                {{-- Nội dung câu hỏi --}}
                <div class="custom-form-row mb-3">
                    <label for="content" class="form-label">Nội dung câu hỏi</label>
                    <textarea name="content" id="content" rows="4"
                        class="form-control @error('content') is-invalid @enderror">{{ old('content', $fields['content'] ?? '') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Multiple Choice Section --}}
                <div class="custom-form-row mb-3" id="multiple_choice_fields" style="display: none;">
                    <label class="form-label mb-2">Đáp án lựa chọn</label>

                    @foreach(['A', 'B', 'C', 'D'] as $label)
                        <div class="d-flex align-items-center mb-2">
                            <label class="me-2" style="width: 80px;">{{ $label }}.</label>
                            <input type="text" name="options[{{ $label }}]" class="form-control"
                                value="{{ old("options.$label", $fields['options'][$label] ?? '') }}">
                        </div>
                    @endforeach

                    <div class="d-flex align-items-center mb-2">
                        <label class="me-2" style="width: 80px;">Đáp án đúng</label>
                        <select name="correct_answer" class="form-select w-auto">
                            <option value="">-- Chọn --</option>
                            @foreach(['A', 'B', 'C', 'D'] as $label)
                                <option value="{{ $label }}" {{ old('correct_answer', $fields['correct_answer'] ?? '') === $label ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>


                {{-- Short Answer Section --}}
                <div class="custom-form-row mb-3" id="short_answer_fields" style="display: none;">
                    <label class="form-label mb-2">Đáp án điền từ</label>

                    <div id="gap-fill-wrapper">
                        @if (!empty($fields['gaps']))
                            @foreach($fields['gaps'] as $i => $gap)
                                <div class="d-flex align-items-center mb-2 gap-item">
                                    <label class="me-2" style="width: 80px;">Chỗ {{ $i }}</label>
                                    <input type="text" name="gaps[{{ $i }}]" class="form-control"
                                        value="{{ old("gaps.$i", $gap) }}">
                                    <button type="button" class="btn btn-danger btn-sm ms-2 remove-gap">X</button>
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex align-items-center mb-2 gap-item">
                                <label class="me-2" style="width: 80px;">Chỗ 1</label>
                                <input type="text" name="gaps[1]" class="form-control">
                                <button type="button" class="btn btn-danger btn-sm ms-2 remove-gap">X</button>
                            </div>
                        @endif
                    </div>

                    <button type="button" class="btn btn-sm btn-primary mt-2" id="add-gap">+ Thêm chỗ điền</button>
                </div>

                {{-- Submit --}}
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                    <a href="{{ route('admin.question.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleQuestionFields() {
            const type = document.getElementById('question_type').value;
            document.getElementById('multiple_choice_fields').style.display = (type === 'multiple_choice') ? 'block' : 'none';
            document.getElementById('short_answer_fields').style.display = (type === 'short_answer') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('question_type').addEventListener('change', toggleQuestionFields);
            toggleQuestionFields(); // On page load
        });

        $(document).ready(function () {
            let gapIndex = $('#gap-fill-wrapper .gap-item').length || 1;

            $('#add-gap').click(function () {
                gapIndex++;
                let html = `
                        <div class="d-flex align-items-center mb-2 gap-item">
                            <label class="me-2" style="width: 80px;">Chỗ ${gapIndex}</label>
                            <input type="text" name="gaps[${gapIndex}]" class="form-control">
                            <button type="button" class="btn btn-danger btn-sm ms-2 remove-gap">X</button>
                        </div>
                    `;
                $('#gap-fill-wrapper').append(html);
            });

            $(document).on('click', '.remove-gap', function () {
                $(this).closest('.gap-item').remove();
            });
        });

    </script>
@endpush
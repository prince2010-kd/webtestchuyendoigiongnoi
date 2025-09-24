@extends('layouts.user')

@section('content')
<div class="container-xxl my-4">
    <h3>{{ $formTitle }}</h3>
    <div class="card p-3">
        <form id="formAnhNgu" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($formMethod === 'PUT')
                @method('PUT')
            @endif

            <!-- Tiêu đề -->
            <div class="custom-form-row mb-3">
                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $fields['title'] ?? '') }}">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr><hr>
            <h5 class="fw-bold">Nội dung chi tiết theo cột</h5>

            @php
                $sections = is_array($fields['sections'] ?? null) ? $fields['sections'] : json_decode($fields['sections'] ?? '[]', true);
            @endphp

            <div id="section-container" class="d-flex flex-column gap-4">
               @foreach ($sections as $index => $section)
<div class="section-block border rounded p-3 bg-light-subtle shadow-sm">
    <div class="custom-form-row mb-3">
        <label class="form-label">Tiêu đề</label>
        <input type="text" name="sections[{{ $index }}][title]" class="form-control section-title"
            value="{{ $section['title'] ?? '' }}" />
    </div>
    <div class="custom-form-row mb-3">
        <label class="form-label">Slug</label>
        <input type="text" name="sections[{{ $index }}][slug]" class="form-control section-slug"
            value="{{ $section['slug'] ?? '' }}" />
    </div>
    <div class="custom-form-row mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="sections[{{ $index }}][description]" class="form-control section-description">{{ $section['description'] ?? '' }}</textarea>
    </div>
    <div class="custom-form-row mb-3">
        <label class="form-label">Hình ảnh</label>
        <input type="file" name="sections[{{ $index }}][image]" class="form-control section-image-input"
            accept="image/*" />
        <div class="logo-preview-container mt-2" style="max-width: 300px;">
            <img class="section-img-preview"
                src="{{ !empty($section['image']) ? asset('storage/' . $section['image']) : '#' }}"
                style="{{ !empty($section['image']) ? '' : 'display: none;' }}; width: 100%;" />
        </div>
    </div>
    <div class="text-center mt-4">
        <button type="button" class="btn btn-sm btn-danger remove-section">Xóa mục</button>
    </div>
</div>
@endforeach

            </div>

            <button type="button" class="btn btn-outline-primary mt-3" id="add-section-btn">
                + Thêm mục nội dung
            </button>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                <a href="{{ route('admin.anhngu.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
   

    const sectionContainer = document.getElementById('section-container');
    const form = document.getElementById('formAnhNgu');
    const addBtn = document.getElementById('add-section-btn');

    // Sự kiện XÓA MỤC - dùng event delegation
    sectionContainer.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-section')) {


            const section = e.target.closest('.section-block');
            if (section) {
                section.remove();
                updateSectionNames();
            } else {
               
            }
        }
    });

    // Thêm mới section
    addBtn.addEventListener('click', function () {

        const html = `
            <div class="section-block border rounded p-3 bg-light-subtle shadow-sm">
                <div class="custom-form-row mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" class="form-control section-title" />
                </div>
                <div class="custom-form-row mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text" class="form-control section-slug" />
                </div>
                <div class="custom-form-row mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control section-description"></textarea>
                </div>
                <div class="custom-form-row mb-3">
                    <label class="form-label">Ảnh mục</label>
                    <input type="file" class="form-control section-image-input" accept="image/*" />
                    <div class="logo-preview-container mt-2" style="max-width: 300px;">
                        <img class="section-img-preview" style="display: none; width: 100%;" />
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-sm btn-danger remove-section">Xóa mục</button>
                </div>
            </div>
        `;
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        const newSection = wrapper.firstElementChild;
        sectionContainer.appendChild(newSection);

        // Gắn preview ảnh
        const input = newSection.querySelector('.section-image-input');
        const preview = newSection.querySelector('.section-img-preview');
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        });

        updateSectionNames();
    });

    // Trước khi submit form, cập nhật lại name[]
    form.addEventListener('submit', function () {
        updateSectionNames();
    });

    // Cập nhật lại name cho các section
    function updateSectionNames() {
        const sections = document.querySelectorAll('.section-block');
        sections.forEach((section, index) => {
            const titleInput = section.querySelector('.section-title');
            const slugInput = section.querySelector('.section-slug');
            const imageInput = section.querySelector('.section-image-input');
            const descriptionInput = section.querySelector('.section-description');

            if (titleInput) titleInput.name = `sections[${index}][title]`;
            if (slugInput) slugInput.name = `sections[${index}][slug]`;
            if (imageInput) imageInput.name = `sections[${index}][image]`;
            if (descriptionInput) descriptionInput.name = `sections[${index}][description]`;
        });
    }
});
</script>
@endpush




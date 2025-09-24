@extends('layouts.user')

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3>{{ $formTitle }}</h3>
    <div class="card p-2 pt-3">
        <form id="stageForm" action="{{ $formAction }}" method="POST">
            @csrf
            @if($formMethod === 'PUT')
            @method('PUT')
            @endif

            <div class="row mb-3 col-form-label col-form-label-top">
                <label for="title" class="form-label-cus  col-sm-2">Tiêu chặng</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="title" name="title"
                        value="{{ old('title', $stage->title ?? '') }}" required @if(!empty($readonly)) disabled @endif>
                </div>
            </div>

            <div class="row mb-3 col-form-label col-form-label-top">
                <label for="code" class="form-label-cus  col-sm-2">Mã chặng</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="code" name="code"
                        value="{{ old('code', $stage->code ?? '') }}" required @if(!empty($readonly)) disabled @endif>
                </div>
            </div>

            <div class="row mb-3 col-form-label col-form-label-top">
                <label class="form-label-cus  col-sm-2"> Thuộc mục tiêu</label>
                <div class="col-sm-10">
                    <select name="targets[]" class="form-control" multiple>
                        @foreach($targets as $target)
                        <option value="{{ $target->id }}"
                            {{ isset($stageTargets) && in_array($target->id, $stageTargets) ? 'selected' : '' }}>
                            {{ $target->title }}
                        </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều mục tiêu</small>
                </div>
            </div>

            <div class="row mb-3 col-form-label">
                <label for="des" class="form-label-cus  col-sm-2">Mô tả: </label>
                <div class="col-sm-10">
                    <textarea id="des" name="des" class="tinymce" @if(!empty($readonly)) disabled
                        @endif>{{ old('des', $stage->des ?? '') }}</textarea>
                </div>
            </div>

            <div class="row mb-3 col-form-label">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    @if(empty($readonly))
                    <button type="submit" class="btn btn-primary">{{ $submitButton }}</button>
                    @endif
                    <a href="{{ route('admin.stage.list') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .col-form-label {
        padding: 0px 20px;
    }

    .col-form-label-top {
        padding-top: 10px;
    }

    .form-label-cus {
        align-content: center;
    }

    .btn-cus {
        margin-bottom: 20px;
        margin-left: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
    const image_upload_handler_callback = (blobInfo, progress) => new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/upload-image');

        xhr.upload.onprogress = (e) => {
            progress(e.loaded / e.total * 100);
        };

        xhr.onload = () => {
            if (xhr.status !== 200) {
                reject('HTTP Error: ' + xhr.status);
                return;
            }

            const json = JSON.parse(xhr.responseText);

            if (!json || typeof json.filename !== 'string') {
                reject('Invalid response: ' + xhr.responseText);
                return;
            }

            // Tự nối đường dẫn lại từ filename
            resolve('/storage/uploads/' + json.filename);
        };

        xhr.onerror = () => {
            reject('Image upload failed. Code: ' + xhr.status);
        };

        const formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.content);
        }

        xhr.send(formData);
    });
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
        toolbar: 'undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl',
        height: 300,
        license_key: 'gpl',
        automatic_uploads: true,
        images_upload_url: '/upload-image',
        images_upload_handler: image_upload_handler_callback,
    });
</script>

<script>
    $(document).ready(function() {
        $('#stageForm').submit(function(e) {
            e.preventDefault(); // Ngăn chặn hành vi submit mặc định

            let formData = $(this).serialize(); // Lấy dữ liệu từ form
            let actionUrl = $(this).attr('action'); // Lấy URL từ thuộc tính action của form

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 2000);
                    } else {
                        toastr.error(response.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let messages = [];
                        $.each(errors, function(key, value) {
                            messages.push(value[0]);
                        });
                        toastr.error(messages.join('<br>')); // Hiển thị lỗi xác thực
                    } else {
                        toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
                    }
                }
            });
        });
    });
</script>
@endpush
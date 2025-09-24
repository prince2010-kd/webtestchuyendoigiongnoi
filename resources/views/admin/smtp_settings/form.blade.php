@extends('layouts.user')

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3>{{ $formTitle }}</h3>
    <div class="card p-2 pt-3">
        <form id="smtp_settingsForm" action="{{ $formAction }}" method="POST">
            @csrf
            @if($formMethod === 'PUT')
            @method('PUT')
            @endif

            <div class="row mb-3 col-form-label col-form-label-top">
                <label for="username" class="form-label-cus  col-sm-2">User name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="username" name="username"
                        value="{{ old('username', $smtp_settings->username ?? '') }}" required @if(!empty($readonly)) disabled @endif>
                </div>
            </div>

            <div class="row mb-3 col-form-label">
                <label for="password" class="form-label-cus col-sm-2">Password</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="password" name="password"
                        value="{{ old('password', $smtp_settings->password ?? '') }}" required @if(!empty($readonly)) disabled @endif>
                </div>
            </div>

            <div class="row mb-3 col-form-label">
                <label for="hostname" class="form-label-cus col-sm-2">Hostname</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="hostname" name="hostname"
                        value="{{ old('hostname', $smtp_settings->hostname ?? '') }}" required @if(!empty($readonly)) disabled @endif>

                </div>
            </div>
            <div class="row mb-3 col-form-label">
                <label for="secure" class="form-label-cus col-sm-2">Secure</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="secure" name="secure"
                        value="{{ old('secure', $smtp_settings->secure ?? '') }}" required @if(!empty($readonly)) disabled @endif>
                </div>
            </div>
            <div class="row mb-3 col-form-label">
                <label for="port" class="form-label-cus col-sm-2">Port</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="port" name="port"
                        value="{{ old('port', $smtp_settings->port ?? '') }}" required @if(!empty($readonly)) disabled @endif>
                </div>
            </div>

            <div class="row mb-3 col-form-label">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    @if(empty($readonly))
                    <button type="submit" class="btn btn-primary">{{ $submitButton }}</button>
                    @endif
                    <a href="{{ route('admin.smtp_settings.list') }}" class="btn btn-secondary">Quay lại</a>
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
    $(document).ready(function() {
        const successMsg = "Cập nhật thành công";

        $('#smtp_settingsForm').submit(function(e) {
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
                            // window.location.href = '/admin/list-smtp'; // Điều hướng về danh sách
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

        $('.check-all').change(function() {
            const id = $(this).attr('id').replace("check-all-", "");
            const checkStatus = $(this).prop('checked');
            //console.log("check all: " + `-checkbox-${id}}`)
            $(`input[type="checkbox"][id$="-checkbox-${id}"]`).each(function() {
                //console.log($(this))
                if (checkStatus) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            })
        })
    });
</script>
@endpush
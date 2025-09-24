@extends('layouts.user')

{{-- @section('title', 'Create New User') --}}

@section('content')
    @php
        use App\Enums\FormMode;
        $isSelfEdit = $mode === FormMode::FORM_EDIT_SELF;
    @endphp
    <div class="container-xxl my-4 vh-100">
        <h3>{{ $mode === FormMode::FORM_CREATE ? 'Thêm mới' : 'Cập nhật' }}</h3>
        <div class="card p-4 pt-3">
            <form class="my-4" id="create-form" method="POST"
               action="{{ $mode === FormMode::FORM_CREATE ? route('admin.user.store') : route('admin.user.update', $user->id) }}"

                @csrf

                @if (in_array($mode, [\App\Enums\FormMode::FORM_EDIT, \App\Enums\FormMode::FORM_EDIT_SELF]))
                    @method('PUT')
                @endif


                <div class="custom-form-row mb-3">
                    <label for="username" class="form-label col-sm-2">Tài khoản</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ isset($user) && isset($user->name) ? $user->name : '' }}" />
                    </div>
                </div>
                <div class="custom-form-row mb-3">
                    <label for="fullname" class="col-sm-2">Họ tên</label>
                    <div class="col-sm-10 col-sm-4 mb-2">
                        <input type="text" class="form-control" id="fullname" name="ten_day_du"
                            value="{{ isset($user) && isset($user->ten_day_du) ? $user->ten_day_du : '' }}" />
                    </div>
                </div>

                <div class="custom-form-row mb-3">
                    <label for="email" class="col-sm-2">Email</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="email" name="email"
                            value="{{ $user->email ?? '' }}" {{ $isSelfEdit ? 'readonly' : '' }} />
                    </div>
                </div>

                <div class="custom-form-row mb-3">
                    <label for="permis">Chọn nhóm quyền</label>
                    <select name="permis" id="permis" class="form-select @error('permis') is-invalid @enderror"
                        {{ $isSelfEdit ? 'disabled' : '' }}>
                        <option value="">-- Chọn nhóm quyền --</option>
                        @foreach ($nhomQuyen as $item)
                            <option value="{{ $item->id }}"
                                {{ old('permis', $user->group_id ?? '') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('permis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="custom-form-row mb-3">
                    <label for="password" class="col-sm-2">Mật khẩu mới</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" name="password"
                            autocomplete="new-password" />
                    </div>
                </div>

                <div class="custom-form-row mb-3">
                    <label for="rewritepass" class="col-sm-2">Nhập lại mật khẩu</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="rewritepass" name="rewritepass" />
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit"
                        class="btn btn-primary col-sm-2 text-white">{{ $mode === FormMode::FORM_CREATE ? 'Thêm mới' : 'Cập nhật' }}</button>
                    <a href="{{ route('admin.user.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                    </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
         $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        const FORM_MODE = @json($mode);
        $(document).ready(function() {
            $('#create-form').submit(function(e) {
                e.preventDefault();

                let method = $(this).find('input[name="_method"]').val() || 'POST';
                let isEdit = method === 'PUT';
                let successMsg = isEdit ? "Cập nhật thành công" : "Thêm mới thành công";

                let formData = $(this).serialize();

                let password = $('#password').val().trim();
                let rewritepass = $('#rewritepass').val().trim();
                let errors = [];

                // Danh sách các trường bắt buộc
                let fields = {
                    name: 'Tài khoản',
                    fullname: 'Họ tên',
                    email: 'Email'
                };

                // Nếu là thêm mới thì bắt buộc nhập mật khẩu
                if (!isEdit) {
                    fields.password = 'Mật khẩu mới';
                }

                // Nếu người dùng có nhập password thì bắt buộc phải nhập lại và khớp
                if (password || rewritepass) {
                    if (!password || !rewritepass) {
                        errors.push("Cần nhập đầy đủ cả mật khẩu mới và nhập lại mật khẩu");
                    } else if (password !== rewritepass) {
                        errors.push("Mật khẩu mới và Nhập lại mật khẩu không khớp");
                    }
                }

                // Kiểm tra các trường bắt buộc
                $.each(fields, function(fieldId, fieldLabel) {
                    if (!$(`#${fieldId}`).val().trim()) {
                        errors.push(`${fieldLabel} không được để trống`);
                    }
                });

                // Nếu có lỗi thì hiển thị và dừng submit
                if (errors.length > 0) {
                    errors.forEach(error => {
                        toastr.error(error);
                    });
                    return;
                }
                let redirectUrl = FORM_MODE === 'edit_self' ? '/admin/dashboard' : '/admin/list-user';
                // Gửi AJAX request
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        toastr.success(successMsg);
                        setTimeout(function() {
                            window.location.href = redirectUrl;
                        }, 2000);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let messages = [];
                            $.each(errors, function(key, value) {
                                messages.push(value[0]);
                            });
                            toastr.error(messages.join('<br>'));
                        } else {
                            toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
                        }
                    }
                });
            });
        });
    </script>
@endpush

@extends('layouts.user')
@php use App\Enums\FormMode; @endphp
@push('styles')
<style>
li {
  list-style-type: disc;
}
</style>
@endpush

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3 class="mb-4">    
        {{ $mode === FormMode::FORM_CREATE ? 'Thêm mới' : 'Cập nhật' }}
    </h3>
    <div id="mode-container" data-form-mode="{{ $mode }}"></div>
    <div class="card-body bg-white rounded p-4">
        <form id="form-add-menu" 
        method="POST"
        action="{{ $mode === FormMode::FORM_CREATE ? route('nhomquyen.store') : route('nhomquyen.update', $nhomQuyen->id) }}"
        >
            @csrf
            
            @if ($mode === \App\Enums\FormMode::FORM_EDIT)
                @method('PUT')
            @endif
            <div class="mb-3 row">
                <label for="name" class="col-sm-2 form-label">Tiêu đề</label>
                <div class="col-sm-10 ">
                    <input 
                    type="text" 
                    class="form-control" 
                    id="name" 
                    name="name"
                    value="{{ isset($nhomQuyen) ? $nhomQuyen->name : '' }}" 
                    required>
                </div>
            </div>
            <div class="mb-3">
                <div class="row">
                    <label class="col-sm-2 py-2 form-label">Phân quyền cho menu</label>
                    <div class="col-sm-10">
                        @include('menu-auth', [
                            'menus' => $menus,
                            'selected' => $menu->parent_id ?? null,
                            'depth' => 0,
                            'permissionTypes' => ['can_view', 'can_add', 'can_edit', 'can_delete', 'can_export']
                        ])
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $mode === FormMode::FORM_CREATE ? 'Thêm mới' : 'Cập nhật' }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const FORM_CREATE = @json(\App\Enums\FormMode::FORM_CREATE);
    const FORM_EDIT = @json(\App\Enums\FormMode::FORM_EDIT);
    const formMode = $("#mode-container").data('form-mode');
    const successMsg = formMode == FORM_CREATE ? "Tạo mới thành công" : "Cập nhật thành công";

    $('#form-add-menu').submit(function(e) {
        e.preventDefault();
        let formData = $(this).serialize();        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            success: function(response) {
                toastr.success(successMsg);
                setTimeout(function() {
                    window.location.href = '/admin/nhomquyen';
                }, 2000);
            },
            error: function(xhr) {
                if(xhr.status === 422) {
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

    $('.check-all').change(function() {
        const id = $(this).attr('id').replace("check-all-", "");
        const checkStatus = $(this).prop('checked');
        $(`input[type="checkbox"][id$="-checkbox-${id}"]`).each(function(){
            if(checkStatus)
            {
                $(this).prop('checked', true);
            }else
            {
                $(this).prop('checked', false   );
            }
        })
    })
});
</script>
@endpush

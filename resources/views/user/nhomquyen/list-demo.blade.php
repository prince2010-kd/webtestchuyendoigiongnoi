@extends('layouts.user')

@push('styles')
<style>
    ul, li{
        list-style-type: none;
    }
</style>
@endpush
@section('content')
<div class="container-xxl my-4 vh-100">
    <h1>DEMO</h1>

    <div class="card p-2 pt-3">
        <div 
            class="d-flex justify-content-between"
            style="{{ !kiemTraQuyen('admin/nhomquyen', 'can_add') && !kiemTraQuyen('admin/nhomquyen', 'can_delete') ? 'display: none !important;' : '' }}"
            >
            <button 
            id="btn-add" 
            data-url="{{ route('nhomquyen.create') }}" 
            class="btn btn-primary mb-3" 
            style="{{ !kiemTraQuyen('admin/nhomquyen', 'can_add') ? 'visibility: hidden;' : '' }}"
            >Thêm mới</button>

            <button 
                class="btn btn-danger" 
                style="
                    height: fit-content;
                    {{ !kiemTraQuyen('admin/nhomquyen', 'can_delete') ? 'visibility: hidden;' : '' }}
                " 
                id="delete-selected"
                >Xóa lựa chọn</button>
        </div>
        <div id="table-container">
            <x-admin.data-table 
            :items="$nhomQuyen" 
            route="admin/nhomquyen"
            :column="$column"
            />
        </div>
        <div class="mt-4 pagination" id="pagination-post">
            @include('admin.component.pagination', ['posts' => $nhomQuyen])
        </div>  
    </div>
</div>

@endsection

@push('scripts')
<!-- <script>
$(document).ready(function () {
    const debounceTimeouts = {};
    $('.status-switch').change(function(){
        const switchBtn = $(this);
        const id = $(this).data('id');
        const isActive = $(this).is(':checked') ? 1 : 0;
        
        if (debounceTimeouts[id]) {
            clearTimeout(debounceTimeouts[id]);
        }

        debounceTimeouts[id] = setTimeout(() => {
            switchBtn.prop('disabled', true)
            $.ajax({
                url: `/admin/nhomquyen/update/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{csrf_token()}}',
                    active: isActive
                },

                success: function(res)
                {
                    console.log(res)
                },
                error: function (xhr) {
                    if (xhr.status === 403) {
                            toastr.warning(xhr.responseJSON.message || 'Bạn không có quyền truy cập.');
                    } else {
                        toastr.error('Có lỗi xảy ra.');
                    }
                    switchBtn.prop('checked', !isActive);
                },
                complete: function(){
                    switchBtn.prop('disabled', false)
                }
            })
        }, 500);
    })

    $('.btn-delete').click(function() {
        let id = $(this).data('id');
        console.log('ID cần xóa:', id);
        if (!id) {
            toastr.error('Không tìm thấy ID để xóa.');
            return;
        }

        if (!confirm('Bạn có chắc chắn muốn xóa?')) return;

        $.ajax({
            url: '/admin/nhomquyen/' + id,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                _method: 'DELETE'
            },
            success: function (response) {
                toastr.success('Xóa menu thành công!');
                $('button[data-id="' + id + '"]').closest('tr').remove();
                location.reload();
            },
            error: function (xhr) {
                if (xhr.status === 403) {
                    toastr.warning(xhr.responseJSON.message || 'Bạn không có quyền truy cập.');
                } else {
                    toastr.error('Có lỗi xảy ra.');
                }
            }
        });
    })

    $('#check-all').change(function() {
        const id = $(this).attr('id').replace("check-all-", "");
        const checkStatus = $(this).prop('checked');
        $(`input[type="checkbox"][id^="option-no"]`).each(function(){
            if(checkStatus)
            {
                $(this).prop('checked', true);
            }else
            {
                $(this).prop('checked', false   );
            }
        })
    })

    $('#delete-selected').click(function(){
        let toastShown = false; // flag to track if toast is shown already

        const checkedBoxes = $(`input[type="checkbox"][id^="option-no"]:checked`);
        if (checkedBoxes.length === 0) {
            toastr.warning('Vui lòng chọn ít nhất một nhóm quyền để xóa.');
            return;
        }
        $(checkedBoxes).each(function(){
            const id = $(this).data('id');
            $.ajax({
                url: '/admin/nhomquyen/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function (response) {
                    toastr.success('Xóa nhóm quyền thành công!');
                    //$('button[data-id="' + id + '"]').closest('tr').remove();
                    location.reload();
                },
                error: function (xhr) {
                    if (!toastShown) {  // Show only once
                        toastShown = true;
                        if (xhr.status === 403) {
                            toastr.warning(xhr.responseJSON.message || 'Bạn không có quyền truy cập.');
                        } else {
                            toastr.error('Có lỗi xảy ra.');
                        }
                    }
                }
            });
        })
    })

    $('#btn-add').click(function(){
        const url = $(this).data('url');
        $.ajax({
            url: url,
            method: 'GET',
            success: function() {
                window.location.href = url;
            },
            error: function(xhr) {
                if (xhr.status === 403) {
                    toastr.warning(xhr.responseJSON.message || 'Bạn không có quyền truy cập.');
                } else {
                    toastr.error('Có lỗi xảy ra.');
                }
            }
        });
    })

    $('#btn-edit').click(function(){
        const url = $(this).data('url');
        $.ajax({
            url: url,
            method: 'GET',
            success: function() {
                window.location.href = url;
            },
            error: function(xhr) {
                if (xhr.status === 403) {
                    toastr.warning(xhr.responseJSON.message || 'Bạn không có quyền truy cập.');
                } else {
                    toastr.error('Có lỗi xảy ra.');
                }
            }
        });
    })
})
</script> -->
@endpush


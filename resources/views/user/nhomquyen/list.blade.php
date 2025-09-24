@extends('layouts.user')

@push('styles')
<style>
    ul, li {
        list-style-type: none;
    }
</style>
@endpush

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3>Danh sách nhóm quyền</h3>

    <div class="card p-2 pt-3">

        @canany(['create', 'bulkDelete'], App\Models\NhomQuyen::class)
        <div class="d-flex justify-content-between">
            @can('create', App\Models\NhomQuyen::class)
                <button 
                    id="btn-add" 
                    data-url="{{ route('nhomquyen.create') }}" 
                    class="btn btn-primary mb-3"
                >Thêm mới</button>
            @endcan

            {{-- @can('bulkDelete', App\Models\NhomQuyen::class)
                <button class="btn btn-danger" id="delete-selected">Xóa lựa chọn</button>
            @endcan --}}
        </div>
        @endcanany

        @php
            $canUpdateAny = $nhomQuyen->first(fn($item) => auth()->user()->can('update', $item));
        @endphp

        <table class="table">
            <thead>
                <tr>
                    @can('bulkDelete', App\Models\NhomQuyen::class)
                        <th class="th-admin-fit">
                            <input type="checkbox" id="check-all" class="form-check-input p-2"/>
                        </th>
                    @endcan

                    <th class="th-admin-fit">STT</th>
                    <th>TIÊU ĐỀ</th>
                    <th class="th-admin-fit text-center">Thao tác</th>

                    @if ($canUpdateAny)
                        <th class="text-center">Trạng thái</th>
                    @endif
                </tr>
            </thead>

            <tbody>
                @foreach ($nhomQuyen as $item)  
                <tr>
                    @can('delete', $item)
                    <td>
                        <input 
                            type="checkbox"  
                            value="{{ $item->id }}" 
                            class="form-check-input"
                            data-id="{{ $item->id }}"
                        />
                    </td>
                    @endcan

                    <td class="td-admin-fit">{{ $loop->iteration }}</td>

                    <td>
                        @can('update', $item)
                            <a href="{{ route('nhomquyen.edit', $item->id) }}">
                                {{ $item->name }}
                            </a>
                        @else
                            <div>{{ $item->name }}</div>
                        @endcan
                    </td>

                    <td>
                        <div class="action-btn-container">
                            @can('update', $item)
                                <button 
                                    id="btn-edit" 
                                    data-url="{{ route('nhomquyen.edit', $item->id) }}" 
                                    class="action-btn btn btn-sm btn-info" 
                                    title="Chỉnh sửa"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                            @endcan
                            
                            @can('delete', $item)
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-danger btn-delete action-btn" 
                                    data-id="{{ $item->id }}" 
                                    title="Xóa"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </td>

                    @if (auth()->user()->can('update', $item))
                    <td>
                        <div class="form-check form-switch d-flex justify-content-center">
                            <input class="form-check-input status-switch" 
                                type="checkbox" 
                                role="switch" 
                                data-id="{{ $item->id }}"
                                @if ($item->active == 1) checked @endif
                            />
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>  

        <div class="mt-4 pagination" id="pagination-post">
            @include('admin.component.pagination', ['posts' => $nhomQuyen])
        </div>  
    </div>
</div>
@endsection


@push('scripts')
<script>
$(document).ready(function () {
    const debounceTimeouts = {};
    $('.status-switch').change(function(e){
        e.preventDefault();
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
                success: function(res) {
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
                complete: function() {
                    switchBtn.prop('disabled', false)
                }
            })
        }, 500);
    })

    $('.btn-delete').click(function() {
        let id = $(this).data('id');
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
            success: function () {
                toastr.success('Xóa nhóm quyền thành công!');
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
        const checkStatus = $(this).prop('checked');
        $('input[type="checkbox"][data-id]').prop('checked', checkStatus);
    })

    $('#delete-selected').click(function(){
        let toastShown = false;
        const checkedBoxes = $('input[type="checkbox"][data-id]:checked');
        if (checkedBoxes.length === 0) {
            toastr.warning('Vui lòng chọn ít nhất một nhóm quyền để xóa.');
            return;
        }

        checkedBoxes.each(function(){
            const id = $(this).data('id');
            $.ajax({
                url: '/admin/nhomquyen/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function () {
                    toastr.success('Xóa nhóm quyền thành công!');
                    location.reload();
                },
                error: function (xhr) {
                    if (!toastShown) {
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
</script>
@endpush

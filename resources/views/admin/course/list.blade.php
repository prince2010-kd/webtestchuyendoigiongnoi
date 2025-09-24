@extends('layouts.user')

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3>Danh sách danh mục website</h3>
    <div class="card p-2 pt-3">
        <div class="row mb-4 col-form-label col-form-label-top">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.course.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
                <button class="btn btn-danger" style="height: fit-content;" id="delete-selected">Xóa lựa chọn</button>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 7%;">
                        <div class="form-check">
                            <input type="checkbox" id="check-all" class="p-2 form-check-input" />
                        </div>
                    </th>
                    <th>STT</th>
                    <th>Tên</th>
                    <th>Content</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($course as $cat)
                <tr>
                    <td>
                        <div class="form-check ">
                            <input
                                type="checkbox"
                                name="cat_ids[]"
                                value="{{ $cat->id }}"
                                id="{{ 'option-no' . $cat->id }}"
                                class="form-check-input"
                                data-id="{{ $cat->id }}" />
                        </div>
                    </td>
                    <td>{{ $cat->stt }}</td>
                    <td>{{ $cat->title }}</td>
                    <td style="vertical-align: middle;">{!! $cat->content !!}</td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input status-switch"
                                type="checkbox"
                                role="switch"
                                data-id="{{ $cat->id }}"
                                @if ($cat->active == 1) checked @endif
                            />
                        </div>
                    </td>
                    <td>
                        <!-- <a href="{{ route('admin.course.show', $cat->id) }}" class="btn btn-sm btn-primary" title="Xem chi tiết">
                            <i class="fa fa-eye"></i>
                        </a> -->
                        <a href="{{ route('admin.course.edit', $cat->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                            <i class="fa fa-edit"></i>
                        </a>
                        <!-- <form action="{{ route('admin.course.destroy', $cat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger btn-delete" type="submit"> <i class="fa fa-trash"></i></button>
                        </form> -->
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $cat->id }}" title="Xóa">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 pagination" id="pagination-post">
            @include('admin.component.pagination', ['posts' => $course])
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.btn-delete').click(function() {
            let id = $(this).data('id');
            console.log('ID cần xóa:', id);
            if (!id) {
                toastr.error('Không tìm thấy ID để xóa.');
                return;
            }

            if (!confirm('Bạn có chắc chắn muốn xóa?')) return;

            $.ajax({
                url: '/admin/list-course/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    toastr.success('Xóa thành công!');
                    location.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Đã xảy ra lỗi khi xóa!');
                }
            });
        });

        // cập nhật trạng thái
        const debounceTimeouts = {};
        $('.status-switch').change(function() {
            const switchBtn = $(this);
            const id = $(this).data('id');
            const isActive = $(this).is(':checked') ? 1 : 0;

            if (debounceTimeouts[id]) {
                clearTimeout(debounceTimeouts[id]);
            }

            debounceTimeouts[id] = setTimeout(() => {
                switchBtn.prop('disabled', true)
                $.ajax({
                    url: `/admin/list-course/updatestatus/${id}`,
                    method: 'PUT',
                    data: {
                        _token: '{{csrf_token()}}',
                        active: isActive
                    },

                    success: function(res) {
                        toastr.success(res.message);
                        console.log(res.message);
                        // Redirect thủ công:
                        window.location.href = res.redirect;
                    },
                    error: function() {
                        console.log('Có lỗi xảy ra khi cập nhật trạng thái.');
                        switchBtn.prop('checked', !isActive);
                    },
                    complete: function() {
                        switchBtn.prop('disabled', false)
                    }
                })
            }, 500);
        })
    });

    $('#check-all').change(function() {
        const id = $(this).attr('id').replace("check-all-", "");
        const checkStatus = $(this).prop('checked');
        $(`input[type="checkbox"][id^="option-no"]`).each(function() {
            if (checkStatus) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        })
    });

    $('#delete-selected').click(function() {
        if (!confirm('Bạn có chắc chắn muốn xóa các bản ghi này?')) return;
        $(`input[type="checkbox"][id^="option-no"]:checked`).each(function() {
            const id = $(this).data('id');
            $.ajax({
                url: '/admin/list-course/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                },
                success: function(response) {
                    toastr.success('Xóa thành công!');
                    location.reload();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Đã xảy ra lỗi khi xóa!');
                }
            });
        })
    });
</script>
@endpush

@push('styles')
<style>
    table.table-bordered th,
    table.table-bordered td {
        border-left: none !important;
        border-right: none !important;
    }

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
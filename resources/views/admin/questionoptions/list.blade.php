@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>Danh sách loại câu hỏi</h3>
        <div class="card p-2 pt-3">
            <div class="row mb-4">
                <div class="col-md-2 
                                    ">
                    <a href="{{ route('admin.questionoption.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
                </div>
            </div>
            <div class="col-form-label" id="filterForm">
                @include('admin.component.filter-form')
            </div>
            <div class="ajax-pagination-container" data-table="#post-data" data-pagination="#pagination-post">
                <div id="table-container" style="overflow-x: auto; width: 100%;">
                    @include('admin.questionoptions.partials._table')
                </div>

                <div class="mt-4 pagination" id="pagination-post">
                    @include('admin.component.pagination', ['posts' => $questionOptions])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // Xóa
            $('.btn-delete').click(function () {
                let id = $(this).data('id');
                if (!id) {
                    toastr.error('Không tìm thấy ID để xóa.');
                    return;
                }
                if (!confirm('Bạn có chắc chắn muốn xóa bài viết?')) return;

                $.ajax({
                    url: '/admin/list-question-option/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function () {
                        toastr.success('Xóa bài viết thành công!');
                        location.reload();
                    },
                    error: function () {
                        toastr.error('Đã xảy ra lỗi khi xóa bài viết!');
                    }
                });
            });

            // Thay đổi số thứ tự
            $(document).on('change', '.stt-input', function () {
                let categoryId = $(this).data('id');
                let newStt = parseInt($(this).val());

                if (isNaN(newStt) || newStt < 1) {
                    toastr.error('STT phải là số nguyên lớn hơn hoặc bằng 1');
                    return;
                }
                $.ajax({
                    url: '/admin/list-question-option/' + categoryId + '/update-stt',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stt: newStt,
                    },
                    success: function (response) {
                        toastr.success(response.message);
                        location.reload();
                    },
                    error: function () {
                        toastr.error('Cập nhật STT thất bại');
                    }
                });
            });

            // Tìm kiếm
            $('.btn-search').on('click', function (e) {
                e.preventDefault();

                const data = $('#filterForm').find('select, input').serialize() + '&action=search';

                $.ajax({
                    url: '/admin/list-question-option',
                    method: 'GET',
                    data: data,
                    success: function (res) {
                        $('#table-container').html(res.table);
                        $('#pagination-post').html(res.pagination);

                        // Chỉ thông báo nếu là thao tác tìm kiếm
                        if (res.isSearch) {
                            toastr.success('Tìm kiếm thành công!');
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Đã xảy ra lỗi khi tìm kiếm.');
                        console.error(xhr.responseText);
                    }
                });
            });

            // Thêm sự kiện cho select page size
            $('.page-size-show').on('change', function () {

                $('.btn-search').click();
            });

            // Thêm sự kiện cho select sort
            $('select[name="sortBy"]').on('change', function () {

                $('.btn-search').click();
            });

            // Thêm sự kiện cho input keyword với debounce
            let searchTimeout;
            $('input[name="keyword"]').on('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    $('.btn-search').click();
                }, 500);
            });

            // Thay đổi trạng thái
            $(document).on('change', '.active-toggle', function () {
                let categoryId = $(this).data('id');
                let active = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/admin/list-question-option/' + categoryId + '/toggle-active',
                    type: 'put',
                    data: {
                        _token: '{{ csrf_token() }}',
                        active: active
                    },
                    success: function (response) {
                        toastr.success(response.message || 'Đã cập nhật trạng thái!');
                        location.reload();
                    },
                    error: function () {
                        toastr.error('Cập nhật trạng thái thất bại!');
                    }
                });
            });
        });

        $('#delete-selected').click(function (e) {
            e.preventDefault();

            let ids = [];
            $('.check-item:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length === 0) {
                toastr.warning('Vui lòng chọn ít nhất một bài viết để xóa.');
                return;
            }

            if (!confirm('Bạn có chắc chắn muốn xóa bài viết đã chọn?')) return;

            let url = '';
            let data = {
                _token: '{{ csrf_token() }}',
                'ids': ids
            };

            let method = 'DELETE';

            if (ids.length === 1) {
                url = '/admin/list-question-optiono/' + ids[0];
            } else {
                url = '{{ route("admin.questionoption.deleteMultiple") }}';
            }

            $.ajax({
                url: url,
                type: method,
                data: data,
                // Không dùng traditional: true nếu muốn gửi mảng
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    toastr.success(response.message || 'Đã xóa thành công');
                    window.location.href = window.location.pathname;
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Xóa thất bại');
                    console.error('Chi tiết lỗi:', xhr.responseJSON || xhr.responseText);
                }
            });
        });

        // Chức năng chọn tất cả
        $('#check-all').change(function () {
            $('.check-item').prop('checked', $(this).is(':checked'));
        });

        $(document).on('change', '.is-featured-toggle', function () {
            const postId = $(this).data('id');
            const isFeatured = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: `/admin/list-question-option/${postId}/toggle-is_featured`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_featured: isFeatured
                },
                success: function (response) {
                    if (isFeatured) {
                        // ✅ Tắt các checkbox khác (chỉ giữ 1 cái được bật)
                        $('.is-featured-toggle').not(`[data-id="${postId}"]`).prop('checked', false);
                    }

                    toastr.success(response.message || 'Cập nhật nổi bật thành công!');
                },
                error: function () {
                    toastr.error('Cập nhật nổi bật thất bại!');
                }
            });
        });

    </script>
@endpush
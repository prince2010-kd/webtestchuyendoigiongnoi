@extends('layouts.user')

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3>Danh sách menu</h3>
    <div class="card p-2 pt-3">
        <div class="row mb-4">
            <div class="col-md-2">
                <a href="{{ route('admin.menufrontend.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
            </div>
        </div>

        @include('admin.component.filter-form')

        <div class="ajax-pagination-container" data-table="#post-data" data-pagination="#pagination-post">
             <div id="table-container" style="overflow-x: auto; width: 100%;">
                    @include('admin.menufrontend.partials._table')
                </div>

             <div class="mt-4 pagination" id="pagination-post">
                @include('admin.component.pagination', ['posts' => $menuTree1])
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Xoá 1 bản ghi
            $('.btn-delete').click(function () {
                let id = $(this).data('id');
                if (!confirm('Bạn có chắc chắn muốn xóa?')) return;

                $.ajax({
                    url: '/admin/list-menu-frontend/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function () {
                        toastr.success('Xóa giáo viên thành công!');
                        location.reload();
                    },
                    error: function () {
                        toastr.error('Đã xảy ra lỗi khi xóa!');
                    }
                });
            });

            // Cập nhật STT
            $(document).on('change', '.stt-input', function () {
                let id = $(this).data('id');
                let stt = parseInt($(this).val());

                if (isNaN(stt) || stt < 1) {
                    toastr.error('STT phải là số hợp lệ');
                    return;
                }

                $.ajax({
                    url: '/admin/list-menu-frontend/' + id + '/update-stt',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stt: stt,
                    },
                    success: function (res) {
                        toastr.success(res.message);
                        location.reload();
                    },
                    error: function () {
                        toastr.error('Cập nhật STT thất bại');
                    }
                });
            });

            $('.btn-search').on('click', function (e) {
                e.preventDefault();

                const data = $('#filterForm').find('select, input').serialize() + '&action=search';

                $.ajax({
                    url: '/admin/list-menu-frontend',
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

            // Toggle trạng thái
            $(document).on('change', '.active-toggle', function () {
                let id = $(this).data('id');
                let active = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/admin/list-menu-frontend/' + id + '/toggle-active',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        active: active,
                    },
                    success: function (res) {
                        toastr.success(res.message || 'Đã cập nhật trạng thái!');
                        location.reload();
                    },
                    error: function () {
                        toastr.error('Cập nhật trạng thái thất bại!');
                    }
                });
            });

            // Xoá hàng loạt
            $('#delete-selected').click(function (e) {
                e.preventDefault();

                let ids = [];
                $('.check-item:checked').each(function () {
                    ids.push($(this).val());
                });

                if (ids.length === 0) {
                    toastr.warning('Vui lòng chọn ít nhất một giáo viên để xóa.');
                    return;
                }

                if (!confirm('Bạn có chắc chắn muốn xóa giáo viên đã chọn?')) return;

                let url = '';
                let data = {
                    _token: '{{ csrf_token() }}',
                    'ids': ids
                };

                let method = 'DELETE';

                if (ids.length === 1) {
                    url = '/admin/list-menu-frontend/' + ids[0];
                } else {
                    url = '{{ route("admin.menufrontend.deleteMultiple") }}';
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

            // Chọn tất cả
            $('#check-all').change(function () {
                $('.check-item').prop('checked', $(this).is(':checked'));
            });
        });
    </script>
@endpush

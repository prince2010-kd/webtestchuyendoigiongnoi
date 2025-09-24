@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>Danh sách sản phẩm</h3>
        <div class="card p-2 pt-3">
            <div class="row mb-4">
                <div class="col-md-2">
                    <a href="{{ route('admin.sanpham.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
                </div>
            </div>

            <div class="col-form-label" id="filterForm">
                @include('admin.component.filter-form')
            </div>

            <div class="ajax-pagination-container" data-table="#product-data" data-pagination="#pagination-product">
                <div id="table-container" style="overflow-x: auto; width: 100%;">
                    @include('admin.sanpham.partials._table')
                </div>

                <div class="mt-4 pagination" id="pagination-product">
                    @include('admin.component.pagination', ['posts' => $products])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.btn-delete').click(function () {
                let id = $(this).data('id');
                if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm?')) return;

                $.ajax({
                    url: '/admin/sanpham/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function () {
                        toastr.success('Xóa sản phẩm thành công!');
                        location.reload();
                    },
                    error: function () {
                        toastr.error('Đã xảy ra lỗi khi xóa sản phẩm!');
                    }
                });
            });

            $(document).on('change', '.stt-input', function () {
                let productId = $(this).data('id');
                let newStt = parseInt($(this).val());

                if (isNaN(newStt) || newStt < 0) {
                    toastr.error('STT phải là số nguyên lớn hơn hoặc bằng 0');
                    return;
                }

                $.ajax({
                    url: '/admin/sanpham/' + productId + '/update-stt',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        stt: newStt
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

            $('.btn-search').on('click', function (e) {
                e.preventDefault();
                const data = $('#filterForm').find('select, input').serialize();
                $.ajax({
                    url: '/admin/sanpham',
                    method: 'GET',
                    data: data,
                    success: function (res) {
                        $('#table-container').html(res.table);
                        $('#pagination-product').html(res.pagination);
                        toastr.success('Tìm kiếm thành công!');
                    },
                    error: function () {
                        toastr.error('Đã xảy ra lỗi khi tìm kiếm.');
                    }
                });
            });

            $('.page-size-show, select[name="sortBy"], input[name="keyword"]').on('change input', function () {
                $('.btn-search').click();
            });

            $(document).on('change', '.active-toggle', function () {
                let productId = $(this).data('id');
                let active = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: '/admin/sanpham/' + productId + '/toggle-trangthai',
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        trangthai: active
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

            $('#check-all').change(function () {
                $('.check-item').prop('checked', $(this).is(':checked'));
            });
        });
    </script>
@endpush
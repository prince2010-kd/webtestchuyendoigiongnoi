@extends('layouts.user')
@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>Danh sách bài viết sự kiện</h3>
        <div class="card p-2 pt-3">
            <div class="row mb-4">
            </div>

            <form id="filterForm">
                <div class="row mb-4">
                    <div class="col-md-2">
                        <select class="form-select page-size-show" name="page_size" aria-label="Default select example">
                            <option hidden="">Số bản ghi hiển thị</option>
                            <option value="10" selected="" {{ request('page_size') == 10 ? 'selected' : '' }}>10 dòng</option>
                            <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50 dòng</option>
                            <option value="100" {{ request('page_size') == 100 ? 'selected' : '' }}>100 dòng</option>
                            <option value="500" {{ request('page_size') == 500 ? 'selected' : '' }}>500 dòng</option>
                            <option value="1000" {{ request('page_size') == 1000 ? 'selected' : '' }}>1,000 dòng</option>
                            <option value="10000" {{ request('page_size') == 10000 ? 'selected' : '' }}>10,000 dòng</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}"
                            id="defaultFormControlInput" placeholder="Nhập tiêu đề"
                            aria-describedby="defaultFormControlHelp">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary float-start btn-search">Tìm kiếm</button>
                    </div>

                </div>
            </form>

            <div class="ajax-pagination-container" data-table="#post-data" data-pagination="#pagination-post">
                <div id="table-container" style="overflow-x: auto; width: 100%;">
                    @include('admin.qlybluansukien.partials._table')
                </div>

                <div class="mt-4 pagination" id="pagination-post">
                    @include('admin.component.pagination', ['posts' => $suKien])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Tìm kiếm
            $('.btn-search').on('click', function (e) {
                e.preventDefault();

                const data = $('#filterForm').find('select, input').serialize() + '&action=search';

                $.ajax({
                    url: '/admin/list-comment-event',
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
        });
    </script>
@endpush
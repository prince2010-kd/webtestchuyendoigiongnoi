@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h4>Bình luận cho bài: <strong>{{ $blog->title }}</strong></h4>
        <div class="card p-2 pt-3">

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
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold">Từ ngày</span>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}"
                                style="max-width: 160px;">

                            <span class="mx-2">-</span>

                            <span class="fw-semibold">Đến ngày</span>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}"
                                style="max-width: 160px;">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary float-start btn-search" data-id="{{ $blog->id }}">Tìm
                            kiếm</button>
                    </div>

                </div>
            </form>

            <p class="text-muted mb-3">
                Tổng số bình luận: <strong>{{ $comments->total() }}</strong>
            </p>
            <div class="ajax-pagination-container" data-table="#post-data" data-pagination="#pagination-post">
                <div id="table-container">
                    @include('admin.qlybluanblog.partials._tablecomments')
                </div>

                <div class="mt-4 pagination" id="pagination-post">
                    @include('admin.component.pagination', ['posts' => $comments])
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

                let postId = $(this).data('id'); // lấy từ attribute
                const data = $('#filterForm').serialize();

                $.ajax({
                    url: `/admin/list-comment-blog/${postId}/comments`,
                    method: 'GET',
                    data: data,
                    success: function (res) {
                        $('#table-container').html(res.table);
                        $('#pagination-post').html(res.pagination);

                        if (res.totalRecords !== undefined) {
                            $('.total-records').text('Tổng: ' + res.totalRecords + ' bản ghi');
                        }

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

            $('input[name="from_date"], input[name="to_date"]').on('change', function () {
                $('.btn-search').click();
            });

        });

    </script>
@endpush
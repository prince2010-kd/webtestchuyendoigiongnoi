@extends('layouts.user')

@section('content')
<div class="container-xxl my-4 vh-100">
    <h3>Danh sách khóa học</h3>
    <div class="card p-2 pt-3">
        <div class="row mb-4">
            <div class="col-md-2">
                <a href="{{ route('admin.khoahoc.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
            </div>
        </div>

        @include('admin.component.filter-form')

        <div class="ajax-pagination-container" data-table="#giaovien-data" data-pagination="#pagination-giaovien">
            <table class="table table-bordered" id="giaovien-data">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
                        <th style="width: 130px;">Tiêu đề</th>
                        <th style="width: 170px;">Mô tả</th>
                        <th style="width: 70px;">Trạng thái</th>
                        <th style="width: 110px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($khoahoc as $kh)
                    <tr>
                        <td style="text-align:center;">
                            <input type="checkbox" class="check-item" value="{{ $kh->id }}">
                        </td>
                        <td>{{ $kh->name }}</td>
                        <td style="text-align:center;">
                            <div class="form-check form-switch">
                                <input class="form-check-input active-toggle" type="checkbox"
                                    data-id="{{ $kh->id }}" {{ $kh->active ? 'checked' : '' }}>
                            </div>
                        </td>

                        <td style="text-align:center;">
                            <a href="{{ route('admin.khoahoc.edit', $kh->id) }}"
                                class="btn btn-sm btn-info" title="Chỉnh sửa">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $kh->id }}"
                                title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 pagination" id="pagination-giaovien">
                @include('admin.component.pagination', ['posts' => $khoahoc])
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
                url: '/admin/giaovien/' + id,
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
                url: '/admin/giaovien/' + id + '/update-stt',
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

        // Toggle trạng thái
        $(document).on('change', '.active-toggle', function () {
            let id = $(this).data('id');
            let active = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: '/admin/giaovien/' + id + '/toggle-active',
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
        $('.showActionDelete').click(function () {
            let ids = $('.check-item:checked').map(function () {
                return $(this).val();
            }).get();

            if (ids.length === 0) {
                toastr.warning('Vui lòng chọn ít nhất một giáo viên để xóa.');
                return;
            }

            if (!confirm('Bạn có chắc chắn muốn xóa các giáo viên đã chọn?')) return;

            $.ajax({
                url: '{{ route("admin.giaovien.deleteMultiple") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ids: ids,
                    _method: 'DELETE'
                },
                traditional: true,
                success: function (res) {
                    toastr.success(res.message);
                    location.reload();
                },
                error: function () {
                    toastr.error('Xóa thất bại');
                }
            });
        });

        // Chọn tất cả
        $('#check-all').change(function () {
            $('.check-item').prop('checked', $(this).is(':checked'));
        });

        // Lọc - tìm kiếm
        $('#filterForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.giaovien.list') }}",
                type: "GET",
                data: $(this).serialize(),
                success: function (res) {
                    $('#giaovien-data tbody').html($(res.table).find('tbody').html());
                    $('#pagination-giaovien').html(res.pagination);
                },
                error: function () {
                    alert("Đã có lỗi xảy ra khi tìm kiếm");
                }
            });
        });

        $('select[name="sortBy"], select[name="page_size"]').on('change', function () {
            $('#filterForm').trigger('submit');
        });
    });
</script>
@endpush

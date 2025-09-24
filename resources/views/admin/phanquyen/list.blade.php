@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>Danh sách menu</h3>
        <div class="card p-2 pt-3">
            <div class="row mb-4">
                <div class="col-md-2">
                    <a href="{{ route('admin.phanquyen.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>TIÊU ĐỀ</th>
                        <th>THAO TÁC</th>
                        <th>Trạng thái</th>
                        <th>STT</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Hàm đệ quy để render menu đa cấp
                        function renderMenuRows($menus, $level = 0)
                        {
                            foreach ($menus as $menu) {
                                $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                echo '<tr>';
                                echo '<td>' . $indent . ' <i class="' . htmlspecialchars($menu->icon) . '"></i> ' . htmlspecialchars($menu->title) . '</td>';
                                echo '<td>
                                            <a href="' . route('admin.phanquyen.edit', $menu->id) . '" class="btn btn-sm btn-info" title="Chỉnh sửa">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $menu->id . '" title="Xóa">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>';
                                echo '<td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input active-toggle" type="checkbox" 
                                                data-id="' . $menu->id . '" ' . ($menu->active ? 'checked' : '') . '>
                                            </div>
                                        </td>';
                                echo '<td>
                                        <input type="number" class="form-control form-control-sm stt-input" 
                                            data-id="' . $menu->id . '" 
                                            value="' . $menu->stt . '" 
                                            style="width: 60px;" />
                                    </td>';
                                echo '</tr>';

                                if ($menu->childrenAll && $menu->childrenAll->isNotEmpty()) {
                                    renderMenuRows($menu->childrenAll, $level + 1);
                                }
                            }
                        }

                        // Gọi hàm đệ quy để hiển thị menu bắt đầu từ cấp cha
                        renderMenuRows($menus);
                    @endphp
                </tbody>
            </table>
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
                    url: '/admin/list-menu/' + id,
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
                    url: '/admin/list-menu/' + categoryId + '/update-stt',
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
                    url: '/admin/list-menu',
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
                    url: '/admin/list-menu' + categoryId + '/toggle-active',
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
        
        // Chức năng chọn tất cả
        $('#check-all').change(function () {
            $('.check-item').prop('checked', $(this).is(':checked'));
        });

    </script>
@endpush

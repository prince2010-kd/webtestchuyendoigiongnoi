<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;">
                <input type="checkbox" id="check-all">
            </th>
            <th style="width: 80px;">STT</th>
            <th>TIÊU ĐỀ</th>
            <th style="width: 110px; text-align:center;">THAO TÁC</th>
            <th style="width: 130px; text-align:center;">TRẠNG THÁI</th>
        </tr>
    </thead>
    <tbody>
        @php $stt = 1; @endphp
        @forelse ($khoahocs as $khoahoc)
            <tr>
                <!-- Checkbox -->
                <td style="text-align:center;">
                    <input type="checkbox" class="check-item" value="{{ $khoahoc->id }}">
                </td>

                <!-- STT -->
                <td>{{ $stt++ }}</td>

                <!-- Tiêu đề -->
                <td>{{ $khoahoc->tieu_de }}</td>

                <!-- Thao tác -->
                <td style="text-align:center;">
                    <a href="{{ route('admin.khoahoc.edit', $khoahoc->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $khoahoc->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>

                <!-- Trạng thái -->
                <td style="text-align:center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox"
                            data-id="{{ $khoahoc->id }}" {{ $khoahoc->active ? 'checked' : '' }}>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

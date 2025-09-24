<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th>Hình ảnh</th>
            <th>Tiêu đề</th>
            <th style="width: 110px;">Thao tác</th>
            <th style="width: 130px;">Trạng thái</th>
            <th style="width: 70px;">STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($images as $image)
            <tr>
                <td style="text-align: center;">
                    <input type="checkbox" class="check-item" value="{{ $image->id }}">
                </td>

                <td>
                    <img src="{{ asset($image->image_path) }}" alt="{{ $image->alt_text }}" style="width: 100px; height: auto;">
                </td>

                <td>{{ $image->alt_text }}</td>

                <td style="text-align: center;">
                    <a href="{{ route('admin.hinhanhhdong.edit', $image->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $image->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>

                <td style="text-align: center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox"
                               data-id="{{ $image->id }}" {{ $image->active ? 'checked' : '' }}>
                    </div>
                </td>

                <td style="text-align: center;">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $image->id }}"
                           value="{{ $image->stt }}"
                           style="width: 60px; margin: auto;" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

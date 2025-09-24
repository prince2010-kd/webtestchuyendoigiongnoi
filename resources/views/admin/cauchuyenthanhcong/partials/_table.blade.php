<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th>Hình ảnh</th>
            <th>Tên học viên</th>
            <th>Nội dung</th>
            <th style="width: 110px;">Thao tác</th>
            <th style="width: 130px;">Trạng thái</th>
            <th style="width: 70px;">STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($stories as $story)
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="check-item" value="{{ $story->id }}">
                </td>

                <td>
                    <img src="{{ asset($story->image) }}" alt="{{ $story->name }}"
                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                </td>

                <td>{{ $story->name }}</td>

                <td>{{ Str::limit(strip_tags($story->content), 80) }}</td>

                <td class="text-center">
                    <a href="{{ route('admin.thanhcong.edit', $story->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $story->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>

                <td class="text-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox"
                               data-id="{{ $story->id }}" {{ $story->active ? 'checked' : '' }}>
                    </div>
                </td>

                <td class="text-center">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $story->id }}"
                           value="{{ $story->stt }}"
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

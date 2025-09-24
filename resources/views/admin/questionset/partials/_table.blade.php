<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;"><input type="checkbox" id="check-all"></th>
            <th>Tiêu đề bộ đề</th>
            <th>Thời lượng</th>
            <th style="width: 110px; text-align:center;">Thao tác</th>
            <th style="width: 130px; text-align:center;">Trạng thái</th>
            <th style="width: 90px; text-align:center;">STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($sets as $item)
            <tr>
                <td style="text-align:center;">
                    <input type="checkbox" class="check-item" value="{{ $item->id }}">
                </td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->duration }} phút</td>
                <td style="text-align:center;">
                    <a href="{{ route('admin.questionset.edit', $item->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
                <td style="text-align:center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox" 
                               data-id="{{ $item->id }}" {{ $item->active ? 'checked' : '' }}>
                    </div>
                </td>
                <td style="text-align:center;">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $item->id }}"
                           value="{{ $item->stt }}" style="width: 60px;" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

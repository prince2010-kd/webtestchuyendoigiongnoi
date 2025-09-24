<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th>Tiêu đề</th>
            <th style="width: 110px;">Thao tác</th>
            <th style="width: 130px;">Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="check-item" value="{{ $item->id }}">
                </td>

                <td>{{ $item->title }}</td>

                <td class="text-center">
                    <a href="{{ route('admin.anhngu.edit', $item->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>

                <td class="text-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox"
                               data-id="{{ $item->id }}" {{ $item->active ? 'checked' : '' }}>
                    </div>
                </td>

            </tr>
        @endforeach
    </tbody>
</table>

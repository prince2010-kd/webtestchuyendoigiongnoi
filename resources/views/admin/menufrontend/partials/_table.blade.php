<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th>Tiêu đề</th>
            <th>Vị trí</th>
            <th style="width: 130px;">Trạng thái</th>
            <th style="width: 110px;">Thao tác</th>
            <th style="width: 110px;">STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse($menuTree1 as $menu)
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="check-item" value="{{ $menu->id }}">
                </td>
                <td>{{ $menu->title }}</td>
                <td>{{ $menu->position }}</td>
                <td class="text-center">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox" data-id="{{ $menu->id }}" {{ $menu->active ? 'checked' : '' }}>
                    </div>
                </td>

                <td class="text-center">
                    <a href="{{ route('admin.menufrontend.edit', $menu->id) }}" class="btn btn-sm btn-info"
                        title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $menu->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
                <td class="text-center">
                    <input type="number" class="form-control stt-input" style="width: 70px; margin: auto"
                        data-id="{{ $menu->id }}" value="{{ $menu->stt }}">
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>
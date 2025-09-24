<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th>Tiêu đề</th>
            <th>Bộ đề</th>
            <th >Thao tác</th>
            <th >Trạng thái</th>
            <th >STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $item)
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="check-item" value="{{ $item->id }}">
                </td>

                <td>{{ $item->title }}</td>

               <td>{{ $item->questionSet->title ?? '---' }}</td>

                <td class="text-center">
                    <a href="{{ route('admin.quality.edit', $item->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
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

                <td class="text-center">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $item->id }}"
                           value="{{ $item->stt }}"
                           style="width: 60px; margin: auto;" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

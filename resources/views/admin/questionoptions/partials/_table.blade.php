<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;">
                <input type="checkbox" id="check-all">
            </th>
            <th style="width: 90px; text-align:center;">STT</th>
            <th>Câu hỏi</th>
            <th style="width: 200px; text-align:center;">Đáp án đúng</th>
            <th style="width: 200px; text-align:center;">Nội dung đáp án</th>
            <th style="width: 110px; text-align:center;">Thao tác</th>
            <th style="width: 130px; text-align:center;">Trạng thái</th>
            {{-- <th style="width: 90px; text-align:center;">STT</th> --}}
        </tr>
    </thead>
    <tbody>
        @forelse ($questionOptions as $option)
            <tr>
                <td style="text-align:center;">
                    <input type="checkbox" class="check-item" value="{{ $option->id }}">
                </td>

                <td style="text-align:center;">{{ ($questionOptions->currentPage() - 1) * $questionOptions->perPage() + $loop->iteration }}</td>

                <td>{{ Str::limit($option->question->content, 80) }}</td>
                <td style="text-align: center">{{ $option->label }}</td>
                <td style="text-align: center">{{ $option->text }}</td>

                <td style="text-align:center;">
                    <a href="{{ route('admin.questionoption.edit', $option->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $option->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>

                <td style="text-align:center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox"
                               data-id="{{ $option->id }}"
                               {{ $option->active ? 'checked' : '' }}>
                    </div>
                </td>

                {{-- <td style="text-align:center;">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $option->id }}"
                           value="{{ $option->stt }}"
                           style="width: 60px;" />
                </td> --}}
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không có lựa chọn nào.</td>
            </tr>
        @endforelse
    </tbody>
</table>

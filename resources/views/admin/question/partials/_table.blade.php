<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;">
                <input type="checkbox" id="check-all">
            </th>
            <th>Bộ đề</th>
            <th style="width: 100px">Câu số</th>
            <th>Nội dung câu hỏi</th>
            {{-- <th style="width: 150px; text-align:center;">Đáp án đúng</th> --}}
            <th style="width: 110px">Thao tác</th>
            <th style="width: 130px">Trạng thái</th>
            <th style="width: 90px; text-align:center;">STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($questions as $item)
            <tr>
                <td style="text-align:center;">
                    <input type="checkbox" class="check-item" value="{{ $item->id }}">
                </td>

                <td style="width: 100px">{{ $item->questionSet->title ?? '(Không có bộ đề)' }}</td>

                <td>{{ $item->cau }}</td>

                <td>{{ Str::limit($item->content, 100) }}</td>

                {{-- <td>
                    @if ($item->type === 'multiple_choice')
                        {{ $item->correct_answer }}
                    @elseif ($item->type === 'short_answer')
                        @php
                            $answers = json_decode($item->correct_answer, true);
                        @endphp
                        @if (is_array($answers))
                            {{ implode(', ', $answers) }}
                        @else
                            {{ $item->correct_answer }}
                        @endif
                    @endif
                </td> --}}

                <td style="text-align:center;">
                    <a href="{{ route('admin.question.edit', $item->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>

                <td style="text-align:center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox"
                               data-id="{{ $item->id }}"
                               {{ $item->active ? 'checked' : '' }}>
                    </div>
                </td>

                <td style="text-align:center;">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $item->id }}"
                           value="{{ $item->stt }}"
                           style="width: 60px;" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

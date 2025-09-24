<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th>Họ tên</th>
            <th>Chức vụ</th>
            <th style="width: 110px;">Thao tác</th>
            <th style="width: 130px;">Trạng thái</th>
            <th style="width: 70px;">STT</th>
        </tr>
    </thead>
    <tbody>
        @php
            function renderTeacherRows($teachers, &$index = 1, $level = 0)
            {
                foreach ($teachers as $teacher) {
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);

                    echo '<tr>';

                    echo '<td style="text-align:center;">
                            <input type="checkbox" class="check-item" value="' . $teacher->id . '">
                          </td>';

                    echo '<td>' . $indent . htmlspecialchars($teacher->name) . '</td>';

                    echo '<td>' . $indent . htmlspecialchars($teacher->position) . '</td>';

                    echo '<td style="text-align:center;">
                            <a href="' . route('admin.giaovien.edit', $teacher->id) . '" class="btn btn-sm btn-info" title="Chỉnh sửa">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $teacher->id . '" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                          </td>';

                    echo '<td style="text-align:center;">
                            <div class="form-check form-switch">
                                <input class="form-check-input active-toggle" type="checkbox"
                                    data-id="' . $teacher->id . '" ' . ($teacher->active ? 'checked' : '') . '>
                            </div>
                          </td>';

                    echo '<td style="text-align:center;">
                            <input type="number" class="form-control form-control-sm stt-input" 
                                   data-id="' . $teacher->id . '" 
                                   value="' . $teacher->stt . '" 
                                   style="width: 60px; margin: auto;" />
                          </td>';

                    echo '</tr>';

                    $index++;

                    if (!empty($teacher->children)) {
                        renderTeacherRows($teacher->children, $index, $level + 1);
                    }
                }
            }
        @endphp

        @php $index = 1; @endphp
        @forelse ($giaoviens as $teacher)
            @php renderTeacherRows([$teacher], $index); @endphp
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

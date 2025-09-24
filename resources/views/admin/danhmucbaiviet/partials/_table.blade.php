<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;">
                <input type="checkbox" id="check-all">
            </th>
            <th>TIÊU ĐỀ</th>
            <th style="width: 120px; ">Danh mục</th>
            <th style="width: 110px; text-align:center;">THAO TÁC</th>
            <th style="width: 130px; text-align:center;">Trạng thái</th>
            <th style="width: 130px; text-align:center;">Nổi bật</th>
            <th style="width: 90px; text-align:center;">STT</th>
        </tr>
    </thead>
    <tbody>
        @php
            function renderCategoryRows($categories, $level = 0) {
                foreach ($categories as $category) {
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);

                    echo '<tr>';
                    echo '<td style="text-align:center;">
                            <input type="checkbox" class="check-item" value="' . $category->id . '">
                          </td>';

                    echo '<td>' . $indent . htmlspecialchars($category->title) . '</td>';
                    echo '<td>' . $indent . htmlspecialchars($category->type) . '</td>';
                    echo '<td style="text-align:center;">
                            <a href="' . route('admin.danhmucbaiviet.edit', $category->id) . '" class="btn btn-sm btn-info" title="Chỉnh sửa">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $category->id . '" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                          </td>';

                    echo '<td style="text-align:center;">
                            <div class="form-check form-switch">
                                <input class="form-check-input active-toggle" type="checkbox" data-id="' . $category->id . '" ' . ($category->active ? 'checked' : '') . '>
                            </div>
                          </td>';

                    echo '<td style="text-align:center;">
                            <div class="form-check form-switch">
                                <input class="form-check-input is-featured-toggle" type="checkbox" data-id="' . $category->id . '" ' . ($category->is_featured ? 'checked' : '') . '>
                            </div>
                          </td>';

                    echo '<td style="text-align:center;">
                            <input type="number" class="form-control form-control-sm stt-input"
                                   data-id="' . $category->id . '"
                                   value="' . $category->stt . '"
                                   style="width: 60px;" />
                          </td>';
                    echo '</tr>';

                    if (!empty($category->children)) {
                        renderCategoryRows($category->children, $level + 1);
                    }
                }
            }
        @endphp

        @forelse ($danhmucbaiviet as $category)
            @php
                renderCategoryRows([$category]);
            @endphp
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

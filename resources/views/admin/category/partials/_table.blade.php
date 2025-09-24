<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th style="width: 80px;">STT</th>
            <th>Tên danh mục</th>
            <th>Mô tả</th>
            <th style="width: 110px;">Thao tác</th>
            <th style="width: 130px;">Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @php
            function renderCategoryRows($categories, &$index = 1, $level = 0)
            {
                foreach ($categories as $category) {
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);

                    echo '<tr>';

                    // Checkbox
                    echo '<td style="text-align:center;">
                            <input type="checkbox" class="check-item" value="' . $category->id . '">
                          </td>';

                          echo '<td>' . $index . '</td>';

                    // Tên danh mục (title)
                    echo '<td>' . $indent . e($category->title) . '</td>';

                    // Mô tả (content)
                    echo '<td>' . htmlentities(Str::limit(strip_tags($category->content), 80), ENT_QUOTES, 'UTF-8') . '</td>';


                    // Nút sửa/xoá
                    echo '<td style="text-align:center;">
                            <a href="' . route('admin.category.edit', $category->id) . '" class="btn btn-sm btn-info" title="Chỉnh sửa">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $category->id . '" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                          </td>';

                    // Toggle trạng thái
                    echo '<td style="text-align:center;">
                            <div class="form-check form-switch">
                                <input class="form-check-input active-toggle" type="checkbox"
                                    data-id="' . $category->id . '" ' . ($category->active ? 'checked' : '') . '>
                            </div>
                          </td>';

                    echo '</tr>';

                    $index++;

                    // Nếu có children → đệ quy tiếp
                    if (isset($category->children) && $category->children->isNotEmpty()) {
                        renderCategoryRows($category->children, $index, $level + 1);
                    }
                }
            }
        @endphp
        @php $index = 1; @endphp
        @forelse ($categories as $category)
            @php renderCategoryRows([$category], $index); @endphp
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

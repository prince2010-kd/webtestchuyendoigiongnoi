<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;">
                <input type="checkbox" id="check-all">
            </th>
            <th>TIÊU ĐỀ</th>
            <th>THAO TÁC</th>
            <th>Trạng thái</th>
            <th style="width: 50px;">STT</th>
        </tr>
    </thead>
    <tbody>
        @php
            function renderSliderRows($sliders, &$index = 1, $level = 0) {
                foreach ($sliders as $slider) {
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                    echo '<tr>';
                    echo '<td style="text-align:center;">
                            <input type="checkbox" class="check-item" value="' . $slider->id . '">
                          </td>';
                    echo '<td>' . $indent . htmlspecialchars($slider->title) . '</td>';

                    echo '<td>
                            <a href="' . route('admin.slider.edit', $slider->id) . '" class="btn btn-sm btn-info" title="Chỉnh sửa">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $slider->id . '" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                          </td>';

                    echo '<td>
                            <div class="form-check form-switch">
                                <input class="form-check-input active-toggle" type="checkbox" 
                                       data-id="' . $slider->id . '" ' . ($slider->active ? 'checked' : '') . '>
                            </div>
                          </td>';

                    echo '<td style="text-align:center;">
                            <input type="number" class="form-control form-control-sm stt-input" 
                                   data-id="' . $slider->id . '" 
                                   value="' . $slider->stt . '" 
                                   style="width: 60px; margin: auto;" />
                          </td>';
                    echo '</tr>';

                    $index++;

                    if (!empty($slider->children)) {
                        renderSliderRows($slider->children, $index, $level + 1);
                    }
                }
            }
        @endphp

        @php $index = 1; @endphp
        @forelse ($sliders as $slider)
            @php renderSliderRows([$slider], $index); @endphp
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

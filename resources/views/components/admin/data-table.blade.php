<div class="table-responsive">
<table class="table">
    <thead>
        <tr>
            @if($canDelete)
                <th style="width: 1%;">
                    <div class="form-check th-admin-fit">
                        <input type="checkbox" id="check-all" class="p-2 form-check-input"/>
                    </div>
                </th>
            @endif
            <!-- Hiển thị cột truyền vao ở đây -->
            @if(isset($column))
                @foreach($column as $col)
                    <th>{{ $col['label'] }}</th>
                @endforeach
            @endif
            <!-- Kết thúc hiển thị cột truyền vào -->
            @if($canEdit || $canDelete)
                <th class="text-center">Thao tác</th>
            @endif
            @if($canEdit)
                <th class="text-center td-admin-fit">Trạng thái</th>
                <th class="th-admin-fit text-center">STT</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
        <tr>
            <!-- checkbox -->
            @if($canDelete)
                <td>
                    <input 
                        type="checkbox" 
                        name="menu_ids[]" 
                        value="{{ $item->id }}" 
                        class="form-check-input"
                        id="{{ 'option-no' . $item->id }}"
                        data-id="{{ $item->id }}" 
                    />
                </td>
            @endif

            <!-- Hiển thị cột truyền vao ở đây -->
            @if(isset($column))
                @foreach($column as $col)
                    {{-- @if(isset($col['permis']) && $col['permis'] == 'can_edit' && $canEdit)--}}
                    @if($canEdit)
                        <td>
                            <a href="{{ $editRoutes[$item->id] }}"> {{ data_get($item, $col['name']) }} </a>
                        </td>
                    @else
                        <td>{{ data_get($item, $col['name']) }}</td>
                    @endif
                @endforeach
            @endif
            <!-- Kết thúc hiển thị cột truyền vào -->
            <!-- Thao tác  -->
            @if ($canEdit || $canDelete)
                <td>
                    <div class="action-btn-container text-center">
                        <a href="{{ $editRoutes[$item->id] }}"
                           class="btn btn-sm btn-info"
                           style="{{ $canEdit ? '' : 'display: none;'}}">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete"
                                data-id="{{ $item->id }}"
                                data-url="{{ $deleteRoutes[$item->id] }}"
                                style="{{ $canDelete ?  '' : 'display: none;' }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            @endif
            
            <!-- Trạng thái -->
            @if ($canEdit)
                <td class="text-center">
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input type="checkbox"
                            class="form-check-input status-switch"
                            data-id="{{ $item->id }}"
                            data-url="{{ $updateActiveRoutes[$item->id] }}"
                            @checked($item->active) 
                        />
                    </div>
                </td>
            @endif
            <!-- Số thứ tự -->
            @if ($canEdit)
                <td class="td-admin-fit">
                    <input type="text"
                        class="form-control px-1 py-0 text-center stt-input"
                        data-id="{{ $item->id }}" 
                        data-url="{{ $updateSttRoutes[$item->id] }}"
                        value="{{ $item->stt }}"
                    />
                </td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@push('scripts')
    <script>
        $(document).ready(function () {
            const debounceTimeouts = [];

            $('#check-all').change(function() {
                const id = $(this).attr('id').replace("check-all-", "");
                const checkStatus = $(this).prop('checked');
                $(`input[type="checkbox"][id^="option-no"]`).each(function(){
                    if(checkStatus)
                    {
                        $(this).prop('checked', true);
                    }else
                    {
                        $(this).prop('checked', false   );
                    }
                })
            })

            $('.status-switch').change(function(){
                const switchBtn = $(this);
                const id = $(this).data('id');
                const url = $(this).data('url');
                const isActive = $(this).is(':checked') ? 1 : 0;
                
                if (debounceTimeouts[id]) {
                    clearTimeout(debounceTimeouts[id]);
                }
                //console.log("url: " + url);
                debounceTimeouts[id] = setTimeout(() => {
                    switchBtn.prop('disabled', true)
                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: {
                            _token: '{{csrf_token()}}',
                            active: isActive
                        },

                        success: function(res)
                        {
                            toastr.success("Cập nhật trạng thái thành công!")
                        },
                        error: function (xhr) {
                            if (xhr.status === 403) {
                                    toastr.warning(xhr.responseJSON.message || 'Bạn không có quyền thực hiện hành động này');
                            } else {
                                toastr.error('Có lỗi xảy ra.');
                            }
                            switchBtn.prop('checked', !isActive);
                        },
                        complete: function(){
                            switchBtn.prop('disabled', false)
                        }
                    }) 
                }, 500);
            })

            $('.stt-input').change(function(){
                const id = $(this).data('id');
                const url = $(this).data('url');
                const inputEl = $(this);
                if(debounceTimeouts[id]) {
                    clearTimeout(debounceTimeouts[id])
                }
                debounceTimeouts[id] = setTimeout(() => {
                    $.ajax({
                        url: url,
                        method: 'PUT',
                        data: {
                            _token: '{{csrf_token()}}',
                            stt: inputEl.val()
                        },

                        success: function(res)
                        {
                            console.log(res)
                            // reloadTable();
                        },
                        error: function () {
                            toastr.error("Cập nhật trạng thái thất bại!")
                        },
                        complete: function(){
                            // switchBtn.prop('disabled', false)
                            toastr.success("Cập nhật trạng thái thành công!")
                        }
                    })
                }, 800)
            });

            $('.btn-delete').click(function() {
                let id = $(this).data('id');
                let url = $(this).data('url');
                if (!id) {
                    toastr.error('Không tìm thấy ID để xóa.');
                    return;
                }

                if (!confirm('Bạn có chắc chắn muốn xóa?')) return;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        toastr.success('Xóa menu thành công!');
                        $('button[data-id="' + id + '"]').closest('tr').remove();
                        location.reload();
                    },
                    error: function (xhr) {
                        if (xhr.status === 403) {
                            toastr.warning(xhr.responseJSON.message || 'Bạn không có quyền truy cập.');
                        } else {
                            toastr.error('Có lỗi xảy ra.');
                        }
                    }
                });
            })
        })
    </script>
@endpush
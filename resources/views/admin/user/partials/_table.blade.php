{{-- <table class="table">
    <thead>
        <tr>
            <th style="width: 1%;
            {{!kiemTraQuyen('admin/user', 'can_delete') ? 'display: none;' : '' }}
            ">
                <div class="form-check th-admin-fit">
                    <input type="checkbox" id="check-all" class="p-2 form-check-input" />
                </div>
            </th>
            <th>TIÊU ĐỀ</th>
            <th style="{{ !kiemTraQuyen('admin/user', 'can_edit') && !kiemTraQuyen('admin/user', 'can_delete') ? 'display: none !important;' : '' }}"
                class="text-center">Thao tác</th>
            <th class="text-center td-admin-fit"
                style="{{ !kiemTraQuyen('admin/user', 'can_edit') ? 'display: none;' : '' }}">Trạng thái</th>
            <th class="th-admin-fit">STT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td style="{{!kiemTraQuyen('admin/user', 'can_delete') ? 'display: none;' : '' }}">
                <input type="checkbox" name="menu_ids[]" value="{{ $user->id }}" id="{{ 'option-no' . $user->id }}"
                    class="form-check-input" data-id="{{ $user->id }}" />
            </td>
            <td>
                @if (kiemTraQuyen('admin/user', 'can_edit'))
                <a href="{{ route('admin.edit', $user->id) }}">
                    {{$user->name}}
                </a>
                @else
                <div href="{{ route('admin.edit', $user->id) }}">
                    {{$user->name}}
                </div>
                @endif
            </td>
            <td
                style="{{ !kiemTraQuyen('admin/user', 'can_edit') && !kiemTraQuyen('admin/user', 'can_delete') ? 'display: none !important;' : '' }}">
                <div class="action-btn-container">
                    <a href="{{ route('admin.edit', $user->id) }}" class="btn btn-sm btn-info action-btn"
                        title="Chỉnh sửa"
                        style="{{ !kiemTraQuyen('admin/user', 'can_edit') ? 'visibility: hidden;' : '' }}">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete action-btn" data-id="{{ $user->id }}"
                        title="Xóa"
                        style="{{ !kiemTraQuyen('admin/user', 'can_delete') ? 'visibility: hidden;' : '' }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </td>
            <td class="td-admin-fit">
                <div class="d-flex justify-content-center form-check form-switch"
                    style="{{ !kiemTraQuyen('admin/user', 'can_edit') ? 'display: none !important;' : '' }}">
                    <input class="form-check-input status-switch" type="checkbox" role="switch"
                        data-id="{{ $user->id }}" @if ($user->active == 1) checked @endif
                    />
                </div>
            </td>
            <td class="td-admin-fit">
                <input type="text" data-id="{{ $user->id }}" class="form-control px-1 py-0 stt-input text-center"
                    maxlength="" />
            </td>
        </tr>
        @endforeach
    </tbody>
</table> --}}

<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;">
                <input type="checkbox" id="check-all">
            </th>
            <th>Tên người dùng</th>
            <th>Email</th>
            <th style="width: 110px; text-align:center;">Thao tác</th>
            <th style="width: 130px; text-align:center;">Trạng thái</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
            <tr>
                <td style="text-align:center;">
                    <input type="checkbox" class="check-item" value="{{ $user->id }}">
                </td>
                <td>
                    <a href="{{ route('admin.user.edit', $user->id) }}">
                        {{ $user->name }}
                    </a>
                </td>

                <td>{{ $user->email }}</td>
                <td style="text-align:center;">
                    <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $user->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
                <td style="text-align:center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox" data-id="{{ $user->id }}" {{ $user->active ? 'checked' : '' }}>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>
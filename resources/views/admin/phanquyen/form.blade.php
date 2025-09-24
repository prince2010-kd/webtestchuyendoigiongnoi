@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-2 pt-3">
            <form id="menuForm" action="{{ $formAction }}" method="POST">
                @csrf
                @if($formMethod === 'PUT')
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" class="form-control" id="title" name="title"
                        value="{{ old('title', $menu->title ?? '') }}" required @if(!empty($readonly)) disabled @endif>
                </div>

                <div class="mb-3">
                    <label for="url" class="form-label">Đường dẫn</label>
                    <input type="text" class="form-control" id="url" name="url" value="{{ old('url', $menu->url ?? '') }}"
                        required placeholder="/admin" @if(!empty($readonly)) disabled @endif>
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Menu cha</label>
                    <select class="form-select" id="parent_id" name="parent_id" @if(!empty($readonly)) disabled @endif>
                        <option value="">Chọn menu cha</option>
                        @php
                            function renderMenuOptions($menus, $parentId = null, $level = 0)
                            {
                                foreach ($menus as $menu) {
                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                    $selected = ($menu->id == $parentId) ? 'selected' : '';
                                    echo '<option value="' . $menu->id . '" ' . $selected . '>' . $indent . $menu->title . '</option>';
                                    if ($menu->childrenRecursive && $menu->childrenRecursive->isNotEmpty()) {
                                        renderMenuOptions($menu->childrenRecursive, $parentId, $level + 1);
                                    }
                                }
                            }
                            renderMenuOptions($menus, old('parent_id', $menu->parent_id ?? ''));
                        @endphp
                    </select>
                </div>

                {{-- Gọi lại form tinymce --}}

                {{-- <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <x-tinymce id="description" name="description" class="tinymce" :disabled="!empty($readonly)">
                        {{ old('description', $menu->description ?? '') }}
                    </x-tinymce>
                </div> --}}


                <!-- Chọn icon -->
                @php
                    $icons = [
                        'fa-house',
                        'fa-user',
                        'fa-gear',
                        'fa-envelope',
                        'fa-star',
                        'fa-camera',
                        'fa-heart',
                        'fa-chart-line',
                        'fa-bell',
                        'fa-book',
                        'fa-cloud',
                        'fa-code',
                        'fa-comment',
                        'fa-compass',
                        'fa-download',
                        'fa-edit',
                        'fa-eye',
                        'fa-flag',
                        'fa-globe',
                        'fa-image',
                        'fa-key',
                        'fa-lock',
                        'fa-magic',
                        'fa-map',
                        'fa-microphone',
                        'fa-music',
                        'fa-paper-plane',
                        'fa-phone',
                        'fa-print',
                        'fa-question',
                        'fa-reply',
                        'fa-search',
                        'fa-share',
                        'fa-shield',
                        'fa-signal',
                        'fa-sliders',
                        'fa-thumbs-up',
                        'fa-trash',
                        'fa-upload',
                        'fa-user-plus',
                        'fa-video',
                        'fa-wallet'
                    ];
                @endphp

                <div class="mb-3">
                    @if(empty($readonly))
                        <!-- Nút toggle -->
                        <button type="button" id="toggle-icon-list" class="btn btn-outline-primary mb-2">
                            <i class="fa fa-icons"></i> Chọn Icon
                        </button>
                    @endif

                    <!-- Danh sách icon -->
                    <div id="icon-list" class="d-flex flex-wrap gap-2 border p-3"
                        style="max-height: 300px; overflow-y: auto; display: none !important;">
                        <div class="icon-item text-center" data-icon="" style="width: 50px; cursor: pointer;">
                            <div class="border rounded py-2 px-1 small text-muted">Không chọn</div>
                        </div>
                        @foreach($icons as $icon)
                            <div class="icon-item text-center" data-icon="fa {{ $icon }}" style="width: 50px; cursor: pointer;">
                                <i class="fa {{ $icon }} fa-xl"></i>
                            </div>
                        @endforeach
                    </div>

                    <input type="hidden" name="icon" id="selected-icon" value="{{ old('icon', $menu->icon ?? '') }}">
                    <div class="mt-2">
                        <strong>Icon đã chọn:</strong>
                        <span id="icon-preview" class="ms-2"><i
                                class="{{ old('icon', $menu->icon ?? '') }} fa-lg"></i></span>
                    </div>
                </div>


                @if(empty($readonly))
                    <button type="submit" class="btn btn-primary">{{ $submitButton }}</button>
                @endif
                <a href="{{ route('admin.phanquyen.list') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            var readonly = @json(!empty($readonly));

            // Highlight icon đã chọn khi load trang
            let selectedIcon = $('#selected-icon').val();
            $('#icon-list .icon-item').each(function () {
                if ($(this).data('icon') === selectedIcon) {
                    $(this).addClass('border border-primary rounded');
                }
            });

            if (!readonly) {
                // Toggle hiển thị danh sách icon
                $('#toggle-icon-list').on('click', function () {
                    $('#icon-list').toggle();
                });

                // Chọn icon
                $('#icon-list .icon-item').on('click', function () {
                    const iconClass = $(this).data('icon');
                    $('#selected-icon').val(iconClass);
                    $('#icon-preview').html(iconClass ? `<i class="${iconClass} fa-lg"></i>` : '');

                    $('#icon-list .icon-item').removeClass('border border-primary rounded');
                    $(this).addClass('border border-primary rounded');

                    $('#icon-list').hide(); // ẩn danh sách sau khi chọn
                });

            }
        });
    </script>
@endpush
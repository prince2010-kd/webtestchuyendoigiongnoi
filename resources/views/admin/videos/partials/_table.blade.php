<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;">
                <input type="checkbox" id="check-all">
            </th>
            <th>Tiêu đề</th>
            <th>VIDEO</th>
            <th style="width: 120px;">Loại</th>
            <th style="width: 110px; text-align:center;">THAO TÁC</th>
            <th style="width: 130px; text-align:center;">Trạng thái</th>
            <th style="width: 90px; text-align:center;">STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($videos as $video)
            <tr>
                <td style="text-align:center;">
                    <input type="checkbox" class="check-item" value="{{ $video->id }}">
                </td>

                <td>{{ $video->title }}</td>

                <td>
                    @if ($video->youtube_url)
                        <div class="ratio ratio-16x9" style="max-width: 320px;">
                            <iframe src="https://www.youtube.com/embed/{{ $video->youtube_id }}" 
                                frameborder="0" allowfullscreen></iframe>
                        </div>
                    @elseif ($video->local_path)
                        <div style="max-width: 320px;">
                            <video width="100%" height="180" controls>
                                <source src="{{ asset($video->local_path) }}" type="video/mp4">
                                Trình duyệt không hỗ trợ video.
                            </video>
                        </div>
                    @endif
                </td>

                <td>
                    {{ $video->youtube_url ? 'YouTube' : 'Tải lên' }}
                </td>

                <td style="text-align:center;">
                    <a href="{{ route('admin.video.edit', $video->id) }}" class="btn btn-sm btn-info" title="Chỉnh sửa">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $video->id }}" title="Xóa">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>

                <td style="text-align:center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox" 
                               data-id="{{ $video->id }}" 
                               {{ $video->active ? 'checked' : '' }}>
                    </div>
                </td>

                <td style="text-align:center;">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $video->id }}"
                           value="{{ $video->stt }}"
                           style="width: 60px;" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;">Không có video nào.</td>
            </tr>
        @endforelse
    </tbody>
</table>

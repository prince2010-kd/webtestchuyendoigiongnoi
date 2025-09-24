<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th>Tiêu đề bài viết</th>
            <th style="text-align: center">Bình luận đã duyệt</th>
            <th style="text-align: center">Bình luận chưa duyệt</th>
            <th >Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($uuDai as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td style="text-align: center">{{ $post->comments_count }}</td>
                <td style="text-align: center">
                    @if ($post->new_comments_count > 0)
                        <span class="text-dark">{{ $post->new_comments_count }}</span>
                    @else
                        <span class="text-muted">0</span>
                    @endif
                </td>
                {{-- <td>{!! $post->active ? '<span class="badge bg-success">Hiển thị</span>' : '<span class="badge bg-secondary">Ẩn</span>' !!} --}}
                </td>
                <td>
                    <a href="{{ route('admin.qlybluanuudai.comments', $post->id) }}" class="btn btn-sm btn-primary"
                        title="Xem bình luận">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>
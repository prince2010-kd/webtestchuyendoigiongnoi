<table class="table table-bordered mt-3" id="post-data">
    <thead>
        <tr>
            <th>Người gửi</th>
            <th>Email</th>
            <th>Bình luận</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($comments as $comment)
            <tr>
                <td>{{ $comment->name }}</td>
                <td>{{ $comment->email }}</td>
                <td>{{ $comment->comment }}</td>
                <td>{{ $comment->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}</td>
                <td>
                    @if ($comment->approved)
                        <span class="badge bg-success">Đã duyệt</span>
                    @else
                        <span class="badge bg-warning text-dark">Chưa duyệt</span>
                    @endif
                </td>
                <td>
                    @if (!$comment->approved)
                        <form action="{{ route('admin.qlybluanuudai.comment.approve', $comment->id) }}" method="POST"
                            style="display:inline-block">
                            @csrf
                            <button class="btn btn-sm btn-success" onclick="return confirm('Duyệt bình luận này?')">
                                <i class="fas fa-check-circle" title="Duyệt bình luận"></i>
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.qlybluanuudai.comment.unapprove', $comment->id) }}" method="POST"
                        style="display:inline-block">
                        @csrf
                        <button class="btn btn-sm btn-warning" onclick="return confirm('Hủy duyệt bình luận này?')">
                            <i class="fas fa-times-circle" title="Hủy duyệt"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach

        @if ($uuDai->comments->isEmpty())
            <tr>
                <td colspan="6" class="text-center text-muted">Chưa có bình luận nào.</td>
            </tr>
        @endif
    </tbody>
</table>


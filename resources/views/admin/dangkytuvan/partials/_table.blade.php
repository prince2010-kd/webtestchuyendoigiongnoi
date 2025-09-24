<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            {{-- <th style="width: 40px; text-align:center;"><input type="checkbox" id="check-all"></th> --}}
            <th>Họ tên</th>
            <th>Tuổi</th>
            <th>SĐT</th>
            <th>Email</th>
            <th>Khu vực</th>
            <th>Ngày đăng ký</th>
        </tr>
    </thead>
    <tbody>
        @forelse($dangkytuvan as $item)
            <tr>
                {{-- <td style="text-align:center;"><input type="checkbox" class="check-item" value="{{ $item->id }}"></td> --}}
                <td>{{ $item->hoten }}</td>
                <td>{{ $item->tuoi }}</td>
                <td>{{ $item->sdt }}</td>
                <td>{{ $item->email }}</td>
                <td>{{ $item->khuvuc }}</td>
                <td>{{ $item->created_at->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }}</td>
            </tr>
         @empty
            <tr>
                <td colspan="7" class="text-center">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>

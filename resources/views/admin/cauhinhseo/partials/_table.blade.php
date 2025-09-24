<table class="table table-bordered" id="post-data">
    <thead>
        <tr>
            <th style="width: 40px;"><input type="checkbox" id="check-all"></th>
            <th>STT</th>
            <th>Menu</th>
            <th>Meta Title</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($items as $index => $item)
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="check-item" value="{{ $item->page_key }}">
                </td>

                <td>{{ $items->firstItem() + $index }}</td> 
                
                <td>{{ $menuNames[$item->page_key] ?? $item->page_key }}</td>
                <td>{{ $metaTitles[$item->page_key] ?? '(Chưa có Meta Title)' }}</td>
                {{-- Cột hoạt động (public) --}}
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox" data-id="{{ $item->page_key }}" {{ $item->public ? 'checked' : '' }}>

                    </div>
                </td>


                <td>
                    <a href="{{ route('admin.cauhinhseo.edit', ['id' => ltrim($item->page_key, '/')]) }}"
                        class="btn btn-sm btn-info">
                        <i class="fa fa-edit"></i>
                    </a>


                    {{-- <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->page_key }}">
                        <i class="fa fa-trash"></i>
                    </button> --}}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có dữ liệu.</td>
            </tr>
        @endforelse
    </tbody>
</table>
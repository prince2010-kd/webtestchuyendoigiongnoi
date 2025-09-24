<table class="table table-bordered" id="product-data">
    <thead>
        <tr>
            <th style="width: 40px; text-align:center;">
                <input type="checkbox" id="check-all">
            </th>
            <th>TÊN SẢN PHẨM</th>
            <th>GIÁ BÁN</th>
            <th style="width: 110px; text-align:center;">THAO TÁC</th>
            <th style="width: 130px; text-align:center;">Trạng thái</th>
            <th style="width: 90px; text-align:center;">STT</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $product)
            <tr>
                <td style="text-align:center;">
                    <input type="checkbox" class="check-item" value="{{ $product->id }}">
                </td>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->sale_price ?? 0) }}đ</td>
                <td style="text-align:center;">
                    <a href="{{ route('admin.sanpham.edit', $product->id) }}" class="btn btn-sm btn-info">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $product->id }}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
                <td style="text-align:center;">
                    <div class="form-check form-switch">
                        <input class="form-check-input active-toggle" type="checkbox"
                            data-id="{{ $product->id }}" {{ $product->trangthai ? 'checked' : '' }}>
                    </div>
                </td>
                <td style="text-align:center;">
                    <input type="number" class="form-control form-control-sm stt-input"
                           data-id="{{ $product->id }}"
                           value="{{ $product->stt }}"
                           style="width: 60px;" />
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Không có sản phẩm nào.</td>
            </tr>
        @endforelse
    </tbody>
</table>

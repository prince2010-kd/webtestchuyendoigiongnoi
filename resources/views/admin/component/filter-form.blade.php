<form id="filterForm">
     <div class="row mb-4">
                <div class="col-md-2">
                    <select class="form-select page-size-show" name="page_size" aria-label="Default select example">
                        <option hidden="">Số bản ghi hiển thị</option>
                        <option value="10" selected="" {{ request('page_size') == 10 ? 'selected' : '' }}>10 dòng</option>
                        <option value="50" {{ request('page_size') == 50 ? 'selected' : '' }}>50 dòng</option>
                        <option value="100" {{ request('page_size') == 100 ? 'selected' : '' }}>100 dòng</option>
                        <option value="500" {{ request('page_size') == 500 ? 'selected' : '' }}>500 dòng</option>
                        <option value="1000" {{ request('page_size') == 1000 ? 'selected' : '' }}>1,000 dòng</option>
                        <option value="10000" {{ request('page_size') == 10000 ? 'selected' : '' }}>10,000 dòng</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="sortBy">
                        <option hidden {{ request('sortBy') == null ? 'selected' : '' }}>Sắp xếp theo</option>
                        <option value="asc" {{ request('sortBy') == 'asc' ? 'selected' : '' }}>Theo A-Z</option>
                        <option value="desc" {{ request('sortBy') == 'desc' ? 'selected' : '' }}>Theo Z-A</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" value="{{ request('keyword') }}" id="defaultFormControlInput"
                        placeholder="Nhập tiêu đề" aria-describedby="defaultFormControlHelp">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary float-start btn-search">Tìm kiếm</button>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-danger" style="height: fit-content;" id="delete-selected">Xóa lựa chọn</button>
                </div>
            </div>
</form>
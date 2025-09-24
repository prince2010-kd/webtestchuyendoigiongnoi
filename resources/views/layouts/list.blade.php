@extends('layouts.user')

@section('content')

<div class="container-xxl my-4 vh-100">    
    <h3>Danh mục bài viết</h3>
    <div class="card p-2 pt-3">
        <div class="row">
            <div class="col-md-2">
                <select class="form-select page-size-show" name="page_size" aria-label="Default select example">
                    <option hidden="">Số bản ghi hiển thị</option>
                    <option value="10" selected="">10 dòng</option>
                    <option value="50">50 dòng</option>
                    <option value="100">100 dòng</option>
                    <option value="500">500 dòng</option>
                    <option value="1000">1,000 dòng</option>
                    <option value="10000">10,000 dòng</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select sortByShow" name="sortBy" aria-label="Default select example">
                    <option hidden="">Sắp xếp theo</option>
                    <option value="desc" selected="">Theo Z-A</option>
                    <option value="asc">Theo A-Z</option>
                </select>
            </div>               
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" id="defaultFormControlInput" placeholder="Nhập tiêu đề/mô tả" aria-describedby="defaultFormControlHelp">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-primary float-start btn-search">Tìm kiếm</button>
            </div>
            <div class="col-md-2 ">
                <button type="button" class="showActionDelete btn btn-danger float-end" data-table="slider">Xóa lựa chọn</button>
            </div>
        </div>

        <div class="ajax-pagination-container" data-table="#post-data" data-pagination="#pagination-post">
            <table class="table">
                <thead>
                    <tr>
                        @yield('tbl-headers')
                    </tr>
                </thead>
                <tbody id="post-data" style="position: relative;">
                    @yield('tbl-rows')
                </tbody>
            </table>
                

        </div>        
    </div>
</div>

@endsection

@push('scripts')

@endpush

@push('styles')
<style>
   
</style>
@endpush
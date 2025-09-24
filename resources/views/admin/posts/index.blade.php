@extends('layouts.user')

@section('content')


<div class="container-xxl my-4 vh-100">    
    <h3>Danh mục bài viết</h3>
    <div class="card p-2 pt-3">
        <div class="row mb-4">
            <div class="col-md-2 {{ !kiemTraQuyen('admin/list-posts', 'can_add') }}">
                <a href="{{ route('admin.category.create') }}" class="btn btn-primary mb-3">Thêm mới</a>
            </div>
        </div>
        <div class="ajax-pagination-container" data-table="#post-data" data-pagination="#pagination-post">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Content</th>
                        <th>STT</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="post-data" style="position: relative;">
                    @include('admin.posts.list')
                </tbody>
            </table>
                
            <div class="mt-4 pagination" id="pagination-post">
                @include('admin.component.pagination')
            </div>   
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
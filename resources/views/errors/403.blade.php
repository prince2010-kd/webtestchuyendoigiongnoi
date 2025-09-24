@extends('layouts.user')

@section('content')
<div class="container mt-5">
    <div class="alert alert-warning">
        <h4 class="alert-heading">403 - Không có quyền truy cập</h4>
        <p>Bạn sẽ được chuyển hướng sau vài giây...</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    //toastr.warning('Bạn không có quyền truy cập.');
    setTimeout(() => {
        const originalUrl = @json(session('original_url'));
        if (originalUrl) {
            window.location.href = originalUrl;
        } else {
            window.location.href = '/admin/dashboard';
        }
    }, 3000);
</script>
@endpush

<!DOCTYPE html>
<html lang="en">

<head>
    
</head>

<body>


    <main class="my-5">
    <div class="container d-flex justify-content-center">
        <div class="position-relative text-center" style="max-width: 700px;">
            <img src="{{ asset('frontend/asset/images/404.gif') }}" alt="404 Not Found" class="img-fluid w-100">

            <div class="position-absolute bottom-0 start-50 translate-middle-x black w-100 pb-3 px-3">
                <h1 class="h4 mb-1">Không tìm thấy đường dẫn này!</h1>
                <p class="mb-0">Bạn có thể quay lại <a href="{{ url('/') }}" class="text-warning text-decoration-underline">Trang chủ</a> để tiếp tục.</p>
            </div>
        </div>
    </div>
</main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
            crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="{{ asset('frontend/asset/js/script.js') }}"></script>
</body>

</html>

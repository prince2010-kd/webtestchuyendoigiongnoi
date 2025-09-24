<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'ICEAI')</title>
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>    
    @stack('styles')
</head>
<body>
    @yield('content')
    <!-- @yield('scripts')     -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/libs/apex-charts/apexcharts.js') }}"></script>

    <script src="{{ asset('sneat/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('sneat/libs/masonry/masonry.js') }}"></script>

    <script src="{{ asset('sneat/libs/popper/popper.js') }}"></script>

    <script src="{{ asset('sneat/libs/highlight/highlight.js') }}"></script>
    @yield('scripts')
    <script>
        
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard')</title>
  <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
  <link rel="manifest" href="/site.webmanifest" />
  {{--
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/css/custom.css') }}" />
  <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://unpkg.com/alpinejs" defer></script>

  @stack('styles')
</head>

<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">

      <!--Sidebar-->
      @include('partials.sidebar')

      <!--Layout-->
      <div class="layout-page">

        <!--Navbar-->
        @include('partials.navbar')

        <!--Content-->
        <div class="content-wrapper">
          @yield('content')
        </div>
        <!-- End of content -->

        @include('partials.footer')
      </div>
    </div>
  </div>

  <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/extended-ui-perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>


  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (typeof Helpers !== 'undefined') {
        Helpers.init();
      }
    });
  </script>
  <script src="{{ asset('sneat/assets/js/ui-popover.js') }}"></script>

  <script src="{{ asset('sneat/assets/js/config.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/main.js') }}"></script>
  <script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

  <script src="{{ asset('sneat/assets/js/dashboards-analytics.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/pages-account-settings-account.js') }}"></script>
  <script src="{{ asset('sneat/assets/js/ui-toasts.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
  <!-- Choices CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<!-- Choices JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>


  @stack('scripts')
  @push('scripts')



  @endpush


  <script>
    $(document).ready(function () {
      $('.ajax-pagination-container').each(function () {
        var container = $(this);
        var tableSelector = container.data('table');
        var paginationSelector = container.data('pagination');

        // Bắt sự kiện click trên phân trang bên trong container này
        container.off('click', paginationSelector + ' a').on('click', paginationSelector + ' a', function (e) {
          e.preventDefault();

          var url = $(this).attr('href');
          if (!url) return;

          showLoading(tableSelector);

          $.ajax({
            url: url,
            type: 'GET',
            success: function (res) {
              $('#post-data').html(res.table);
              $('#pagination-post').html(res.pagination);

              hideLoading(tableSelector);
            },
            error: function () {
              alert('Không thể tải dữ liệu.');
              hideLoading(tableSelector);
            }
          });
        });
      });
    });

    function showLoading(tableSelector) {
      if ($(tableSelector + ' .loading-overlay').length === 0) {
        $(tableSelector).append(`
            <tr class="loading-overlay">
                <td colspan="100%" class="text-center py-3">
                    <div class="spinner"></div>
                </td>
            </tr>
        `);
      }
    }

    function hideLoading(tableSelector) {
      $(tableSelector + ' .loading-overlay').remove();
    }


  </script>

  <script>
    var route_prefix = "/laravel-filemanager"; // Đường dẫn mặc định

    tinymce.init({
      selector: 'textarea.tinymce-editor', // Thay bằng selector bạn dùng
      plugins: 'image link media code',
      toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright | image link media | code',
      relative_urls: false,
      file_picker_callback: function (callback, value, meta) {
        let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
        let y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

        let cmsURL = route_prefix + '?editor=' + meta.fieldname;
        if (meta.filetype == 'image') {
          cmsURL = cmsURL + "&type=Images";
        } else {
          cmsURL = cmsURL + "&type=Files";
        }

        tinyMCE.activeEditor.windowManager.openUrl({
          url: cmsURL,
          title: 'File Manager',
          width: x * 0.8,
          height: y * 0.8,
          onMessage: (api, message) => {
            callback(message.content);
          }
        });
      }
    });
  </script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.layout-menu-toggle');
    const layoutWrapper = document.querySelector('.layout-wrapper');
    const icon = toggle.querySelector('i');

    if (toggle && layoutWrapper) {
      toggle.addEventListener('click', function () {
        layoutWrapper.classList.toggle('layout-menu-collapsed');

        // Đảo icon
        if (layoutWrapper.classList.contains('layout-menu-collapsed')) {
          icon.classList.remove('bx-chevron-left');
          icon.classList.add('bx-chevron-right');
        } else {
          icon.classList.remove('bx-chevron-right');
          icon.classList.add('bx-chevron-left');
        }
      });
    }
  });
</script>



</body>

</html>
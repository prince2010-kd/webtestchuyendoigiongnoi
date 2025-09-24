@php
    use App\Helpers\MenuHelper;
    $currentURL = request()->path();
    $userId = auth()->id();

    $menus = MenuHelper::dataMenuBackend($userId);
    $menu_tree = MenuHelper::build_menu_tree($menus, 0);
    
    // print_r($menu_tree);
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="app-brand demo mb-2">
        <a href="/admin/dashboard" class="app-brand-link">
            <img src="{{ asset('frontend/asset/images/logo1.png') }}" alt="Logo" />
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ $currentURL == 'admin/dashboard' ? 'active open' : '' }}">
            <a href="{{ url('admin/dashboard') }}" class="menu-link px-3"
             {{-- style="padding-left: 10px !important;" --}}
             >
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Bảng điều khiển</div>
            </a>
        </li>

        {!! MenuHelper::renderMenuBackend($menu_tree, $currentURL) !!}
    </ul>
</aside>
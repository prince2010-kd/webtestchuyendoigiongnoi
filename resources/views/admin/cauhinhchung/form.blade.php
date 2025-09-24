@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4 vh-100">
        <h3>Cấu hình chung</h3>
        <div class="card p-2 pt-3">
            <form action="{{ route('cauhinhchung.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="custom-form-row" style="align-items: center;">
                    <label for="logo">Logo</label>
                    <input type="file" name="logo" id="logo" class="form-control" accept="image/*"
                        onchange="previewLogo(event)" style="flex:1;">
                    <div class="logo-preview-container">
                        <img id="logo-preview" src="{{ $fields['logo'] ?? '#' }}" alt="Logo Preview"
                            style="max-width: 100%; max-height: 100%; {{ empty($fields['logo']) ? 'display:none;' : '' }}">
                    </div>
        </div>



        @php
            $fieldsDef = [
                'email' => ['label' => 'Email', 'required' => true, 'type' => 'email'],
                'phone' => ['label' => 'Số điện thoại', 'required' => true, 'type' => 'text'],
                'address' => ['label' => 'Địa chỉ', 'required' => true, 'type' => 'text'],
                'facebook' => ['label' => 'Facebook', 'required' => false, 'type' => 'text'],
                'youtube' => ['label' => 'Youtube', 'required' => false, 'type' => 'text'],
                // 'phi_du_thi' => ['label' => 'Phí dự thi', 'required' => false, 'type' => 'number', 'step' => '0.01'],
                // 'phi_van_chuyen' => ['label' => 'Phí vận chuyển', 'required' => false, 'type' => 'number', 'step' => '0.01'],
                // 'tg_giu_cho' => ['label' => 'Thời gian giữ chỗ (phút)', 'required' => false, 'type' => 'number', 'step' => '1'],
                // 'tg_live_qrcode' => ['label' => 'Thời gian live QR Code (phút)', 'required' => false, 'type' => 'number', 'step' => '1'],
            ];
        @endphp

        @foreach ($fieldsDef as $key => $props)
            <div class="custom-form-row">
                <label for="{{ $key }}">{{ $props['label'] }}</label>
                <input type="{{ $props['type'] }}" name="fields[{{ $key }}]" id="{{ $key }}" class="form-control"
                    value="{{ old('fields.' . $key, $fields[$key] ?? '') }}" @if(!empty($props['required'])) required @endif
                    @if(isset($props['step'])) step="{{ $props['step'] }}" @endif>
            </div>
        @endforeach


        <div class="custom-form-row">
            <label for="gioithieu">Giới thiệu</label>
            <x-tinymce id="gioithieu" name="fields[gioithieu]" class="tinymce-editor" required style="flex:1;">
                {{ old('fields.gioithieu', $fields['gioithieu'] ?? '') }}
            </x-tinymce>
        </div>


        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Lưu</button>
            <a href="{{ route('admin.phanquyen.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
        </div>
        </form>
    </div>
    </div>

    
@endsection
@push('scripts')
<script>
        function previewLogo(event) {
            const input = event.target;
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '{{ $fields['logo'] ?? '#' }}';
                preview.style.display = '{{ empty($fields['logo']) ? 'none' : 'block' }}';
            }
        }
    </script>
@endpush
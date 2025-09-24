@extends('layouts.user')

@section('content')
  <div class="container-xxl my-4 vh-100">
    <h3>Cấu hình SEO</h3>
    <div class="card p-2 pt-3">
    <form action="{{ $formAction }}" method="POST">
      @csrf
      @if (isset($formMethod) && $formMethod === 'PUT')
      @method('PUT')
    @endif

      @php
    $fieldsDef = [
      'meta_title' => ['label' => 'Meta Title', 'required' => true, 'type' => 'text'],
      'meta_description' => ['label' => 'Meta Description', 'required' => false, 'type' => 'textarea'],
      'meta_keywords' => ['label' => 'Meta Keywords', 'required' => false, 'type' => 'text'],
    ];
    @endphp


      @foreach ($fieldsDef as $key => $props)
      <div class="custom-form-row">
      <label for="{{ $key }}">{{ $props['label'] }}</label>
      @if ($props['type'] === 'textarea')
      <textarea name="fields[{{ $key }}]" id="{{ $key }}" class="form-control" rows="3"
      @if(!empty($props['required'])) required @endif>{{ old('fields.' . $key, $fields[$key] ?? '') }}</textarea>
    @else
      <input type="{{ $props['type'] }}" name="fields[{{ $key }}]" id="{{ $key }}" class="form-control"
      value="{{ old('fields.' . $key, $fields[$key] ?? '') }}" @if(!empty($props['required'])) required @endif>
    @endif
      </div>
    @endforeach

      {{-- Chọn menu --}}
      <div class="custom-form-row mb-3">
      <label for="page_key">Chọn menu</label>
      <select name="page_key" id="page_key" class="form-control" required>
        <option value="home" {{ old('page_key', $page_key ?? '') == 'home' ? 'selected' : '' }}>Trang chủ</option>
        @foreach($menus as $key => $title)
      <option value="{{ $key }}" {{ old('page_key', $page_key ?? '') == $key ? 'selected' : '' }}>
      {{ $title }}
      </option>
      @endforeach
      </select>
      </div>

      <div class="text-center mt-4">
      <button type="submit" class="btn btn-primary">Lưu</button>
      <a href="{{ route('admin.cauhinhseo.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
      </div>

    </form>
    </div>
  </div>
@endsection
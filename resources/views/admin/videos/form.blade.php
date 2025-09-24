@extends('layouts.user')

@section('content')
    <div class="container-xxl my-4">
        <h3>{{ $formTitle }}</h3>
        <div class="card p-3">
            <form id="videoForm" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if($formMethod === 'PUT')
                    @method('PUT')
                @endif

                {{-- Nguồn video --}}
                <div class="custom-form-row mb-3">
                    <label class="form-label">Nguồn video</label>
                    <div>
                        <label><input type="radio" name="video_type" value="youtube" {{ old('video_type', $fields['video_type'] ?? '') !== 'upload' ? 'checked' : '' }}> YouTube</label>
                        &nbsp;&nbsp;
                        <label><input type="radio" name="video_type" value="upload" {{ old('video_type', $fields['video_type'] ?? '') === 'upload' ? 'checked' : '' }}> Tải lên</label>
                    </div>
                </div>

                {{-- Tiêu đề --}}
                <div class="custom-form-row mb-3">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $fields['title'] ?? '') }}">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Link YouTube --}}
                <div class="custom-form-row mb-3 video-youtube">
                    <label for="youtube_url" class="form-label">Link YouTube</label>
                    <input type="text" name="youtube_url" id="youtube_url"
                        class="form-control @error('youtube_url') is-invalid @enderror"
                        value="{{ old('youtube_url', $fields['youtube_url'] ?? '') }}">
                    @error('youtube_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    {{-- Khung preview --}}
                    <div class="mt-2 position-relative" id="youtube-preview-container"
                        style="max-width: 100%; max-height: 400px;">
                        {{-- Thumbnail + play --}}
                        <div id="youtube-thumbnail-wrapper" style="position: relative; display: none;">
                            <img id="youtube-thumbnail-preview" src="{{ $fields['youtube_thumbnail'] ?? '' }}"
                                alt="Thumbnail" style="width: 100%; max-height: 400px; object-fit: cover; cursor: pointer;">
                            <div id="play-button-overlay"
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                                                            background-color: rgba(0, 0, 0, 0.5); border-radius: 50%; padding: 20px; cursor: pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#fff"
                                    class="bi bi-play-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M11.596 8.697l-6.363 3.692A.5.5 0 0 1 4 11.985V4.015a.5.5 0 0 1 .76-.424l6.363 3.692a.5.5 0 0 1 0 .848z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Video Iframe --}}
                        <div id="youtube-video-wrapper" style="display: none;">
                            <iframe id="youtube-iframe" width="100%" height="400" frameborder="0"
                                allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>

                {{-- Upload file --}}
                <div class="custom-form-row mb-3 video-upload d-none">
                    <label for="video_file" class="form-label">Tệp video</label>
                    <input type="file" name="video_file" id="video_file"
                        class="form-control @error('video_file') is-invalid @enderror" accept="video/*">
                    @error('video_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    {{-- Preview video upload --}}
                    <div id="video-upload-preview-wrapper" class="mt-3"
                        style="{{ empty($fields['local_path']) ? 'display:none;' : '' }}">
                        <video id="video-upload-preview" width="100%" height="400" controls>
                            @if (!empty($fields['local_path']))
                                <source src="{{ asset($fields['local_path']) }}" type="video/mp4">
                            @endif
                            Trình duyệt không hỗ trợ video.
                        </video>
                    </div>

                </div>


                {{-- Submit --}}
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">{{ $submitButton ?? 'Lưu' }}</button>
                    <a href="{{ route('admin.video.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function extractYouTubeId(url) {
            const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|.*v=))([\w\-]+)/);
            return match ? match[1] : null;
        }

        function updateThumbnail(videoId) {
            const thumbnail = `https://img.youtube.com/vi/${videoId}/hqdefault.jpg`;
            const img = new Image();
            img.onload = function () {
                $('#youtube-thumbnail-preview').attr('src', thumbnail).show();
                $('#play-button-overlay').show();
                $('#youtube-thumbnail-wrapper').show();
                $('#youtube-video-wrapper').hide();
                $('#youtube-iframe').attr('src', '');
            };
            img.onerror = function () {
                resetPreview();
            };
            img.src = thumbnail;
        }

        function resetPreview() {
            $('#youtube-thumbnail-preview').hide().attr('src', '');
            $('#play-button-overlay').hide();
            $('#youtube-thumbnail-wrapper').hide();
            $('#youtube-video-wrapper').hide();
            $('#youtube-iframe').attr('src', '');
        }

        $(document).ready(function () {
            // Show/hide fields theo loại video
            function toggleVideoFields() {
                const type = $('input[name="video_type"]:checked').val();

                if (type === 'youtube') {
                    $('.video-youtube').removeClass('d-none');
                    $('.video-upload').addClass('d-none');

                    // Xoá preview upload
                    $('#video-upload-preview').attr('src', '');
                    $('#video-upload-preview-wrapper').hide();
                    $('#video_file').val('');

                    // Xoá luôn preview YouTube & input link
                    $('#youtube_url').val('');
                    resetPreview(); // <- Quan trọng: xoá thumbnail + iframe

                } else {
                    $('.video-upload').removeClass('d-none');
                    $('.video-youtube').addClass('d-none');

                    // Xoá preview upload
                    $('#video-upload-preview').attr('src', '');
                    $('#video-upload-preview-wrapper').hide();
                    $('#video_file').val('');

                    // Xoá preview YouTube
                    $('#youtube_url').val('');
                    resetPreview();
                }
            }

            const currentType = $('input[name="video_type"]:checked').val();
            if (currentType === 'youtube') {
                $('.video-youtube').removeClass('d-none');
                $('.video-upload').addClass('d-none');
            } else {
                $('.video-upload').removeClass('d-none');
                $('.video-youtube').addClass('d-none');
            }

            $('input[name="video_type"]').on('change', toggleVideoFields);

            // Xử lý khi nhập URL
            $('#youtube_url').on('input', function () {
                const url = $(this).val();
                const videoId = extractYouTubeId(url);
                if (videoId) {
                    updateThumbnail(videoId);
                } else {
                    resetPreview();
                }
            });

            // Nhấn vào play
            $('#play-button-overlay, #youtube-thumbnail-preview').on('click', function () {
                const url = $('#youtube_url').val();
                const videoId = extractYouTubeId(url);
                if (videoId) {
                    const iframeSrc = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                    $('#youtube-iframe').attr('src', iframeSrc);
                    $('#youtube-thumbnail-wrapper').hide();
                    $('#youtube-video-wrapper').show();
                } else {
                    alert('Vui lòng nhập link YouTube hợp lệ!');
                }
            });

            // Nếu có sẵn link khi mở trang
            const initUrl = $('#youtube_url').val();
            const initId = extractYouTubeId(initUrl);
            if (initId) {
                updateThumbnail(initId);
            } else {
                resetPreview();
            }

            // Khi chọn file video upload
            $('#video_file').on('change', function () {
                const file = this.files[0];
                if (file && file.type.startsWith('video/')) {
                    const url = URL.createObjectURL(file);
                    $('#video-upload-preview').attr('src', url);
                    $('#video-upload-preview-wrapper').show();
                } else {
                    $('#video-upload-preview').attr('src', '');
                    $('#video-upload-preview-wrapper').hide();
                }
            });

        });
    </script>
@endpush
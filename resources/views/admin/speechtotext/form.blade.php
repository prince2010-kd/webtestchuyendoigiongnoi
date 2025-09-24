@extends('layouts.user')

@section('content')
<div class="container-xxl my-4">
    <h3>{{ $formTitle ?? 'Chuyển giọng nói thành văn bản' }}</h3>
    <div class="card p-3">
        <form action="{{ $formAction ?? '#' }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề (tùy chọn)</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
            </div>

            {{-- Upload file MP3 --}}
            <div class="mb-3">
                <label for="audio_file" class="form-label">Upload file âm thanh (MP3, WAV...)</label>
                <input type="file" name="audio_file" id="audio_file" class="form-control" accept="audio/*">
                @error('audio_file')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="button" id="transcribe-btn" class="btn btn-info mb-3">Phiên âm file</button>

<div class="mb-3">
    <label class="form-label">Nội dung phiên âm (tự động tạo từ giọng nói)</label>
    <textarea name="text" id="transcript" rows="6" class="form-control" placeholder="Nội dung sẽ hiển thị ở đây...">{{ old('text') }}</textarea>
</div>



            {{-- Ghi âm mic (Web Speech API) --}}
            <button type="button" id="start-btn" class="btn btn-primary">Bắt đầu ghi âm</button>
            <button type="button" id="stop-btn" class="btn btn-secondary" disabled>Dừng ghi âm</button>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-success">Lưu bản ghi</button>
                <a href="{{ route('admin.speechtotext.list') }}" class="btn btn-secondary ms-2">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let recognition;
    let isRecording = false;

    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        alert('Trình duyệt của bạn không hỗ trợ Web Speech API!');
    } else {
        const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        recognition = new SpeechRecognition();

        recognition.lang = 'vi-VN'; // Ngôn ngữ tiếng Việt
        recognition.interimResults = true;
        recognition.continuous = true;

        recognition.onresult = function(event) {
            let interimTranscript = '';
            let finalTranscript = '';

            for (let i = event.resultIndex; i < event.results.length; i++) {
                let transcript = event.results[i][0].transcript;
                if (event.results[i].isFinal) {
                    finalTranscript += transcript;
                } else {
                    interimTranscript += transcript;
                }
            }

            document.getElementById('transcript').value = finalTranscript + interimTranscript;
        };

        recognition.onerror = function(event) {
            console.error('Speech recognition error:', event.error);
        };

        recognition.onend = function() {
            if (isRecording) {
                recognition.start(); // tự động restart nếu chưa dừng
            }
        };
    }

    document.getElementById('start-btn').addEventListener('click', function() {
        if (!isRecording) {
            recognition.start();
            isRecording = true;
            this.disabled = true;
            document.getElementById('stop-btn').disabled = false;
        }
    });

    document.getElementById('stop-btn').addEventListener('click', function() {
        if (isRecording) {
            recognition.stop();
            isRecording = false;
            this.disabled = true;
            document.getElementById('start-btn').disabled = false;
        }
    });

    document.getElementById('transcribe-btn').addEventListener('click', function () {
    let input = document.getElementById('audio_file');
    if (!input.files.length) {
        alert('Vui lòng chọn file âm thanh trước!');
        return;
    }

    let file = input.files[0];
    let formData = new FormData();
    formData.append('audio_file', file);
    formData.append('_token', '{{ csrf_token() }}');

    this.disabled = true;
    this.textContent = 'Đang phiên âm...';

    fetch('{{ route("admin.speechtotext.transcribe") }}', {
        method: 'POST',
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        if (data.status) {
            document.getElementById('transcript').value = data.text;
        } else {
            alert('Lỗi phiên âm: ' + (data.message || 'Không thể phiên âm'));
        }
        this.disabled = false;
        this.textContent = 'Phiên âm file';
    })
    .catch(() => {
        console.error('Fetch error:', error);
    alert('Lỗi khi gọi API phiên âm: ' + error.message);
    this.disabled = false;
    this.textContent = 'Phiên âm file';
    });
});

</script>
@endpush

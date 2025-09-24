<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transcript;
use Illuminate\Support\Facades\Storage;
use Google\Cloud\Speech\SpeechClient;

class SpeechToTextController extends Controller
{
    public function index(Request $request)
    {
        $query = Transcript::query();

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->input('keyword') . '%');
        }

        $query->orderBy(
            $request->input('sortBy', 'stt'),
            $request->input('sort', 'asc') === 'desc' ? 'desc' : 'asc'
        );

        $pageSize = $request->input('page_size', 10);
        $transcripts = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.speechtotext.partials._table', compact('transcripts'))->render(),
                'pagination' => view('admin.component.pagination', ['posts' => $transcripts])->render(),
                'status' => true
            ]);
        }

        return view('admin.speechtotext.list', compact('transcripts'));
    }

    public function create()
    {
        return view('admin.speechtotext.form', [
            'formAction' => route('admin.speechtotext.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới phiên âm',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:speech_to_texts,title',
            'audio_file' => 'required|file|mimes:mp3,wav,flac',
        ]);

        $this->ensureTempAudioDirectoryExists();

        $path = $request->file('audio_file')->store('temp_audio', 'public');
$fullPath = storage_path('app/public/' . $path);

if (!file_exists($fullPath)) {
    return back()->withErrors(['audio_file' => 'Không tìm thấy file audio sau khi upload.']);
}


        $transcriptText = $this->transcribeAudioFile($fullPath, $path);
        if (is_array($transcriptText) && isset($transcriptText['error'])) {
            return back()->withErrors(['audio_file' => $transcriptText['error']]);
        }

        $stt = (Transcript::max('stt') ?? 0) + 1;

        Transcript::create([
            'title' => $request->title,
            'text' => trim($transcriptText),
            'file_path' => $path,
            'stt' => $stt,
            'active' => 1,
        ]);

        return redirect()->route('admin.speechtotext.list')->with('success', 'Chuyển giọng nói thành văn bản thành công!');
    }

    public function transcribe(Request $request)
    {
        $request->validate([
            'audio_file' => 'required|file|mimes:mp3,wav,flac',
        ]);

        $this->ensureTempAudioDirectoryExists();

        $path = $request->file('audio_file')->store('temp_audio');
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy file audio sau khi upload.'
            ]);
        }

        $transcriptText = $this->transcribeAudioFile($fullPath, $path);
        if (is_array($transcriptText) && isset($transcriptText['error'])) {
            return response()->json([
                'status' => false,
                'message' => $transcriptText['error']
            ]);
        }

        return response()->json([
            'status' => true,
            'text' => trim($transcriptText),
        ]);
    }

    /**
     * Đảm bảo thư mục temp_audio tồn tại
     */
    private function ensureTempAudioDirectoryExists()
    {
        $tempDir = storage_path('app/public/temp_audio');
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0775, true);
    }
    }

    /**
     * Hàm dùng để gọi Google Speech API và trả kết quả hoặc lỗi
     */
    private function transcribeAudioFile($fullPath, $pathToDelete = null)
{
    try {
        $speech = new SpeechClient();

        $audioContent = file_get_contents($fullPath);
        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // Phát hiện encoding dựa trên phần mở rộng
        $encoding = match ($extension) {
            'mp3' => 'MP3',
            'wav' => 'LINEAR16',
            'flac' => 'FLAC',
            default => 'ENCODING_UNSPECIFIED'
        };

        $options = [
            'encoding' => $encoding,
            'sampleRateHertz' => 16000,
            'languageCode' => 'vi-VN',
        ];

        $results = $speech->recognize($audioContent, $options);

        $transcriptText = '';
        foreach ($results as $result) {
            $transcriptText .= $result->alternatives()[0]['transcript'] . ' ';
        }

        return $transcriptText;
    } catch (\Exception $e) {
        if ($pathToDelete) {
            Storage::delete($pathToDelete);
        }
        return ['error' => 'Lỗi khi chuyển giọng nói: ' . $e->getMessage()];
    } finally {
        if ($pathToDelete) {
            Storage::delete($pathToDelete);
        }
    }
}

}

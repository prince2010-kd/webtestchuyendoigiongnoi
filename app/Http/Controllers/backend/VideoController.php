<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Helpers\SortOrderHelper;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $query = Video::query();

        // Tìm kiếm theo tiêu đề
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('title', 'like', '%' . $keyword . '%');
            });
        }

        // Sắp xếp theo STT mặc định hoặc theo yêu cầu
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('stt', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $videos = $query->paginate($pageSize)->appends($request->all());

        // AJAX trả về partial view
        if ($request->ajax()) {
            $table = view('admin.videos.partials._table', compact('videos'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $videos])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.videos.list', compact('videos'));
    }

    public function create()
    {
        return view('admin.videos.form', [
            'formAction' => route('admin.video.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm video mới',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'video_type' => 'required|in:youtube,upload',
            'youtube_url' => 'nullable|required_if:video_type,youtube|url',
            'video_file' => 'nullable|required_if:video_type,upload|mimes:mp4,mov,avi|max:512000',
            'stt' => 'nullable|integer|min:0',
            'active' => 'nullable|boolean',
            'title' => 'required|string|max:255',
        ]);

        if ($request->video_type === 'youtube') {
            $request->request->remove('video_file');
        } else {
            $request->request->remove('youtube_url');
        }

        $maxStt = Video::max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        $data = [
            'stt' => $nextStt,
            'active' => 1,
            'title' => $request->title,
        ];

        if ($request->video_type === 'youtube') {
            $youtubeUrl = $request->youtube_url;
            preg_match("/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w\-]+)/", $youtubeUrl, $matches);
            $videoId = $matches[1] ?? null;

            if (!$videoId) {
                return back()->withInput()->withErrors(['youtube_url' => 'Không thể lấy video ID từ link YouTube.']);
            }

            $data['youtube_url'] = $youtubeUrl;
            $data['youtube_id'] = $videoId;
            $data['youtube_thumbnail'] = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
        } else {
            $file = $request->file('video_file');
            $filename = time() . '_' . Str::slug($file->getClientOriginalName(), '_') . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('videos', $filename, 'public');

            $data['local_path'] = "storage/{$path}";
            $data['original_name'] = $file->getClientOriginalName();
        }

        Video::create($data);

        return redirect()->route('admin.video.list')->with('success', 'Thêm video thành công!');
    }

    public function edit($id)
    {
        $video = Video::findOrFail($id);

        $fields = [
            'video_type' => $video->youtube_url ? 'youtube' : 'upload',
            'youtube_url' => $video->youtube_url,
            'youtube_thumbnail' => $video->youtube_thumbnail,
            'local_path' => $video->local_path,
            'original_name' => $video->original_name,
            'stt' => $video->stt,
            'active' => $video->active,
            'title' => $video->title,
        ];

        return view('admin.videos.form', [
            'formAction' => route('admin.video.update', $video->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa video',
            'fields' => $fields,
        ]);
    }


    public function update(Request $request, $id)
    {
        $video = Video::findOrFail($id);

        $request->validate([
            'video_type' => 'required|in:youtube,upload',
            'youtube_url' => 'nullable|required_if:video_type,youtube|url',
            'video_file' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request, $video) {
                    if ($request->video_type === 'upload' && !$value && !$video->local_path) {
                        $fail('Bạn cần chọn tệp video khi chưa có video đã tải lên.');
                    }
                },
                'mimes:mp4,mov,avi',
                'max:512000',
            ],

            'stt' => 'nullable|integer|min:0',
            'title' => 'required|string|max:255',
        ]);

        $data = [
            'title' => $request->title,
        ];

        if ($request->video_type === 'youtube') {
            $youtubeUrl = $request->youtube_url;
            preg_match("/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w\-]+)/", $youtubeUrl, $matches);
            $videoId = $matches[1] ?? null;

            if (!$videoId) {
                return back()->withInput()->withErrors(['youtube_url' => 'Không thể lấy video ID từ link YouTube.']);
            }

            $data['youtube_url'] = $youtubeUrl;
            $data['youtube_id'] = $videoId;
            $data['youtube_thumbnail'] = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";

            // Xoá video cũ nếu có
            if ($video->local_path) {
                Storage::disk('public')->delete(str_replace('storage/', '', $video->local_path));
                $data['local_path'] = null;
                $data['original_name'] = null;
            }
        } else {
            if ($request->hasFile('video_file')) {
                if ($video->local_path) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $video->local_path));
                }

                $file = $request->file('video_file');
                $filename = time() . '_' . Str::slug($file->getClientOriginalName(), '_') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('videos', $filename, 'public');

                $data['local_path'] = "storage/{$path}";
                $data['original_name'] = $file->getClientOriginalName();

                // Xoá thông tin youtube nếu có
                $data['youtube_url'] = null;
                $data['youtube_id'] = null;
                $data['youtube_thumbnail'] = null;
            }
        }

        $video->update($data);

        return redirect()->route('admin.video.list')->with('success', 'Cập nhật video thành công!');
    }

    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return response()->json(['success' => true, 'message' => 'Đã xoá video.']);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        Video::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        $item = Video::findOrFail($id);
        $item->active = $request->input('active') ? 1 : 0;
        $item->save();

        return response()->json(['message' => $item->active ? 'Đã bật hiển thị' : 'Đã tắt hiển thị']);
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(Video::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

}

<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Posts;
use App\Models\Comment;
use Illuminate\Http\Request;

class QlyBluanKinhNghiemHocController extends Controller
{
    public function index(Request $request)
    {
        $query = Posts::query()
            ->where('type', 'kinh-nghiem-hoc-ielts')
            ->withCount([
            'comments' => function ($query) {
                $query->where('approved', true); // Đếm số bình luận đã duyệt
            },
            'comments as new_comments_count' => function ($query) {
                $query->where('approved', false); // Đếm số bình luận chưa duyệt
            },
        ]);

        // Tìm kiếm theo tiêu đề
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where('title', 'like', '%' . $keyword . '%');
        }

        // Sắp xếp theo title hoặc mặc định theo stt
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') == 'desc' ? 'desc' : 'asc';
            $query->orderBy('title', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Phân trang
        $pageSize = $request->input('page_size', 10);
        $kinhNghiem = $query->paginate($pageSize)->appends($request->all());

        // Trả về AJAX nếu cần
        if ($request->ajax()) {
            $table = view('admin.qlybluanknghiem.partials._table', compact('kinhNghiem'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $kinhNghiem])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true,
            ]);
        }

        return view('admin.qlybluanknghiem.list', compact('kinhNghiem'));
    }

    public function comments(Request $request, $postId)
{
    $kinhNghiem = Posts::findOrFail($postId);

    $pageSize = $request->input('page_size', 100);

    $query = $kinhNghiem->comments()->orderByDesc('created_at');

    // Lọc theo ngày
    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->input('from_date'));
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->input('to_date'));
    }

    $comments = $query->paginate($pageSize)->appends($request->all());

    if ($request->ajax()) {
        $table = view('admin.qlybluanknghiem.partials._tablecomments', compact('comments', 'kinhNghiem'))->render();
        $pagination = view('admin.component.pagination', ['posts' => $comments])->render();

        return response()->json([
            'table' => $table,
            'pagination' => $pagination,
            'status' => true,
            'isSearch' => true,
            'totalRecords' => $comments->total(),
        ]);
    }

    return view('admin.qlybluanknghiem.partials.comments', compact('kinhNghiem', 'comments'));
}



    public function approve(Comment $comment)
    {
        $comment->approved = true;
        $comment->save();

        return back()->with('success', 'Bình luận đã được duyệt.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Đã xóa bình luận.');
    }

    public function unapprove(Comment $comment)
    {
        $comment->approved = false;
        $comment->save();

        return back()->with('success', 'Bình luận đã được hủy duyệt.');
    }
}

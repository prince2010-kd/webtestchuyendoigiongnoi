<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Hiển thị danh sách log hoạt động.
     */
    public function index(Request $request)
    {
        $query = Activity::with('causer')->latest();

        // Tìm kiếm theo tên người thực hiện (causer.name)
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->whereHas('causer', function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%');
            });
        }

        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $logs = $query->paginate($pageSize)->appends($request->all());

        // Trả về AJAX
        if ($request->ajax()) {
            $table = view('admin.activity_logs.partials._table', compact('logs'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $logs])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination
            ]);
        }

        // Trả về view chính
        return view('admin.activity_logs.list', compact('logs'));
    }

    /**
     * Hiển thị chi tiết một log cụ thể (nếu cần).
     */
    public function show($id)
    {
        $log = Activity::with('causer')->findOrFail($id);
        return view('backend.activity_logs.show', compact('log'));
    }
}

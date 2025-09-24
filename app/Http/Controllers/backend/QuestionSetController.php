<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\QuestionSet;
use Illuminate\Http\Request;
use App\Helpers\SortOrderHelper;

class QuestionSetController extends Controller
{
    public function index(Request $request)
    {
        $query = QuestionSet::query();

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%' . $request->input('keyword') . '%');
        }

        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('stt', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        $pageSize = $request->input('page_size', 10);
        $sets = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            $table = view('admin.questionset.partials._table', compact('sets'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $sets])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.questionset.list', compact('sets'));
    }

    public function create()
    {
        return view('admin.questionset.form', [
            'formAction' => route('admin.questionset.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm bộ đề',
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        QuestionSet::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'stt' => QuestionSet::max('stt') + 1 ?? 1,
            'active' => 1
        ]);

        return redirect()->route('admin.questionset.list')->with('success', 'Thêm bộ đề thành công!');
    }

    public function edit($id)
    {
        $set = QuestionSet::findOrFail($id);

        return view('admin.questionset.form', [
            'formAction' => route('admin.questionset.update', $set->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Cập nhật bộ đề',
            'fields' => $set->only(['title', 'description', 'duration', 'stt', 'active']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $set = QuestionSet::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $set->update([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'stt' => $request->stt ?? $set->stt,
            'active' => $request->active ?? $set->active
        ]);

        return redirect()->route('admin.questionset.list')->with('success', 'Cập nhật bộ đề thành công!');
    }

    public function destroy($id)
    {
        $set = QuestionSet::findOrFail($id);
        $set->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa bộ đề']);
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        $item = QuestionSet::findOrFail($id);
        $item->active = $request->input('active') ? 1 : 0;
        $item->save();

        return response()->json(['message' => $item->active ? 'Đã bật hiển thị' : 'Đã tắt hiển thị']);
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(QuestionSet::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        QuestionSet::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
}

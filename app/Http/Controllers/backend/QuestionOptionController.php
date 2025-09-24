<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use App\Helpers\SortOrderHelper;
class QuestionOptionController extends Controller
{
    public function index(Request $request)
    {
        $query = QuestionOption::with('question');

        // Tìm kiếm theo nội dung câu hỏi liên kết
        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->whereHas('question', function ($q) use ($keyword) {
                $q->where('content', 'like', '%' . $keyword . '%');
            });
        }

        // Sắp xếp theo STT
        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('stt', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $questionOptions = $query->paginate($pageSize)->appends($request->all());

        // Nếu là Ajax (load động)
        if ($request->ajax()) {
            $table = view('admin.questionoptions.partials._table', compact('questionOptions'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $questionOptions])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.questionoptions.list', compact('questionOptions'));
    }

    public function create()
    {
        return view('admin.questionoptions.form', [
            'formAction' => route('admin.questionoption.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm đáp án',
            'formTitle' => 'Thêm đáp án mới',
            'questions' => Question::where('active', true)->orderBy('stt')->get(),
            'fields' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'label' => 'required|string|max:1',
            'text' => 'required|string',
        ]);

        $maxStt = QuestionOption::where('question_id', $request->question_id)->max('stt');
        $nextStt = $maxStt ? $maxStt + 1 : 1;

        QuestionOption::create([
            'question_id' => $request->question_id,
            'label' => strtoupper($request->label),
            'text' => $request->text,
            'stt' => $nextStt,
            'active' => 1,
        ]);

        return redirect()->route('admin.questionoption.list')->with('success', 'Thêm đáp án thành công!');
    }

    public function edit($id)
    {
        $option = QuestionOption::findOrFail($id);

        return view('admin.questionoptions.form', [
            'formAction' => route('admin.questionoption.update', $option->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật đáp án',
            'formTitle' => 'Cập nhật đáp án',
            'questions' => Question::where('active', true)->orderBy('stt')->get(),
            'fields' => $option->toArray(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $option = QuestionOption::findOrFail($id);

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'label' => 'required|string|max:1',
            'text' => 'required|string',
        ]);

        $option->update([
            'question_id' => $request->question_id,
            'label' => strtoupper($request->label),
            'text' => $request->text,
        ]);

        return redirect()->route('admin.questionoption.list')->with('success', 'Cập nhật đáp án thành công!');
    }

    public function destroy($id)
    {
        $option = QuestionOption::findOrFail($id);
        $option->delete();

        return response()->json(['success' => true, 'message' => 'Xóa đáp án thành công']);
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        $item = QuestionOption::findOrFail($id);
        $item->active = $request->input('active') ? 1 : 0;
        $item->save();

        return response()->json(['message' => $item->active ? 'Đã bật hiển thị' : 'Đã tắt hiển thị']);
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(QuestionOption::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        QuestionOption::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }
    
}

<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionSet;
use Illuminate\Http\Request;
use App\Helpers\SortOrderHelper;
class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::query();
        $query = Question::with('questionSet');

        if ($request->filled('keyword')) {
            $query->where('content', 'like', '%' . $request->input('keyword') . '%');
        }

        if ($request->filled('sortBy')) {
            $sort = $request->input('sortBy') === 'desc' ? 'desc' : 'asc';
            $query->orderBy('stt', $sort);
        } else {
            $query->orderBy('stt', 'asc');
        }

        $pageSize = $request->input('page_size', 10);
        $questions = $query->paginate($pageSize)->appends($request->all());

        if ($request->ajax()) {
            $table = view('admin.question.partials._table', compact('questions'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $questions])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true
            ]);
        }

        return view('admin.question.list', compact('questions'));
    }

    public function create()
    {
        $questionSets = QuestionSet::orderBy('stt')->get();

        return view('admin.question.form', [
            'formAction' => route('admin.question.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm câu hỏi',
            'fields' => [],
            'questionSets' => $questionSets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'type' => 'required|in:multiple_choice,short_answer',
            'cau' => 'required|integer|min:1',
        ]);

        $data = [
            'content' => $request->input('content'),
            'type' => $request->input('type'),
            'stt' => Question::max('stt') + 1 ?? 1,
            'active' => 1,
            'question_set_id' => $request->input('question_set_id'),
            'cau' => $request->input('cau')
        ];

        if ($request->type === 'multiple_choice') {
            $request->validate([
                'options' => 'required|array|min:2',
                'correct_answer' => 'required|string|in:A,B,C,D'
            ]);

            // Lưu tạm đáp án đúng vào bảng questions
            $data['correct_answer'] = $request->input('correct_answer');

            // Tạo câu hỏi
            $question = Question::create($data);

            // Lưu các lựa chọn vào bảng question_options
            foreach ($request->input('options') as $label => $text) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'label' => $label,
                    'text' => $text
                ]);
            }
        } elseif ($request->type === 'short_answer') {
            $request->validate([
                'gaps' => 'required|array|min:1',
            ]);

            $data['correct_answer'] = json_encode(array_values($request->input('gaps')));

            // Tạo câu hỏi
            $question = Question::create($data);
        }

        return redirect()->route('admin.question.list')->with('success', 'Thêm câu hỏi thành công!');
    }


    public function edit($id)
    {
        $question = Question::with('options')->findOrFail($id);
        $questionSets = QuestionSet::orderBy('stt')->get();

        $options = $question->options->pluck('text', 'label')->toArray();

        // Thêm xử lý cho câu hỏi điền từ
        $gaps = [];
        if ($question->type === 'short_answer') {
            $decoded = json_decode($question->correct_answer, true);
            if (is_array($decoded)) {
                foreach ($decoded as $i => $val) {
                    $gaps[$i + 1] = $val; // key bắt đầu từ 1
                }
            }
        }

        return view('admin.question.form', [
            'formAction' => route('admin.question.update', $question->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Cập nhật câu hỏi',
            'fields' => array_merge(
                $question->only(['content', 'correct_answer', 'explanation', 'stt', 'active', 'question_set_id', 'type']),
                ['options' => $options, 'gaps' => $gaps]
            ),
            'questionSets' => $questionSets
        ]);
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $baseRules = [
            'content' => 'required|string',
            'explanation' => 'nullable|string',
            'question_set_id' => 'required|exists:question_sets,id',
            'type' => 'required|in:multiple_choice,short_answer',
            'cau' => 'required|integer|min:1',
        ];

        if ($request->type === 'multiple_choice') {
            $baseRules['correct_answer'] = 'required|string|in:A,B,C,D';
        } elseif ($request->type === 'short_answer') {
            $baseRules['gaps'] = 'required|array|min:1';
        }

        $request->validate($baseRules);

        // Cập nhật dữ liệu chung
        $question->content = $request->content;
        $question->explanation = $request->explanation;
        $question->question_set_id = $request->question_set_id;
        $question->cau = $request->cau;

        // Cập nhật đúng loại
        if ($request->type === 'multiple_choice') {
            $question->correct_answer = strtoupper($request->correct_answer);
        } elseif ($request->type === 'short_answer') {
            $question->correct_answer = json_encode(array_values($request->gaps));
        }

        $question->save();

        return redirect()->route('admin.question.list')->with('success', 'Cập nhật thành công!');
    }


    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa câu hỏi']);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ'], 422);
        }

        Question::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Xóa thành công']);
    }

    public function thaydoitrangthai(Request $request, $id)
    {
        $item = Question::findOrFail($id);
        $item->active = $request->input('active') ? 1 : 0;
        $item->save();

        return response()->json(['message' => $item->active ? 'Đã bật hiển thị' : 'Đã tắt hiển thị']);
    }

    public function updateStt(Request $request, $id)
    {
        $request->validate([
            'stt' => 'required|integer|min:1',
        ]);

        $result = SortOrderHelper::updateStt(Question::class, $id, $request->stt);

        return response()->json(['message' => $result['message']]);
    }
}

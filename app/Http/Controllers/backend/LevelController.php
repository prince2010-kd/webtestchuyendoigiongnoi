<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LevelController extends Controller
{
    public function index(Request $request)
    {
        $level = Level::orderBy('stt', 'asc')->paginate(10);
        if ($request->ajax()) {
            $table = view('admin.level.list', compact('level'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $level])->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'level' => $level
            ]);
        }
        return view('admin.level.list', compact('level'));
    }

    public function create()
    {
        return view('admin.level.form', [
            'formAction' => route('admin.level.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới trình độ'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'title' => 'required|string',
            'des' => 'nullable|string',
        ]);

        $title = $request->input('title');
        $maxStt = Level::max('stt') ?? 0;
        $stt = $maxStt + 1;

        // Level::create($data);
        Level::create([
            'title' => $title,
            'code' => $request->input('code', ''),
            'des' => $request->input('des'),
            'stt' => $stt,
            'active' => 1,
        ]);
        // return redirect()->route('level.index')->with('success', 'Level created successfully');
        return response()->json([
            'message' => 'Thêm mới thành công',
            'success' => true,
            'redirect' => route('admin.level.list')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $level = Level::findOrFail($id);
        $formTitle = 'Xem chi tiết trình độ';
        $formAction = '#';
        $submitButton = 'Lưu';
        $formMethod = null;
        $readonly = true;

        return view('admin.level.form', compact('level', 'formTitle', 'formAction', 'submitButton', 'formMethod', 'readonly'));
    }

    public function edit(string $id)
    {
        // return view('level.edit', compact('level'));
        $level = Level::findOrFail($id);

        return view('admin.level.form', [
            'formAction' => route('admin.level.update', $level->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa trình độ',
            'level' => $level,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $level = Level::findOrFail($id);

        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('level', 'title')->ignore($level->id),
            ],
            'code' => 'required|string',
            'title' => 'required|string',
            'des' => 'nullable|string'
        ]);
        $title = $request->input('title');
        $level->update([
            'title' => $title,
            'code' => $request->input('code'),
            'des' => $request->input('des'),
        ]);

        // return redirect()->route('admin.category.list')->with('success', 'Cập nhật thành công!');
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'success' => true,
            'redirect' => route('admin.level.list')
        ]);

    }

    public function updatestatus(Request $request, string $id)
    {
        $level = Level::findOrFail($id);
        $level->active = $request->input('active'); // Lấy giá trị từ body
        $level->save();

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'redirect' => route('admin.level.list')
        ]);
    }

    public function destroy(string $id)
    {
        $level = Level::findOrFail($id);
        $level->delete();

        return redirect()->route('admin.level.list')->with('success', 'Xóa thành công!');
    }
}

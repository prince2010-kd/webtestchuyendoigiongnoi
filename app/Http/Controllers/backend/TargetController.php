<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TargetController extends Controller
{
    public function index(Request $request)
    {
        $target = Target::with('levels')
            ->whereNull('deleted_at')
            ->orderBy('stt', 'asc')
            ->paginate(10);

        if ($request->ajax()) {
            $table = view('admin.target.list', compact('target'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $target])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'target' => $target
            ]);
        }

        return view('admin.target.list', compact('target'));
    }

    public function create()
    {
        $levels = Level::where('active', 1)->get();
        return view('admin.target.form', [
            'formAction' => route('admin.target.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới mục tiêu',
            'levels' => $levels
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'title' => 'required|string',
            'price' => 'required|numeric'
        ]);

        $title = $request->input('title');
        $maxStt = Target::max('stt') ?? 0;
        $stt = $maxStt + 1;
        // Tạo Target trước
        $target = Target::create([
            'title' => $title,
            'code' => $request->input('code', ''),
            'price' => $request->input('price', ''),
            'des' => $request->input('des'),
            'stt' => $stt,
            'active' => 1,
        ]);
        $target->levels()->sync($request->input('levels', []));

        return response()->json([
            'message' => 'Thêm mới thành công',
            'success' => true,
            'redirect' => route('admin.target.list')
        ]);
    }

    public function edit(string $id)
    {
        $target = Target::findOrFail($id);
        $levels = Level::where('active', 1)->get();
        $targetLevels = $target->levels()->pluck('levels.id')->toArray(); // Giả sử có quan hệ levels()

        return view('admin.target.form', [
            'formAction' => route('admin.target.update', $target->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa mục tiêu',
            'target' => $target,
            'levels' => $levels,
            'targetLevels' => $targetLevels
        ]);
    }

    public function update(Request $request, string $id)
    {
        $target = Target::findOrFail($id);
        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('targets', 'title')->ignore($target->id),
            ],
            'code' => 'required|string',
            'price' => 'required|numeric',
            'des' => 'nullable|string',
            'levels' => 'array' // optional: nếu có nhiều trình độ
        ]);

        $title = $request->input('title');
        $target->update([
            'title' => $title,
            'code' => $request->input('code'),
            'des' => $request->input('des'),
            'price' => $request->input('price'),
        ]);
        $target->levels()->sync($request->input('levels', []));

        return response()->json([
            'message' => 'Cập nhật thành công!',
            'success' => true,
            'redirect' => route('admin.target.list')
        ]);
    }

    public function updatestatus(Request $request, string $id)
    {
        $target = Target::findOrFail($id);
        $target->active = $request->input('active'); // Lấy giá trị từ body
        $target->save();

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'redirect' => route('admin.target.list')
        ]);
    }

    public function destroy(string $id)
    {
        $target = Target::findOrFail($id);
        $target->delete();

        return redirect()->route('admin.target.list')->with('success', 'Xóa thành công!');
    }
}

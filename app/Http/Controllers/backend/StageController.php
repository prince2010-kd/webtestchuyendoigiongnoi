<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StageController extends Controller
{
    public function index(Request $request)
    {
        $stage = Stage::with('targets')
            ->whereNull('deleted_at')
            ->orderBy('stt', 'asc')
            ->paginate(10);

        if ($request->ajax()) {
            $table = view('admin.stage.list', compact('stage'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $stage])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'stage' => $stage
            ]);
        }
        
        return view('admin.stage.list', compact('stage'));

    }

    public function create()
    {
        $targets = Target::where('active', 1)->get();
        return view('admin.stage.form', [
            'formAction' => route('admin.stage.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới chặng',
            'targets' => $targets
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'title' => 'required|string',
            'des' => 'nullable|string'
        ]);
        $title = $request->input('title');
        $maxStt = Stage::max('stt') ?? 0;
        $stt = $maxStt + 1;
        // Tạo Target trước
        $stage = Stage::create([
            'title' => $title,
            'code' => $request->input('code', ''),
            'des' => $request->input('des'),
            'stt' => $stt,
            'active' => 1,
        ]);
        $stage->targets()->sync($request->input('targets', []));

        return response()->json([
            'message' => 'Thêm mới thành công',
            'success' => true,
            'redirect' => route('admin.stage.list')
        ]);
    }

    public function edit(string $id)
    {
        $stage = Stage::findOrFail($id);
        $targets = Target::where('active', 1)->get();
        $stageTargets = $stage->targets()->pluck('targets.id')->toArray(); 

        return view('admin.stage.form', [
            'formAction' => route('admin.stage.update', $stage->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa mục tiêu',
            'stage' => $stage,
            'targets' => $targets,
            'stageTargets' => $stageTargets
        ]);
    }

    public function update(Request $request, string $id)
    {
        $stage = Stage::findOrFail($id);
        $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stages', 'title')->ignore($stage->id),
            ],
            'code' => 'required|string',
            'des' => 'nullable|string',
            'targets' => 'array' // optional: nếu có nhiều trình độ
        ]);
        $title = $request->input('title');

        $stage->update([
            'title' => $title,
            'code' => $request->input('code'),
            'des' => $request->input('des')
        ]);
        
        $stage->targets()->sync($request->input('targets', []));
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'success' => true,
            'redirect' => route('admin.stage.list')
        ]);
    }

    public function updatestatus(Request $request, string $id)
    {
        $stage = Stage::findOrFail($id);
        $stage->active = $request->input('active'); // Lấy giá trị từ body
        $stage->save();

        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'redirect' => route('admin.stage.list')
        ]);
    }

    public function destroy(string $id)
    {
        $stage = Stage::findOrFail($id);
        $stage->delete();

        return redirect()->route('admin.stage.list')->with('success', 'Xóa thành công!');
    }
}

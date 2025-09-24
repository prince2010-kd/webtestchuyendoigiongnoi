<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\General;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class GeneralController extends Controller
{
    public function index()
    {
        // $configmails = General::all();
        $configmails = General::where('group_conf', 'cfmails')->paginate(10);
        foreach ($configmails as $item) {
            $val = json_decode($item->val, true);

            // Gắn thêm các thuộc tính để sử dụng trong view
            $item->labelemail = $val['labelemail'] ?? '';
            $item->content = $val['content'] ?? '';
            $item->active = $val['active'] ?? '';
            $item->contentchange = $val['contentchange'] ?? '';
        }
        return view('admin.configmails.list', compact('configmails'));
    }

    public function create()
    {
        return view('admin.configmails.form', [
            'formAction' => route('admin.configmails.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới cấu hình',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
            'val' => 'nullable|string'
        ]);

        // Tìm stt lớn nhất hiện tại
        $maxStt = General::where('group_conf', 'cfmails')->max('stt') ?? 0;
        $stt = $maxStt + 1;

        // Kiểm tra xem keyword đã tồn tại chưa
        $keyword = $request->input('keyword');
        $existingConfig = General::where('keyword', $keyword)
            ->where('group_conf', 'cfmails')
            ->first();
        if ($existingConfig) {
            return response()->json([
                'message' => 'Keyword đã tồn tại trong cấu hình email.',
                'success' => false
            ], 400);
        }
        $label = $request->input('label');

        // Tạo mới cấu hình email
        $val = [
            'label' => $request->input('label'),
            'labelemail' => $request->input('labelemail'),
            'contentchange' => $request->input('contentchange'),
            'content' => $request->input('content'),
            'active' => 1
        ];
        $data = [
            'keyword' => $keyword,
            'label' => $label,
            'val' => json_encode($val),
            'type' => 'text',
            'group_conf' => 'cfmails',
            'public' => 1,
            'stt' => $stt,
        ];

        General::create($data);

        // // return redirect()->route('admin.configmails.list')->with('success', 'Thêm mới thành công!');
        return response()->json([
            'message' => 'Thêm mới thành công',
            'logData' => json_encode($data), // hoặc logData => json_encode(...)
            'success' => true,
            'redirect' => route('admin.configmails.list')
        ]);
    }

    public function show(string $id)
    {
        $configmails = General::findOrFail($id);
        $formTitle = 'Xem chi tiết';
        $formAction = '#';
        $submitButton = 'Lưu';
        $formMethod = null;
        $readonly = true;

        return view('admin.configmails.form', compact('configmails', 'formTitle', 'formAction', 'submitButton', 'formMethod', 'readonly'));
    }

    public function update(Request $request, string $id)
    {
        $general = General::findOrFail($id);

        $request->validate([
            'keyword' => 'required|string|max:255',
            'label' => 'nullable|string|max:255',
        ]);
        // Kiểm tra xem keyword đã tồn tại chưa
        $keyword = $request->input('keyword');
        // $existingConfig = General::where('keyword', $keyword)
        //     ->where('group_conf', 'cfmails')
        //     ->first();
        // if ($existingConfig) {
        //     return response()->json([
        //         'message' => 'Keyword đã tồn tại trong cấu hình email.',
        //         'success' => false
        //     ], 400);
        // }
        $label = $request->input('label');

        // Tạo mới cấu hình email
        $val = [
            'label' => $request->input('label'),
            'labelemail' => $request->input('labelemail'),
            'contentchange' => $request->input('contentchange'),
            'content' => $request->input('content'),
            'active' => 1
        ];
        $data = [
            'keyword' => $keyword,
            'label' => $label,
            'val' => json_encode($val)
        ];

        $general->update($data);

        // return redirect()->route('admin.configmails.list')->with('success', 'Cập nhật thành công!');
        return response()->json([
            'message' => 'Cập nhật thành công!',
            'success' => true,
            'redirect' => route('admin.configmails.list')
        ]);
    }

    public function updatestatus(Request $request, string $id)
    {
        $general = General::findOrFail($id);
        $val = json_decode($general->val, true); // true để thành mảng
        $val['active'] = (int) $request->input('active');
        $general->val = json_encode($val, JSON_UNESCAPED_UNICODE);
        $general->save();

        // return redirect()->route('admin.smtp_settings.list')
        //     ->with('success', 'Cập nhật trạng thái thành công!');
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'success' => true,
            'redirect' => route('admin.configmails.list')
        ]);
    }

    public function destroy($id)
    {
        $general = General::findOrFail($id);
        $general->delete();

        return redirect()->route('admin.configmails.list')->with('success', 'Xóa thành công!');
    }

    public function edit(string $id)
    {
        $configmails = General::findOrFail($id);
        $configmails->decoded = json_decode($configmails->val, true);
        return view('admin.configmails.form', [
            'formAction' => route('admin.configmails.update', $configmails->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa cấu hình',
            'configmails' => $configmails,
        ]);
    }
}

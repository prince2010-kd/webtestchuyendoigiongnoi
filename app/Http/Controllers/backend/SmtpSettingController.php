<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SmtpSettingController extends Controller
{
    public function index(Request $request)
    {

        $settings = SmtpSetting::latest()->paginate(10);
        if ($request->ajax()) {
            $table = view('admin.smtp_settings.list', compact('settings'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $settings])->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
            ]);
        }

        return view('admin.smtp_settings.list', compact('settings'));
    }

    public function create()
    {
        return view('admin.smtp_settings.form', [
            'formAction' => route('admin.smtp_settings.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'formTitle' => 'Thêm mới mail server SMTP',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'hostname' => 'required',
            'port' => 'required|integer',
            'secure' => 'nullable|string'
        ]);

        $maxStt = SmtpSetting::max('stt') ?? 0;
        $stt = $maxStt + 1;

        // SmtpSetting::create($request->all());
        SmtpSetting::create([
            'username' => $request->input('username', ''),
            'password' => $request->input('password'),
            'hostname' => $request->input('hostname'),
            'port' => $request->input('port'),
            'secure' => $request->input('secure'),
            'stt' => $stt,
            'active' => 1
        ]);

        // return redirect()->route('admin.smtp_settings.list')->with('success', 'Cấu hình SMTP đã được thêm.');
        return response()->json([
            'message' => 'Cấu hình SMTP đã được thêm.',
            'success' => true,
            'redirect' => route('admin.smtp_settings.list')
        ]);
    }

    public function edit(string $id)
    {
        // return view('admin.smtp_settings.edit', compact('smtpSetting'));
        $settings = SmtpSetting::findOrFail($id);

        return view('admin.smtp_settings.form', [
            'formAction' => route('admin.smtp_settings.update', $settings->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'formTitle' => 'Chỉnh sửa cấu hình SMTP',
            'smtp_settings' => $settings,
        ]);
    }

    public function update(Request $request, $id)
    {
        $smtpSetting = SmtpSetting::findOrFail($id);

        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'hostname' => 'required',
            'port' => 'required|integer',
            'secure' => 'nullable|string',
            'active' => 'nullable|boolean',
        ]);

        $smtpSetting->update($request->all());

        // return redirect()->route('admin.smtp_settings.list')->with('success', 'Đã cập nhật cấu hình SMTP.');
        return response()->json([
            'message' => 'Đã cập nhật cấu hình SMTP!',
            'success' => true,
            'redirect' => route('admin.smtp_settings.list')
        ]);
    }

    public function updatestatus(Request $request, string $id)
    {
        $smtpSetting = SmtpSetting::findOrFail($id);
        $smtpSetting->active = $request->input('active'); // Lấy giá trị từ body
        $smtpSetting->save();

        // return redirect()->route('admin.smtp_settings.list')
        //     ->with('success', 'Cập nhật trạng thái thành công!');
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công!',
            'redirect' => route('admin.smtp_settings.list')
        ]);
    }

    public function destroy(string $id)
    {
        $smtpSetting = SmtpSetting::findOrFail($id);
        $smtpSetting->delete();

        return redirect()->route('admin.smtp_settings.list')->with('success', 'Xóa thành công!');
    }

    public function show(string $id)
    {
        $category = SmtpSetting::findOrFail($id);
        $formTitle = 'Xem chi';
        $formAction = '#';
        $submitButton = 'Lưu';
        $formMethod = null;
        $readonly = true;

        return view('admin.smtp_settings.form', compact('smtpSetting', 'formTitle', 'formAction', 'submitButton', 'formMethod', 'readonly'));
    }
}

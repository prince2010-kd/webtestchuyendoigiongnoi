<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General;

class CauHinhChungController extends Controller
{
    // Hàm hiển thị form với dữ liệu đã có
    public function edit()
    {
        // Lấy toàn bộ cấu hình nhóm 'site'
        $configs = General::where('group_conf', 'general')->get();

        // Chuyển thành mảng [keyword => val]
        $fields = $configs->pluck('val', 'keyword')->toArray();

        return view('admin.cauhinhchung.form', compact('fields'));
    }

    // Hàm lưu cấu hình
    public function store(Request $request)
    {
        $data = $request->input('fields', []);

        // Xử lý file logo riêng (vì input file không nằm trong fields)
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $path = $request->file('logo')->store('logos', 'public');
            $logoValue = '/storage/' . $path;

            General::updateOrCreate(
                ['keyword' => 'logo', 'group_conf' => 'general'],
                [
                    'val' => $logoValue,
                    'label' => 'Logo URL',
                    'created' => now(),
                    'stt' => General::max('stt') + 1,
                    'public' => 1,
                    'type' => 'image',
                    'group_conf' => 'general',
                ]
            );
        }

        // Lưu hoặc cập nhật các trường còn lại
        foreach ($data as $key => $value) {
            General::updateOrCreate(
                ['keyword' => $key, 'group_conf' => 'general'],
                [
                    'val' => $value,
                    'label' => ucfirst(str_replace('_', ' ', $key)),
                    'created' => now(),
                    'stt' => General::max('stt') + 1,
                    'public' => 1,
                    'type' => ($key == 'gioithieu') ? 'textarea' : 'text',
                    'group_conf' => 'general',
                ]
            );
        }

        return redirect()->back()->with('success', 'Đã lưu cấu hình thành công.');
    }
}

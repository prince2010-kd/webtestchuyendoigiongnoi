<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\KhoaHoc;
use App\Models\LoaiKhoaHoc;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class KhoaHocController extends Controller
{
     public function index(Request $request)
    {
        $khoahoc = KhoaHoc::latest()->paginate(10);

        if ($request->ajax()) {
            $table = view('admin.khoahoc.list', compact('khoahoc'))->render();
            $pagination = view('admin.component.pagination', compact('khoahoc'))->render();
            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
            ]);
        }

        return view('admin.khoahoc.list', compact('khoahoc'));
    }

    public function create()
    {
         $dsLoai  = LoaiKhoaHoc::orderBy('ten')->get();
        return view('admin.khoahoc.form', [
            'dsLoai' => $dsLoai ,
            'formTitle' => 'Thêm khóa học',
            'formAction' => route('admin.khoahoc.store'),
            'formMethod' => 'POST',
            'submitButton' => 'Thêm mới',
            'fields' => []
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:khoa_hocs,slug',
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image',
            'loai' => 'nullable|string',
            'type' => 'nullable|string',
            'active' => 'boolean',
            'stt' => 'nullable|integer',
        ]);

        if (!$data['slug']) {
            $data['slug'] = Str::slug($data['tieu_de']);
        }

        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('uploads/khoahoc', 'public');
            $data['hinh_anh'] = $path;
        }

        KhoaHoc::create($data);
        return redirect()->route('khoa-hoc.index')->with('success', 'Thêm khóa học thành công');
    }

    public function edit(KhoaHoc $khoaHoc)
    {
        return view('admin.khoahoc.form', [
            'formTitle' => 'Chỉnh sửa khóa học',
            'formAction' => route('khoa-hoc.update', $khoaHoc->id),
            'formMethod' => 'PUT',
            'submitButton' => 'Cập nhật',
            'fields' => [
                'tieu_de' => $khoaHoc->tieu_de,
                'slug' => $khoaHoc->slug,
                'mo_ta' => $khoaHoc->mo_ta,
                'loai' => $khoaHoc->loai,
                'type' => $khoaHoc->type,
                'active' => $khoaHoc->active,
                'stt' => $khoaHoc->stt,
                'hinh_anh' => $khoaHoc->hinh_anh,
            ]
        ]);
    }

    public function update(Request $request, KhoaHoc $khoaHoc)
    {
        $data = $request->validate([
            'tieu_de' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:khoa_hocs,slug,' . $khoaHoc->id,
            'mo_ta' => 'nullable|string',
            'hinh_anh' => 'nullable|image',
            'loai' => 'nullable|string',
            'type' => 'nullable|string',
            'active' => 'boolean',
            'stt' => 'nullable|integer',
        ]);

        if (!$data['slug']) {
            $data['slug'] = Str::slug($data['tieu_de']);
        }

        if ($request->hasFile('hinh_anh')) {
            $path = $request->file('hinh_anh')->store('uploads/khoahoc', 'public');
            $data['hinh_anh'] = $path;
        }

        $khoaHoc->update($data);
        return redirect()->route('khoa-hoc.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(KhoaHoc $khoaHoc)
    {
        $khoaHoc->delete();
        return redirect()->route('khoa-hoc.index')->with('success', 'Xóa thành công');
    }
}

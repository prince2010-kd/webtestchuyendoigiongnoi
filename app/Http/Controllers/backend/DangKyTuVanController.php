<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DangKyTuVanForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class DangKyTuVanController extends Controller
{
    public function index(Request $request)
    {
        $query = DangKyTuVanForm::query();

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // Lọc theo ngày kết thúc
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        // Số bản ghi mỗi trang
        $pageSize = $request->input('page_size', 10);
        $dangkytuvan = $query->paginate($pageSize)->appends($request->all());
        $totalRecords = $dangkytuvan->total();
        // Nếu là AJAX thì trả về HTML table + pagination
        if ($request->ajax()) {
            $table = view('admin.dangkytuvan.partials._table', compact('dangkytuvan'))->render();
            $pagination = view('admin.component.pagination', ['posts' => $dangkytuvan])->render();

            return response()->json([
                'table' => $table,
                'pagination' => $pagination,
                'status' => true,
                'totalRecords' => $dangkytuvan->total(),
            ]);
        }

        return view('admin.dangkytuvan.list', compact('dangkytuvan', 'totalRecords'));
    }

    public function store(Request $request)
    {
        DangKyTuVanForm::create([
            'hoten' => $request->hoten,
            'tuoi' => $request->tuoi,
            'sdt' => $request->sdt,
            'email' => $request->email,
            'khuvuc' => $request->khuvuc,
        ]);

        // Gửi lên Google Sheet qua Apps Script
        $googleScriptUrl = 'https://script.google.com/macros/s/AKfycbw13iBGzGIDDrLkMc0DbOizJbFNFyfL3IABj5SnEmc48mXDymvRMNEYME_V6WYRmO2NRg/exec';

        Http::asForm()->post($googleScriptUrl, [
            'hoten' => $request->hoten,
            'tuoi' => $request->tuoi,
            'sdt' => $request->sdt,
            'email' => $request->email,
            'khuvuc' => $request->khuvuc,
        ]);

        return response()->json(['status' => 'ok']);
    }

}

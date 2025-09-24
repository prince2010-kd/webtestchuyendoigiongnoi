<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class RedisDemoController extends Controller
{
    // Lưu dữ liệu vào Redis
    public function setData()
    {
        Redis::set('website', 'Laravel Redis Tutorial');
        return response()->json(['message' => 'Đã lưu Redis thành công!']);
    }

    // Lấy dữ liệu từ Redis
    public function getData()
    {
        $data = Redis::get('website');

        if (!$data) {
            return response()->json(['message' => 'Không có dữ liệu!'], 404);
        }

        return response()->json(['data' => $data]);
    }

    // Xóa dữ liệu Redis
    public function deleteData()
    {
        Redis::del('website');
        return response()->json(['message' => 'Đã xóa Redis!']);
    }
}

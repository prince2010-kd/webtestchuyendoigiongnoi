<?php

namespace App\Http\Controllers\api\member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\Member;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;


class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|unique:members,name|max:255',
                'fullName' => 'required|string|max:255',
                'email' => 'required|email|unique:members,email',
                'phone' => 'nullable|string',
                'password' => ['required', 'confirmed', Password::min(6)],
            ]);

            $member = Member::create([
                'name' => $validated['name'],
                'full_name' => $validated['fullName'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'end_password' => $validated['password'],
                'active' => 1,
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Đăng ký thành công',
                // 'user'    => $member
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('name', 'password');

        if (!$token = Auth::guard('member-api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }
        // $cookie = cookie('token', $token, 60, '/', null, false, true);

        return response()->json(['token' => $token]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

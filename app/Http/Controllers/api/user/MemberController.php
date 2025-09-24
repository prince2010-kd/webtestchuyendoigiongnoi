<?php

namespace App\Http\Controllers\api\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'permission_group' => 'required|in:1,2,3',
            'is_admin' => 'boolean',
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'permission_group' => $validated['permission_group'],
            'is_admin' => $validated['is_admin'] ?? false,
            'password' => Hash::make($validated['password']),
        ]);
    }
}
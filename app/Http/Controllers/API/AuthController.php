<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'phone'      => 'required|string|regex:/^09[0-9]{9}$/|unique:users,phone',
            'password'   => 'required|string|confirmed|min:6',
        ]);

        User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'phone'      => $data['phone'],
            'password'   => Hash::make($data['password']),
            'active'     => false, // منتظر تایید ادمین
        ]);

        return response()->json(['message' => 'ثبت‌نام انجام شد. منتظر تایید ادمین باشید.'], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $data['phone'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['phone' => ['شماره یا رمز اشتباه است.']]);
        }

        if (! $user->active) {
            return response()->json(['message' => 'حساب شما هنوز توسط ادمین تایید نشده است.'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'خروج انجام شد']);
    }
}

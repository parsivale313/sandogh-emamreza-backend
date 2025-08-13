<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Kavenegar\KavenegarApi;

class AuthController extends Controller
{
    // ثبت نام کاربر (غیرفعال تا تایید ادمین)
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users|regex:/^09[0-9]{9}$/',
            'password' => 'required|string|confirmed|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'is_active' => false,
            'role' => 'user',
        ]);

        return response()->json([
            'message' => 'ثبت‌نام انجام شد. منتظر تایید ادمین باشید.'
        ], 201);
    }

    // ورود با شماره تلفن
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^09[0-9]{9}$/',
            'password' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'phone' => ['شماره یا رمز عبور اشتباه است.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'message' => 'حساب شما هنوز توسط ادمین تایید نشده است.'
            ], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // خروج
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'خروج انجام شد']);
    }

    // تایید کاربر توسط ادمین و ارسال پیامک
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = true;
        $user->save();

        // ارسال پیامک (نمونه با کاوه‌نگار)
        try {
            $api = new KavenegarApi(env('KAVENEGAR_API_KEY'));
            $message = "کاربر گرامی، حساب شما فعال شد.\nنام کاربری: {$user->phone}\nرمز عبور: (همان رمز ثبت‌نام)";
            $receptor = $user->phone;
            $api->Send(env('KAVENEGAR_SENDER'), $receptor, $message);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'کاربر تایید شد ولی ارسال پیامک با مشکل مواجه شد.',
                'error' => $e->getMessage()
            ], 200);
        }

        return response()->json(['message' => 'کاربر تایید و پیامک ارسال شد.']);
    }
}

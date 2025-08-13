<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = true;
        $user->save();

        // ارسال پیامک
        $this->sendSms($user->phone, "حساب شما فعال شد.");

        return response()->json(['message' => 'کاربر با موفقیت تأیید شد.']);
    }

    protected function sendSms($phone, $message)
    {
        $api = new \Kavenegar\KavenegarApi(env('KAVENEGAR_API_KEY'));
        $api->Send(env('SMS_SENDER'), $phone, $message);
    }

}

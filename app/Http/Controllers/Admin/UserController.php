<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // مشاهده همه کاربران منتظر تایید
    public function pending()
    {
        $users = User::where('is_active', false)->get();
        return response()->json($users);
    }

    // تایید کاربر
    public function approveUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->active) {
            return response()->json(['message' => 'کاربر قبلاً تایید شده است.'], 400);
        }

        $user->update([
            'active' => true,
            'approved_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'کاربر تایید شد.']);
    }
}

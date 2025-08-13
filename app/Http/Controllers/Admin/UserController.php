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
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = true;
        $user->save();

        return response()->json(['message' => 'کاربر تایید شد']);
    }
}

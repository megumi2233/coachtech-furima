<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // ▼▼▼ 修正ポイント 1：強制的にメールを送る命令を追加！ ▼▼▼
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        // ▼▼▼ 修正ポイント 2：行き先を「認証画面」に変更！（設計書FN006対応） ▼▼▼
        // return redirect('/mypage/profile'); // 👈 元のコード（削除）
        return redirect('/email/verify');      // 👈 新しいコード！
    }
}

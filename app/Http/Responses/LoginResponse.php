<?php

namespace App\Http\Responses;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $sessionId = $request->session()->getId();
        Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => Auth::id()]);

        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/');
    }
}
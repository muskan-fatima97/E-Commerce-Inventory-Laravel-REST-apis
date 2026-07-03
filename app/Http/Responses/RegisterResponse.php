<?php

namespace App\Http\Responses;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        $sessionId = $request->session()->getId();
        Cart::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->update(['user_id' => Auth::id()]);

        return redirect('/');
    }
}
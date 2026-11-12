<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionUser = Session::get('user');

        if (! $sessionUser) {
            return redirect('login')->with('message-error', 'Please login to continue.');
        }

        // Re-read the account so a stale session copy cannot be used to skip
        // the email verification step.
        $user = User::find($sessionUser->id);

        if (! $user) {
            Session::forget('user');

            return redirect('login')->with('message-error', 'Please login to continue.');
        }

        if (! $user->isVerified()) {
            Session::forget('user');
            Session::put('pending_verification_email', $user->email);

            return redirect('verify-email')
                ->with('email', $user->email)
                ->with('message-error', 'Please verify your email address before continuing.');
        }

        Session::put('user', $user);

        return $next($request);
    }
}

<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ($request->user() && $request->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user() && !$request->user()->company) {
            return redirect()->route('onboarding.index');
        }

        return redirect()->intended(config('fortify.home').'?verified=1');
    }
}

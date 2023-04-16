<?php

namespace App\Http\Controllers\Auth;

use App\Classes\JsonRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    use JsonRequest;

    public function login(LoginRequest $request)
    {
        return $request->authenticate();
    }

    /**
     * Destroy an authenticated session.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        $accessToken = Auth::user()->token();
        $accessToken->revoke();

        return $this->success([
            'status' => true,
            'message' => 'Successful logout',
        ]);
    }
}

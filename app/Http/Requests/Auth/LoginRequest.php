<?php

namespace App\Http\Requests\Auth;

use App\Classes\JsonRequest;
use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    use JsonRequest;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        if (! Auth::guard('admin')->attempt($this->only('email', 'password'))) {
            return $this->invalid([
                'success' => false,
                'message' => 'invalid user credentials.',
            ], 'error');
        }
        config(['auth.guards.api.provider' => 'admin']);
        $admin = Admin::select('admins.*')->find(auth()->guard('admin')->user()->id);
        $token = $admin->createToken('MyApp', ['admin'])->accessToken;

        return $this->success([
            'status' => true,
            'user' => $admin,
            'token' => $token,
            'message' => 'Successful login',
        ], 'credential');
    }
}

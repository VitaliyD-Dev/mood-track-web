<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        Log::info('Login attempt', ['email' => $credentials['email']]);
        
        $result = $this->guard()->attempt($credentials, $request->boolean('remember'));
        
        if ($result) {
            Log::info('Login successful', ['email' => $credentials['email']]);
        } else {
            Log::warning('Login failed', ['email' => $credentials['email']]);
        }
        
        return $result;
    }
} 
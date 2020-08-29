<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function attemptLogin(Request $request)
    {
        // attempt to issue a token to the user based on the login credentials
        $token = $this->guard()->attempt( $this->credentials( $request ) );

        if ( !$token )
        {
            return false;
        }

        // Get the authenticated user
        $user = $this->guard()->user();

        if( $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail() )
        {
            return false;
        }

        // set the user token
        $this->guard()->setToken($token);

        return true;

    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        // get the token from the authentication guard (JWT)
        $token = (string) $this->guard()->getToken();

        // extract the expiry data of the token
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ], 200);

    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();

        if( $user instanceof MustVerifyEmail && !$user->hasVerifiedEmail() )
        {
            return response()->json([
                'errors' => [
                    'verification' => 'You need to verify your email account'
                ]
            ], 422);
        }

        throw ValidationException::withMessages([
            $this->username() => 'Invalid Credentials'
        ]);
    }

    public function logout ()
    {
        $this->guard()->logout();

        return response()->json([
            'message' => 'Logged out successfully!'
        ], 200);
    }
}

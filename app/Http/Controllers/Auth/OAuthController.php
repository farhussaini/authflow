<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OAuth\OAuthClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $provider = $request->query('provider', 'einvoice');
        $oauth = new OAuthClient($provider);

        return redirect($oauth->redirect());
    }

    public function callback(Request $request)
    {
        $provider = $request->query('provider', 'einvoice');
        $oauth = new OAuthClient($provider);

        // $data = $oauth->handleCallback($request->query());
        
        // Login or create user
        // $userInfo = $oauth->getUserInfo($data['access_token']);
        // $user = Auth::loginUsingId($userInfo['id']); 
        
        $user = $oauth->handleCallback($request->query());
        Auth::loginUsingId($user['id']);

        return redirect('/dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
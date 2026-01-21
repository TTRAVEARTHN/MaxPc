<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => 'required|min:6',
        ]);

        // vytvorenie usera, heslo sa ma hashovat cez mutator v modeli User
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // hashed via mutator
            'role'     => 'user',
        ]);

        // automaticke prihlasenie po registracii
        Auth::login($user);
        return redirect()->route('home')->with('success', 'Welcome! Your account has been created.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // vratime sa s chybou ak prihlasenie zlyha
        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Incorrect email or password.',
            ]);
        }

        // regeneracia session ID kvoli bezpecnosti
        $request->session()->regenerate();
        return redirect()->route('home')->with('success', 'You are now logged in!');
    }

    public function logout(Request $request)
    {
        // odhlasenie usera
        Auth::logout();

        // zneplatnime staru session a token proti CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function registration(Request $request) {
        if ($request->isMethod('post')) {
            $credentials = $request->validate(RegistrationRequest::rules());

            $user = new User();
            $user->name = strtolower($credentials['name']);
            $user->email = $user->name . "@example.org";
            $user->password = Hash::make($credentials['password']);
            $user->save();

            Auth::login($user, true);
            $request->session()->regenerate();
            return redirect()->route('profile');
        } else
            return view('registration');
    }

    public function login(Request $request) {
        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'name' => ['required', 'regex:/^[-a-zA-Z_0-9]{5,30}$/'],
                'password' => ['required', 'regex:/(?=.*[0-9])(?=.*[!@#$%^&*.,])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@#$%^&*.,]{10,30}/']
            ]);

            $credentials['name'] = strtolower($credentials['name']);

            if (Auth::attempt($credentials, $request->all()['remember'] == "1")) {
                $request->session()->regenerate();
                return redirect('profile');
            }

            return back()->withErrors(['name' => 'The provided credentials do not match our records.']);

        } else
            return view('login');
    }

    public function logout() {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    }
}

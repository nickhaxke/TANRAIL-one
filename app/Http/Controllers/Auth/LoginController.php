<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            $targetUrl = $this->resolveRedirectUrl($user);

            if ($this->isOperationalRestaurantRole($user)) {
                return redirect($targetUrl);
            }

            return redirect()->intended($targetUrl);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    private function resolveRedirectUrl($user): string
    {
        $roleNames = $user->roles->pluck('name')->all();

        if (in_array('Restaurant Cashier', $roleNames) || in_array('Waiter', $roleNames)) {
            return route('restaurant.pos');
        }

        if (in_array('Kitchen Staff', $roleNames)) {
            return route('restaurant.kitchen');
        }

        if (in_array('Restaurant Manager', $roleNames)) {
            return route('restaurant.dashboard');
        }

        return route('management.dashboard');
    }

    private function isOperationalRestaurantRole($user): bool
    {
        $roleNames = $user->roles->pluck('name')->all();

        return in_array('Restaurant Cashier', $roleNames) ||
               in_array('Waiter', $roleNames) ||
               in_array('Kitchen Staff', $roleNames);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}

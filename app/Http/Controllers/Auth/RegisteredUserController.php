<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{

    public function create(): View
    {
        return view('auth.register');
    }
    

    public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'username' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults(), 'regex:/^(?=.*[A-Z]).+$/'],
        'role' => ['nullable', 'in:admin,staff,borrower'],
        'age' => ['required', 'integer'],
        'phonenumber' => ['required', 'string', 'max:255'],
    ]);

    $user = User::create([
        'uid' => uniqid(), // or Str::uuid()
        'username' => $validated['username'],
        'email' => $validated['email'],
        'age' => $validated['age'],
        'phonenumber' => $validated['phonenumber'],
        'role' => $validated['role'] ?? 'borrower',
        'password' => Hash::make($validated['password']),
    ]);

    event(new Registered($user));
    Auth::login($user);

    return redirect()->intended(route('home', absolute: false));
}

}

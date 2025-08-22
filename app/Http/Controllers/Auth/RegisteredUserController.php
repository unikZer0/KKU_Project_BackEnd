<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'username' => ['required', 'string', 'max:255'],
                'age' => ['nullable', 'integer', 'min:1', 'max:120'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                'phonenumber' => ['required', 'string', 'max:15'],
                'password' => ['required', 'confirmed', Password::defaults(), 'regex:/^(?=.*[A-Z]).+$/'],
                'role' => ['nullable', 'in:admin,staff,borrower'],
            ],
            [   // ✅ This is the custom messages array
                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
                'password.regex' => 'รหัสผ่านต้องมีตัวอักษรใหญ่ (A-Z) อย่างน้อยหนึ่งตัว',
            ]
        );

        $user = User::create([
            'username' => $request->username,
            'age' => $request->age,
            'email' => $request->email,
            'phonenumber' => $request->phonenumber,
            'password' => Hash::make($validated['password']),
            'role' => $request->input('role', 'borrower'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}

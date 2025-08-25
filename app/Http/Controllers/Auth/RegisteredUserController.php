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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{

    public function create(): View
    {
        return view('auth.register');
    }


    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'username' => ['required', 'string', 'max:255'],
                'age' => ['required', 'integer', 'min:0'],
                'phonenumber' => ['required', 'string', 'size:10', 'regex:/^[0-9]+$/', Rule::unique('users', 'phonenumber')],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
                'password' => ['required', 'confirmed', Password::defaults(), 'regex:/^(?=.*[A-Z]).+$/'],
                'role' => ['nullable', 'in:admin,staff,borrower'],
            ],
            [   
                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
                'password.regex' => 'รหัสผ่านต้องมีตัวอักษรใหญ่ (A-Z) อย่างน้อยหนึ่งตัว',
            ]
        );

        $user = User::create([
            'username' => $request->username,
            'age' => $request->age,
            'phonenumber' => $request->phonenumber,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->input('role', 'borrower'),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(route('home', absolute: false));
    }
}

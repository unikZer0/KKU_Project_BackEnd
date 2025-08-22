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

    public function create(): View
    {
        return view('auth.register');
    }
    

    public function store(Request $request): RedirectResponse
    {
        $request->validate(
        [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults(), 'regex:/^(?=.*[A-Z]).+$/'],
            'role' => ['nullable', 'in:admin,staff,borrower'],
        ],
        [   // ✅ This is the custom messages array
            'password.required' => 'กรุณากรอกรหัสผ่าน',                     
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',                  
            'password.regex' => 'รหัสผ่านต้องมีตัวอักษรใหญ่ (A-Z) อย่างน้อยหนึ่งตัว',
        ]
    );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => ['required','confirmed',Rules\Password::defaults(),],
            'role' => $request->input('role', 'borrower'),
        ]);

    event(new Registered($user));
    Auth::login($user);

    return redirect()->intended(route('home', absolute: false));
}

}

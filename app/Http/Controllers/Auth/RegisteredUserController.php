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
                'uid' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'phonenumber' => ['required', 'string', 'size:10', 'regex:/^[0-9]+$/', Rule::unique('users', 'phonenumber')],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
                'password' => ['required', 'confirmed', Password::defaults()],
                'password_confirmation' => ['required'],
                'role' => ['nullable', 'in:admin,staff,borrower'],
            ],
            [
                'uid.required' => 'กรุณากรอกหมายเลขบัตร',
                'name.required' => 'กรุณากรอกชื่อผู้ใช้',
                'phonenumber.required' => 'กรุณากรอกหมายเลขโทรศัพท์',
                'phonenumber.size' => 'หมายเลขโทรศัพท์ต้องมีความยาว 10 หลัก',
                'phonenumber.regex' => 'หมายเลขโทรศัพท์ต้องเป็นตัวเลขเท่านั้น',
                'phonenumber.unique' => 'หมายเลขโทรศัพท์นี้ถูกใช้งานแล้ว',
                'email.required' => 'กรุณากรอกอีเมล',
                'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
                'password.required' => 'กรุณากรอกรหัสผ่าน',
                'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
                'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
                'password.regex' => 'รหัสผ่านต้องมีตัวอักษรใหญ่ (A-Z) อย่างน้อยหนึ่งตัว',
            ]
        );

        $user = User::create([
            'uid' => $request->uid,
            'name' => $request->name,
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

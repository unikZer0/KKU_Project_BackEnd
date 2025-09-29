<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AuthenticatedSessionController extends Controller
{
    use LogsActivity;
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'exists:users,email'],
            'password' => ['required', Password::defaults()],
        ], 
        [
            'email.required' => 'กรุณากรอกอีเมล',
            'email.string'   => 'อีเมลต้องเป็นตัวอักษร',
            'email.email'    => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.exists' => 'ไม่พบอีเมลนี้ในระบบ',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านไม่ถูกต้อง',
            'password.regex' => 'รหัสผ่านต้องมีตัวอักษรใหญ่ (A-Z) อย่างน้อยหนึ่งตัว',
        ]);

        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        
        // Log successful login
        $this->logAuth('login', [
            'description' => "ผู้ใช้ {$user->name} ({$user->email}) เข้าสู่ระบบเรียบร้อย",
            'target_name' => $user->name,
            'severity' => 'info'
        ]);

        $defaultRoute = route('home', absolute: false);

        if (in_array($user->role, ['admin', 'staff'])) {
            $defaultRoute = route('admin.index', absolute: false);
        }

        return redirect()->intended($defaultRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Log logout before session is destroyed
        if ($user) {
            $this->logAuth('logout', [
                'description' => "ผู้ใช้ {$user->name} ({$user->email}) ออกจากระบบเรียบร้อย",
                'target_name' => $user->name,
                'severity' => 'info'
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function requestVerification(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        // Mark as pending/not-verified (0). Adjust if you have a separate status.
        $user->verified = 0;
        $user->save();
        return back()->with('success', 'ส่งคำขอยืนยันตัวตนแล้ว โปรดรอการอนุมัติจากผู้ดูแล');
    }
}



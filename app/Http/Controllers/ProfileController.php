<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Notifications\VerificationRequestCreated;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // Get latest verification request status
        $latestVerificationRequest = VerificationRequest::where('user_id', $user->id)
            ->latest()
            ->first();
            
        return view('profile.show', compact('user', 'latestVerificationRequest'));
    }

    public function requestVerification(Request $request)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
            'citizen_id_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max
        ]);

        /** @var User $user */
        $user = Auth::user();
        
        // Check if user already has a pending or approved verification request
        $existingRequest = VerificationRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existingRequest) {
            return back()->with('error', 'คุณมีคำขอยืนยันตัวตนที่รอดำเนินการหรือได้รับการอนุมัติแล้ว');
        }
        
        // Handle citizen ID image upload
        $imagePath = null;
        if ($request->hasFile('citizen_id_image')) {
            try {
                $image = $request->file('citizen_id_image');
                $imageName = 'citizen_id_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('verification_images', $imageName, 'public');
                $imagePath = '/storage/' . $imagePath; 
            } catch (\Exception $e) {
                return back()->with('error', 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ: ' . $e->getMessage());
            }
        }
        
        // Create new verification request
        $verificationRequest = VerificationRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'reason' => $request->reason,
            'citizen_id_image_path' => $imagePath,
        ]);
        
        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new VerificationRequestCreated($verificationRequest));
        }
        
        return back()->with('success', 'ส่งคำขอยืนยันตัวตนแล้ว โปรดรอการอนุมัติจากผู้ดูแล');
    }
}

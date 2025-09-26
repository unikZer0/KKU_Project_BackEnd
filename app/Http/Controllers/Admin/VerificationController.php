<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VerificationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function index()
    {
        $verificationRequests = VerificationRequest::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.verification.index', compact('verificationRequests'));
    }

    public function show($id)
    {
        $verificationRequest = VerificationRequest::with(['user', 'processedBy'])
            ->findOrFail($id);
            
        return view('admin.verification.show', compact('verificationRequest'));
    }

    public function approve(Request $request, $id)
    {
        $verificationRequest = VerificationRequest::findOrFail($id);
        
        if ($verificationRequest->status !== 'pending') {
            return back()->with('error', 'คำขอนี้ได้รับการดำเนินการแล้ว');
        }

        // Update verification request
        $verificationRequest->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        // Update user verification status
        $user = $verificationRequest->user;
        $user->update(['verified' => 1]);

        return back()->with('success', 'อนุมัติการยืนยันตัวตนเรียบร้อยแล้ว');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $verificationRequest = VerificationRequest::findOrFail($id);
        
        if ($verificationRequest->status !== 'pending') {
            return back()->with('error', 'คำขอนี้ได้รับการดำเนินการแล้ว');
        }

        // Update verification request
        $verificationRequest->update([
            'status' => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'ปฏิเสธการยืนยันตัวตนเรียบร้อยแล้ว');
    }
}

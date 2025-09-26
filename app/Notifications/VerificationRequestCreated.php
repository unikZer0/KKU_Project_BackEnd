<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $verificationRequest;

    public function __construct($verificationRequest)
    {
        $this->verificationRequest = $verificationRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('มีคำขอยืนยันตัวตนใหม่')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("ผู้ใช้ {$this->verificationRequest->user->name} ได้ส่งคำขอยืนยันตัวตน")
            ->line("อีเมล: {$this->verificationRequest->user->email}")
            ->line("รหัสผู้ใช้: {$this->verificationRequest->user->uid}")
            ->when($this->verificationRequest->reason, function ($mail) {
                return $mail->line("เหตุผล: {$this->verificationRequest->reason}");
            })
            ->action('ตรวจสอบคำขอ', route('admin.verification.show', $this->verificationRequest->id))
            ->line('กรุณาตรวจสอบและดำเนินการตามคำขอนี้');
    }

    public function toDatabase($notifiable)
    {
        return [
            'verification_request_id' => $this->verificationRequest->id,
            'user' => $this->verificationRequest->user->name,
            'user_email' => $this->verificationRequest->user->email,
            'user_uid' => $this->verificationRequest->user->uid,
            'reason' => $this->verificationRequest->reason,
            'status' => 'created',
            'message' => 'มีคำขอยืนยันตัวตนใหม่',
            'type' => 'verification_request_created',
            'url' => route('admin.verification.show', $this->verificationRequest->id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}

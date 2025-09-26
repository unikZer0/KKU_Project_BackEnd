<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequestAutoRejected extends Notification implements ShouldQueue
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
            ->subject(' คำขอยืนยันตัวตนถูกปฏิเสธอัตโนมัติ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line(" คำขอยืนยันตัวตนของคุณถูกปฏิเสธอัตโนมัติ")
            ->line("เหตุผล: ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน")
            ->line(" วันที่ส่งคำขอ: {$this->verificationRequest->created_at->format('d/m/Y H:i')}")
            ->line(" วันที่ปฏิเสธ: " . now()->format('d/m/Y H:i'))
            ->line("หากต้องการยืนยันตัวตน กรุณาส่งคำขอใหม่")
            ->action('ส่งคำขอใหม่', route('profile.show'))
            ->line('ขอบคุณที่ใช้บริการของเรา');
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->verificationRequest->id,
            'equipment'  => 'การยืนยันตัวตน',
            'message'    => 'คำขอยืนยันตัวตนถูกปฏิเสธอัตโนมัติ เนื่องจากไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
            'status' => 'rejected',
            'type'       => 'verification_auto_rejected',
            'url'        => route('profile.show'),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'reason' => 'ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
                'submitted_at' => $this->verificationRequest->created_at->format('Y-m-d H:i:s'),
                'rejected_at' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }
}

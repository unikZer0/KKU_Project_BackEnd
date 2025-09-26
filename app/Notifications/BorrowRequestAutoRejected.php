<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestAutoRejected extends Notification implements ShouldQueue
{
    use Queueable;
    protected $borrowRequest;

    public function __construct($borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(' คำขอยืมถูกปฏิเสธอัตโนมัติ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line(" เลขที่คำขอ: #{$this->borrowRequest->req_id}")
            ->line("**คำขอยืมของคุณถูกปฏิเสธอัตโนมัติ**")
            ->line("เหตุผล: ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน")
            ->line(" วันที่ส่งคำขอ: {$this->borrowRequest->created_at->format('d/m/Y H:i')}")
            ->line(" วันที่ปฏิเสธ: " . now()->format('d/m/Y H:i'))
            ->line("หากต้องการยืมอุปกรณ์ กรุณาส่งคำขอใหม่")
            ->action('ส่งคำขอใหม่', route('home'))
            ->line('ขอบคุณที่ใช้บริการของเรา');
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'message'    => 'คำขอยืมถูกปฏิเสธอัตโนมัติ เนื่องจากไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
            'status' => 'rejected',
            'type'       => 'auto_rejected',
            'url'        => route('home'),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'reason' => 'ไม่มีการดำเนินการจากผู้ดูแลภายใน 3 วัน',
                'submitted_at' => $this->borrowRequest->created_at->format('Y-m-d H:i:s'),
                'rejected_at' => now()->format('Y-m-d H:i:s'),
            ],
        ];
    }
}

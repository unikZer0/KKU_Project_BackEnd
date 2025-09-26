<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestAutoCancelled extends Notification implements ShouldQueue
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
            ->subject(' คำขอยืมถูกยกเลิกอัตโนมัติ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line(" เลขที่คำขอ: #{$this->borrowRequest->req_id}")
            ->line("**คำขอยืมของคุณถูกยกเลิกอัตโนมัติ**")
            ->line("เหตุผล: ไม่มารับอุปกรณ์ภายใน 3 วัน")
            ->line(" วันที่อนุมัติ: {$this->borrowRequest->updated_at->format('d/m/Y H:i')}")
            ->line(" หมดเขตการรับ: {$this->borrowRequest->pickup_deadline->format('d/m/Y H:i')}")
            ->line("หากต้องการยืมอุปกรณ์อีกครั้ง กรุณาส่งคำขอใหม่")
            ->action('ส่งคำขอใหม่', route('home'))
            ->line('ขอบคุณที่ใช้บริการของเรา');
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'message'    => 'คำขอยืมถูกยกเลิกอัตโนมัติ เนื่องจากไม่มารับอุปกรณ์ภายใน 3 วัน',
            'status' => 'cancelled',
            'type'       => 'auto_cancelled',
            'url'        => route('home'),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'reason' => 'ไม่มารับอุปกรณ์ภายใน 3 วัน',
                'approved_at' => $this->borrowRequest->updated_at->format('Y-m-d H:i:s'),
                'pickup_deadline' => $this->borrowRequest->pickup_deadline->format('Y-m-d H:i:s'),
            ],
        ];
    }
}

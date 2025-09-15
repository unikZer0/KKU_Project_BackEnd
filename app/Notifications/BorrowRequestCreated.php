<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class BorrowRequestCreated extends Notification implements ShouldQueue
    
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
            ->subject('มีคำขอยืมอุปกรณ์ใหม่')
            ->line("ผู้ใช้ {$this->borrowRequest->user->name} ได้สร้างคำขอยืมอุปกรณ์")
            ->line("อุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->action('ดูรายละเอียด', url('admin.requests.show', $this->borrowRequest->req_id))
            ->line('กรุณาตรวจสอบคำขอนี้ด้วยครับ');
    }
    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'user'       => $this->borrowRequest->user->name,
            'status' => 'created',
            'message'    => 'มีคำขอยืมอุปกรณ์ใหม่',
            'url'        => route('admin.requests.show', $this->borrowRequest->req_id),
        ];
    }
}

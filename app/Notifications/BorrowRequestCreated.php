<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestCreated extends Notification
{

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
            ->line("ผู้ใช้ {$this->borrowRequest->user->username} ได้สร้างคำขอยืมอุปกรณ์")
            ->line("อุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->action('ดูรายละเอียด', url('/borrower/myreq'))
            ->line('กรุณาตรวจสอบคำขอนี้ด้วยครับ');
    }
    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'user'       => $this->borrowRequest->user->username,
            'status' => 'created',
            'message'    => 'มีคำขอยืมอุปกรณ์ใหม่',
            'url'        => url('/admin/requests')
        ];
    }
}

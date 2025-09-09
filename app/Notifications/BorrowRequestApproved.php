<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestApproved extends Notification
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
            ->subject('คำขอยืมของคุณได้รับการอนุมัติ')
            ->greeting("สวัสดี {$notifiable->username}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line("ได้รับการอนุมัติเรียบร้อยแล้ว ")
            ->action('ดูรายละเอียด', url('/borrower/reqdetail', $this->borrowRequest->req_id))
            ->line('กรุณามารับอุปกรณ์ตามเวลาที่กำหนด');
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'message'    => 'คำขอยืมของคุณได้รับการอนุมัติแล้ว',
            'status' => 'approved',
            'type'       => 'borrow_request_approved',
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}

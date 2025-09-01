<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestRejected extends Notification
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
            ->subject('คำขอยืมของคุณถูกปฏิเสธ')
            ->greeting("สวัสดี {$notifiable->username}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line('สถานะ: ถูกปฏิเสธ')
            ->line('เหตุผล: ' . ($this->borrowRequest->reject_reason ?: '-'))
            ->action('ดูรายละเอียด', url('/borrower/myreq' . $this->borrowRequest->id));
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment' => $this->borrowRequest->equipment->name,
            'status' => 'rejected',
            'reason' => $this->borrowRequest->reject_reason,
            'type' => 'borrow_request_rejected',
            'created_at' => now()->toDateTimeString(),
        ];
    }
}



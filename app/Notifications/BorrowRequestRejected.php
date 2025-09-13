<?php

namespace App\Notifications;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestRejected extends Notification implements ShouldQueue
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
            ->subject('คำขอยืมของคุณถูกปฏิเสธ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line('สถานะ: ถูกปฏิเสธ')
            ->line('เหตุผล: ' . ($this->borrowRequest->reject_reason ?: '-'))
            ->action('ดูรายละเอียด', url('/borrower/reqdetail', $this->borrowRequest->req_id));
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment' => $this->borrowRequest->equipment->name,
            'status' => 'rejected',
            'message' => 'คำขอยืมของคุณถูกปฏิเสธ',
            'reason' => $this->borrowRequest->reject_reason,
            'type' => 'borrow_request_rejected',
            'url' => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}



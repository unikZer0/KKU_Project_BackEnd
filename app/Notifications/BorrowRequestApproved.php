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
        $rangeMsg = '';
                if ($this->borrowRequest->start_at && $this->borrowRequest->end_at) {
                    $signed = $this->borrowRequest->start_at->diffInDays($this->borrowRequest->end_at, false) + 1;
                    $days = max($signed, 0);

                    $rangeMsg = "คุณสามารถยืมได้ {$days} วัน "
                        . $this->borrowRequest->start_at->format('Y-m-d')
                        . ' ถึง ' . $this->borrowRequest->end_at->format('Y-m-d');
                } else {
                    $rangeMsg = "ยังไม่ได้เลือกวันที่เริ่มและสิ้นสุด";
                }

        return (new MailMessage)
            ->subject('คำขอยืมของคุณได้รับการอนุมัติ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line("ได้รับการอนุมัติเรียบร้อยแล้ว ")
            ->when($rangeMsg !== '', function ($mail) use ($rangeMsg) {
                return $mail->line($rangeMsg);
            })
            ->action('ดูรายละเอียด', url('/borrower/reqdetail', $this->borrowRequest->req_id))
            ->line('กรุณามารับอุปกรณ์ตามเวลาที่กำหนด');
    }

    public function toDatabase($notifiable)
    {
        $extra = [];
        if ($this->borrowRequest->start_at && $this->borrowRequest->end_at) {
            $extra['start_at'] = $this->borrowRequest->start_at->format('Y-m-d');
            $extra['end_at'] = $this->borrowRequest->end_at->format('Y-m-d');
            $extra['days'] = $this->borrowRequest->end_at->diffInDays($this->borrowRequest->start_at) + 1;
            $extra['message_detail'] = "คุณสามารถยืมได้ {$extra['days']} วัน {$extra['start_at']} ถึง {$extra['end_at']}";
        }
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'message'    => 'คำขอยืมของคุณได้รับการอนุมัติแล้ว',
            'status' => 'approved',
            'type'       => 'borrow_request_approved',
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
            'extra' => $extra,
        ];
    }
}

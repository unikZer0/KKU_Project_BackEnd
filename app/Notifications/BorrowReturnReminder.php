<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowReturnReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;
    protected $whenType; // 'due_tomorrow' | 'due_today'

    public function __construct($borrowRequest, string $whenType)
    {
        $this->borrowRequest = $borrowRequest;
        $this->whenType = $whenType;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $endDate = optional($this->borrowRequest->end_at)->format('Y-m-d');
        $equipment = $this->borrowRequest->equipment->name ?? '-';

        $subject = $this->whenType === 'due_tomorrow'
            ? 'แจ้งเตือนกำหนดคืนอุปกรณ์: พรุ่งนี้ครบกำหนด'
            : 'แจ้งเตือนกำหนดคืนอุปกรณ์: วันนี้ครบกำหนด (12:00)';

        $lines = [
            "อุปกรณ์: {$equipment}",
            "วันครบกำหนดคืน: {$endDate}",
        ];

        if ($this->whenType === 'due_tomorrow') {
            array_unshift($lines, 'แจ้งเตือน: พรุ่งนี้ครบกำหนดคืนอุปกรณ์');
        } else {
            array_unshift($lines, 'แจ้งเตือน: วันนี้ครบกำหนดคืนอุปกรณ์ เวลา 12:00');
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("สวัสดี {$notifiable->name}");

        foreach ($lines as $line) {
            $mail->line($line);
        }

        return $mail->action('ดูคำขอยืมของฉัน', url('/borrower/reqdetail', $this->borrowRequest->req_id));
    }

    public function toDatabase($notifiable)
    {
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name ?? '-',
            'type'       => 'borrow_return_reminder',
            'when'       => $this->whenType,
            'end_at'     => optional($this->borrowRequest->end_at)->format('Y-m-d'),
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}



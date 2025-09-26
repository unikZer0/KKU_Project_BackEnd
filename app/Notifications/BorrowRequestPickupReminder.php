<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestPickupReminder extends Notification implements ShouldQueue
{
    use Queueable;
    protected $borrowRequest;
    protected $daysRemaining;

    public function __construct($borrowRequest, $daysRemaining = null)
    {
        $this->borrowRequest = $borrowRequest;
        $this->daysRemaining = $daysRemaining;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; 
    }

    public function toMail($notifiable)
    {
        $daysLeft = $this->daysRemaining ?? 3;
        $deadline = $this->borrowRequest->pickup_deadline 
            ? $this->borrowRequest->pickup_deadline->format('d/m/Y H:i')
            : now()->addDays($daysLeft)->format('d/m/Y H:i');

        $mail = (new MailMessage)
            ->subject(' เตือน: กรุณามารับอุปกรณ์ของคุณ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line("คำขอยืมอุปกรณ์: {$this->borrowRequest->equipment->name}")
            ->line(" เลขที่คำขอ: #{$this->borrowRequest->req_id}")
            ->line(" **คุณยังไม่ได้มารับอุปกรณ์**");

        if ($this->daysRemaining) {
            $mail->line(" **เหลือเวลาอีก {$this->daysRemaining} วัน**");
        }

        $mail->line(" **สถานที่รับ**: คณะเทคโนโลยี มหาวิทยาลัยขอนแก่น")
            ->line('**เวลา**: จันทร์-ศุกร์ 08:00-17:00 น.')
            ->line(" **หมดเขต**: {$deadline}")
            ->line(' **หากไม่มารับภายในกำหนด คำขอจะถูกยกเลิกอัตโนมัติ**')
            ->action('ดูรายละเอียด', route('borrower.equipments.reqdetail', $this->borrowRequest->req_id))
            ->line('กรุณามารับอุปกรณ์โดยเร็ว');

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        $daysLeft = $this->daysRemaining ?? 3;
        
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name,
            'message'    => "เตือน: กรุณามารับอุปกรณ์ หมดเขตในอีก {$daysLeft} วัน",
            'status' => 'warning',
            'type'       => 'pickup_reminder',
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'days_remaining' => $daysLeft,
                'pickup_deadline' => $this->borrowRequest->pickup_deadline?->format('Y-m-d H:i:s'),
                'pickup_location' => 'คณะเทคโนโลยี มหาวิทยาลัยขอนแก่น',
            ],
        ];
    }
}

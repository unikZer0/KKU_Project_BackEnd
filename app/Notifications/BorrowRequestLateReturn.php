<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestLateReturn extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $borrowRequest;
    protected $daysLate;
    protected $penaltyAmount;

    public function __construct($borrowRequest, $daysLate, $penaltyAmount = 0)
    {
        $this->borrowRequest = $borrowRequest;
        $this->daysLate = $daysLate;
        $this->penaltyAmount = $penaltyAmount;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $equipment = $this->borrowRequest->equipment->name ?? '-';
        $reqId = $this->borrowRequest->req_id;
        $endDate = optional($this->borrowRequest->end_at)->format('d/m/Y');
        $currentDate = now()->format('d/m/Y');

        return (new MailMessage)
            ->subject(' คืนอุปกรณ์ล่าช้า - กรุณาคืนโดยเร็ว')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line(" **คืนอุปกรณ์ล่าช้า**")
            ->line(" **อุปกรณ์**: {$equipment}")
            ->line(" **เลขที่คำขอ**: #{$reqId}")
            ->line(" **กำหนดคืน**: {$endDate}")
            ->line(" **วันที่ปัจจุบัน**: {$currentDate}")
            ->line(" **ล่าช้า**: {$this->daysLate} วัน")
            ->line("")
            ->line(" **ค่าปรับ**: ฿" . number_format($this->penaltyAmount, 2))
            ->line("• ค่าปรับคิดตามจำนวนวันที่ล่าช้า")
            ->line("• กรุณาชำระค่าปรับเมื่อคืนอุปกรณ์")
            ->line("")
            ->line(" **กรุณาคืนอุปกรณ์โดยเร็วที่สุด**")
            ->line("• การคืนล่าช้าอาจส่งผลต่อสิทธิ์การยืมในอนาคต")
            ->line("• หากไม่คืนภายใน 7 วัน อาจถูกระงับสิทธิ์การยืม")
            ->line("")
            ->line(" **ติดต่อ**: หากมีปัญหากรุณาติดต่อเจ้าหน้าที่ทันที")
            ->action('ดูรายละเอียดคำขอ', route('borrower.equipments.reqdetail', $this->borrowRequest->req_id))
            ->line("กรุณาคืนอุปกรณ์โดยเร็วเพื่อหลีกเลี่ยงค่าปรับเพิ่มเติม");
    }

    public function toDatabase($notifiable)
    {
        $endDate = optional($this->borrowRequest->end_at)->format('d/m/Y');
        
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name ?? '-',
            'message'    => "คืนอุปกรณ์ล่าช้า {$this->daysLate} วัน - ค่าปรับ: ฿" . number_format($this->penaltyAmount, 2),
            'status'     => 'warning',
            'type'       => 'late_return',
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'end_date' => $this->borrowRequest->end_at?->format('Y-m-d'),
                'days_late' => $this->daysLate,
                'penalty_amount' => $this->penaltyAmount,
                'equipment_name' => $this->borrowRequest->equipment->name,
                'is_late' => true,
                'penalty_rate' => '฿50/วัน' // Configurable penalty rate
            ],
        ];
    }
}

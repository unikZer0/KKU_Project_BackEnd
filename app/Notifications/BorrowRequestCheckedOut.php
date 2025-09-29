<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestCheckedOut extends Notification implements ShouldQueue
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
        $endDate = optional($this->borrowRequest->end_at)->format('d/m/Y');
        $equipment = $this->borrowRequest->equipment->name ?? '-';
        $reqId = $this->borrowRequest->req_id;

        return (new MailMessage)
            ->subject('🎉 ยืมอุปกรณ์สำเร็จ - กรุณาอ่านข้อแนะนำสำคัญ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line(" **ยินดีด้วย! คุณได้รับอุปกรณ์แล้ว**")
            ->line(" **อุปกรณ์**: {$equipment}")
            ->line(" **เลขที่คำขอ**: #{$reqId}")
            ->line("**กำหนดคืน**: {$endDate}")
            ->line("")
            ->line(" **ข้อแนะนำสำคัญ**:")
            ->line("• กรุณาคืนอุปกรณ์ตรงเวลา เพื่อให้ผู้อื่นสามารถยืมได้")
            ->line("• ดูแลรักษาอุปกรณ์ให้อยู่ในสภาพดี")
            ->line("• เก็บอุปกรณ์ในที่ปลอดภัย หลีกเลี่ยงความชื้นและแสงแดด")
            ->line("• หากอุปกรณ์เสียหาย กรุณาแจ้งทันที")
            ->line("• ห้ามให้ผู้อื่นยืมต่อ")
            ->line("")
            ->line(" **ติดต่อ**: หากมีปัญหากรุณาติดต่อเจ้าหน้าที่")
            ->action('ดูรายละเอียดคำขอ', route('borrower.equipments.reqdetail', $this->borrowRequest->req_id))
            ->line("ขอบคุณที่ใช้บริการระบบยืมอุปกรณ์");
    }

    public function toDatabase($notifiable)
    {
        $endDate = optional($this->borrowRequest->end_at)->format('d/m/Y');
        
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name ?? '-',
            'message'    => "ยืมอุปกรณ์สำเร็จ! กำหนดคืน: {$endDate} - กรุณาดูแลรักษาอุปกรณ์",
            'status'     => 'success',
            'type'       => 'equipment_checked_out',
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'end_date' => $this->borrowRequest->end_at?->format('Y-m-d'),
                'equipment_name' => $this->borrowRequest->equipment->name,
                'care_instructions' => [
                    'คืนตรงเวลา',
                    'ดูแลรักษาอุปกรณ์',
                    'เก็บในที่ปลอดภัย',
                    'แจ้งความเสียหายทันที',
                    'ห้ามให้ผู้อื่นยืมต่อ'
                ]
            ],
        ];
    }
}

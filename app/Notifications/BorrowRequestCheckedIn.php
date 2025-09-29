<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestCheckedIn extends Notification implements ShouldQueue
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
        $equipment = $this->borrowRequest->equipment->name ?? '-';
        $reqId = $this->borrowRequest->req_id;
        $checkedInAt = optional($this->borrowRequest->transaction?->checked_in_at)->format('d/m/Y H:i');
        $penaltyAmount = $this->borrowRequest->transaction?->penalty_amount ?? 0;
        
        // Get equipment condition details
        $conditionDetails = $this->getConditionDetails();
        $hasIssues = $this->hasEquipmentIssues();
        $hasPenalty = $penaltyAmount > 0;

        $mail = (new MailMessage)
            ->subject(' คืนอุปกรณ์เรียบร้อยแล้ว - ขอบคุณที่ใช้บริการ')
            ->greeting("สวัสดี {$notifiable->name}")
            ->line(" **คืนอุปกรณ์เรียบร้อยแล้ว**")
            ->line(" **อุปกรณ์**: {$equipment}")
            ->line(" **เลขที่คำขอ**: #{$reqId}")
            ->line(" **เวลาคืน**: {$checkedInAt}");

        // Add condition details if there are issues
        if ($hasIssues) {
            $mail->line("")
                 ->line(" **สภาพอุปกรณ์เมื่อคืน**:")
                 ->line($conditionDetails);
        }

        // Add penalty information if applicable
        if ($hasPenalty) {
            $mail->line("")
                 ->line(" **ค่าปรับ**: ฿" . number_format($penaltyAmount, 2))
                 ->line("• กรุณาชำระค่าปรับตามที่กำหนด");
        }

        $mail->line("")
             ->line(" **ขอบคุณที่**:")
             ->line("• พยายามคืนอุปกรณ์ตรงเวลา")
             ->line("• พยายามดูแลรักษาอุปกรณ์เป็นอย่างดี")
             ->line("• พยายามใช้บริการระบบยืมอุปกรณ์");

        if ($hasIssues || $hasPenalty) {
            $mail->line("")
                 ->line(" **ติดต่อ**: หากมีข้อสงสัยเกี่ยวกับค่าปรับหรือสภาพอุปกรณ์ กรุณาติดต่อเจ้าหน้าที่");
        }

        $mail->line("")
             ->line(" **หากต้องการยืมอุปกรณ์อีกครั้ง** สามารถทำได้ผ่านระบบ")
             ->action('ดูประวัติการยืม', route('borrower.equipments.reqdetail', $this->borrowRequest->req_id))
             ->line("ขอบคุณที่ใช้บริการระบบยืมอุปกรณ์");

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        $checkedInAt = optional($this->borrowRequest->transaction?->checked_in_at)->format('d/m/Y H:i');
        $penaltyAmount = $this->borrowRequest->transaction?->penalty_amount ?? 0;
        $hasIssues = $this->hasEquipmentIssues();
        $hasPenalty = $penaltyAmount > 0;
        
        // Build message based on conditions
        $message = "คืนอุปกรณ์เรียบร้อยแล้ว - ขอบคุณที่ใช้บริการ";
        if ($hasIssues || $hasPenalty) {
            $message .= " (มีรายละเอียดเพิ่มเติม)";
        }
        
        return [
            'request_id' => $this->borrowRequest->id,
            'equipment'  => $this->borrowRequest->equipment->name ?? '-',
            'message'    => $message,
            'status'     => $hasIssues || $hasPenalty ? 'warning' : 'success',
            'type'       => 'equipment_checked_in',
            'url'        => route('borrower.equipments.reqdetail', $this->borrowRequest->req_id),
            'created_at' => now()->toDateTimeString(),
            'extra' => [
                'checked_in_at' => $this->borrowRequest->transaction?->checked_in_at?->format('Y-m-d H:i:s'),
                'equipment_name' => $this->borrowRequest->equipment->name,
                'penalty_amount' => $penaltyAmount,
                'has_equipment_issues' => $hasIssues,
                'has_penalty' => $hasPenalty,
                'condition_details' => $hasIssues ? $this->getConditionDetails() : null,
                'thank_you_message' => 'ขอบคุณที่คืนอุปกรณ์ตรงเวลาและดูแลรักษาเป็นอย่างดี'
            ],
        ];
    }

    /**
     * Get detailed condition information for equipment and accessories
     */
    private function getConditionDetails()
    {
        $details = [];
        
        // Check main equipment items
        foreach ($this->borrowRequest->items as $item) {
            if ($item->condition_in && $item->condition_in !== 'สภาพดี') {
                $serial = $item->equipmentItem->serial_number ?? 'N/A';
                $details[] = "• อุปกรณ์ชิ้นที่ {$item->equipmentItem->serial_number}: {$item->condition_in}";
            }
            
            // Check accessories
            foreach ($item->accessories as $accessory) {
                if ($accessory->condition_in && $accessory->condition_in !== 'สภาพดี') {
                    $accessoryName = $accessory->accessory->name ?? 'N/A';
                    $details[] = "• {$accessoryName}: {$accessory->condition_in}";
                }
            }
        }
        
        return implode("\n", $details);
    }

    /**
     * Check if there are any equipment issues
     */
    private function hasEquipmentIssues()
    {
        // Check main equipment items
        foreach ($this->borrowRequest->items as $item) {
            if ($item->condition_in && $item->condition_in !== 'สภาพดี') {
                return true;
            }
            
            // Check accessories
            foreach ($item->accessories as $accessory) {
                if ($accessory->condition_in && $accessory->condition_in !== 'สภาพดี') {
                    return true;
                }
            }
        }
        
        return false;
    }
}

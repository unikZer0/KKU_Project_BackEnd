<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use App\Models\Log;

class LogsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Log::all()->map(function ($logs) {
            return [
                $logs->admin_id,
                $logs->action,
                $logs->target_type,
                $logs->target_id,
                $logs->description,
                optional($logs->create_at)->format('d/m/Y'),
            ];
        });
    }
    public function headings(): array
    {
        return [
            'เลขไอดีแอดมิน',
            'การกระทำ',
            'เป้าหมาย',
            'รหัสเป้าหมาย',
            'รายละเอียด',
            "สร้างในวันที่"
        ];
    }
}

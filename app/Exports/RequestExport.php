<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\BorrowRequest;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RequestExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return BorrowRequest::all()->map(function ($request) {
            return [
                $request->id,
                $request->req_id,
                $request->user->name,
                $request->equipment->name,
                $request->start_at->format('d/m/Y'),
                $request->end_at->format('d/m/Y'),
                $request->status,
                $request->reject_reason,
                $request->cancel_reason,
                optional($request->create_at)->format('d/m/Y'),
            ];
        });
    }
    public function headings(): array
    {
        return [
            'ไอดี',
            'ไอดีคำขอ',
            'ชื่อผู้ใช้',
            'ชื่ออุปกรณ์',
            'เริ่มวันที่',
            'สิ้นสุดวันที่',
            'สถานะ',
            'เหตุผลการปฏิเสธ',
            "เหตุผลการยกเลิก",
            "สร้างในวันที่"
        ];
    }
}

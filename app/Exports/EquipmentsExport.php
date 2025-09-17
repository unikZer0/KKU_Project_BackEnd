<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Equipment;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EquipmentsExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Equipment::all()->map(function ($equipment) {
            return [
                $equipment->id,
                $equipment->code,
                $equipment->name,
                $equipment->description,
                optional($equipment->category)->name,
                $equipment->status,
                $equipment->photo_path,
                optional($equipment->created_at)->format('d/m/Y'),
            ];
        });
    }
    public function headings(): array
    {
        return [
            'ไอดี',
            'หมายเลขครุภัณฑ์',
            'ชื่อ',
            'รายละเอียด',
            'หมวดหมู่',
            'สถานะ',
            'รูปภาพ',
            'สร้างในวันที่',
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Equipment;

class EquipmentsExport implements FromCollection
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
            'id',
            'code',
            'name',
            'description',
            'category_name',
            'status',
            'photo_path',
            'created_at'
        ];
    }
}

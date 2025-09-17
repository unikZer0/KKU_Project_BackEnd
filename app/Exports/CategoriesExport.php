<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Category::all()->map(function ($category) {
            return [
                $category->id,
                $category->cate_id,
                $category->name,
                optional($category->created_at)->format('d/m/Y'),
            ];
        });
    }
    public function headings(): array
    {
        return [
            'ไอดี',
            'รหัสหมวดหมู่',
            'ชื่อ',
        ];
    }
}

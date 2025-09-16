<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\Category;

class CategoriesExport implements FromCollection
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
            'id',
            'cate_id',
            'name',
        ];
    }
}

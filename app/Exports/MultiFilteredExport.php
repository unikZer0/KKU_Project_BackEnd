<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Equipment;
use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MultiFilteredExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $type;

    public function __construct(Request $request, string $type)
    {
        $this->request = $request;
        $this->type = $type;
    }

    public function collection()
    {
        return match ($this->type) {
            'users' => $this->filteredUsers(),
            'equipments' => $this->filteredEquipments(),
            'categories' => $this->filteredCategories(),
            default => collect(),
        };
    }

    public function headings(): array
    {
        return match ($this->type) {
            'users' => ['ID', 'UID', 'Name', 'Email', 'Phone', 'Role', 'Created At'],
            'equipments' => ['ID', 'Code', 'Name', 'Category', 'Status', 'Created At'],
            'categories' => ['ID', 'CATE_ID', 'Name', 'Created At'],
            default => [],
        };
    }

    protected function filteredUsers()
    {
        $query = User::query();

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phonenumber', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('uid', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('role')) {
            $query->where('role', $this->request->role);
        }

        if ($this->request->filled('sort')) {
            $query->orderBy($this->request->sort, $this->request->direction ?? 'asc');
        }

        return $query->get()->map(fn($u) => [
            $u->id,
            $u->uid,
            $u->name,
            $u->email,
            $u->phonenumber,
            ucfirst($u->role),
            optional($u->created_at)->format('Y-m-d'),
        ]);
    }

    protected function filteredEquipments()
    {
        $query = Equipment::with('category');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($this->request->filled('category_id')) {
            $query->where('categories_id', $this->request->category_id);
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('sort')) {
            $query->orderBy($this->request->sort, $this->request->direction ?? 'asc');
        }

        return $query->get()->map(fn($eq) => [
            $eq->id,
            $eq->code,
            $eq->name,
            $eq->category->name ?? 'N/A',
            ucfirst($eq->status),
            optional($eq->created_at)->format('Y-m-d'),
        ]);
    }
    protected function filteredCategories()
    {
        $query = Category::withCount('equipments');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('cate_id', 'like', "%{$search}%");
            });
        }

        $allowedSorts = ['id', 'cate_id', 'name', 'created_at', 'updated_at', 'equipments_count'];
        $sort = in_array($this->request->sort, $allowedSorts) ? $this->request->sort : 'name';
        $direction = $this->request->direction ?? 'asc';

        $query->orderBy($sort, $direction);

        return $query->get()->map(fn($c) => [
            $c->id,
            $c->cate_id,
            $c->name,
            $c->equipments_count,
            optional($c->created_at)->format('Y-m-d'),
            optional($c->updated_at)->format('Y-m-d'),
        ]);
    }
}

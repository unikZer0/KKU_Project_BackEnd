<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Equipment;
use App\Models\Category;
use App\Models\BorrowRequest;

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
            'requests' => $this->filteredRequests(),
            default => collect(),
        };
    }

    public function headings(): array
    {
        return match ($this->type) {
            'users' => ['ไอดี', 'รหัสผู้ใช้', 'ชื่อ', 'อีเมล', 'โทรศัพท์', 'ตำแหน่ง', 'สร้างวันที่'],
            'equipments' => ['ไอดี', 'หมายเลขคุรุภัณฑ์', 'ชื่ออุปกรณ์', 'หมวดหมู่', 'สถานะ', 'สร้างวันที่'],
            'categories' => ['ไอดี', 'รหัสหมวดหมู่', 'ชื่อ', 'สร้างวันที่'],
            'requests' => ['ไอดี', 'รหัสคำขอ', 'ไอดีผู้ยืม', 'ชื่อผู้ยืม', 'เลขไอดีอุปกรณ์', 'ชื่ออุปกรณ์', 'เริ่มวันที่', 'ถึงวันที่', 'สถานะ', 'สาเหตุการปฏิเสธ', 'สาเหตุการยกเลิก', 'สร้างวันที่'],
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
    protected function filteredRequests()
    {
        $query = BorrowRequest::with('user', 'equipment');

        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('uid', 'like', "%{$search}%")
                )->orWhereHas(
                    'equipment',
                    fn($e) =>
                    $e->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                );
            });
        }

        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }

        if ($this->request->filled('sort')) {
            $query->orderBy($this->request->sort, $this->request->direction ?? 'asc');
        }

        return $query->get()->map(fn($r) => [
            $r->id,
            $r->req_id,
            $r->user->uid,
            $r->user->name,
            $r->equipment->code,
            $r->equipment->name,
            $r->start_at ? $r->start_at->format('Y-m-d') : '-',
            $r->end_at ? $r->end_at->format('Y-m-d') : '-',
            $r->status,
            $r->reject_reason ?? '-',
            $r->$r->cancel_reason ?? '-',
            optional($r->created_at)->format('Y-m-d'),
        ]);
    }
}

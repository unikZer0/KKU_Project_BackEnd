<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all()->map(function ($user) {
            return [
                $user->uid,
                $user->name,
                $user->email,
                $user->phonenumber ?? '-',
                optional($user->created_at)->format('d/m/Y'),
            ];
        });
    }
    public function headings(): array
    {
        return [
            'รหัสผู้ใช้',
            'ชื่อ',
            'อีเมล',
            'เบอร์โทรศัพท์',
            'สร้างขึ้นในวันที่',
        ];
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Exports\CategoriesExport;
use App\Exports\EquipmentsExport;
use App\Exports\RequestExport;
use App\Exports\MultiFilteredExport;

use App\Models\Equipment;
use App\Models\Category;
use App\Models\User;
use App\Models\BorrowRequest;

class ReportController extends Controller
{
    public function vueReport($type)
    {
        return view('admin.report.vue-wrapper', ['type' => $type]);
    }

    public function index()
    {
        return view('admin.report.index');
    }
    public function userReport()
    {
        $users = User::all()->map(function ($user) {
            return [
                'uid' => $user->uid,
                'name' => $user->name,
                'email' => $user->email,
                'phonenumber' => $user->phonenumber ?? '-',
                'created_at' => optional($user->created_at)->format('d/m/Y'),
            ];
        });

        return view('admin.report.users', compact('users'));
    }

    // public function exportUsers()
    // {
    //     return Excel::download(new UsersExport, 'users.xlsx');
    // }

    public function equipmentReport()
    {
        $equipments = Equipment::with('category')->get()->map(function ($eq) {
            return [
                'id' => $eq->id,
                'code' => $eq->code,
                'name' => $eq->name,
                'category_name' => $eq->category->name ?? 'N/A',
                'created_at' => optional($eq->created_at)->format('d/m/Y'),
            ];
        });

        return view('admin.report.equipments', compact('equipments'));
    }

    // public function exportEquipments(Request $request)
    // {
    //     return Excel::download(new FilteredEquipmentExport($request), 'equipments.xlsx');
    // }

    public function categoryReport()
    {
        $categories = Category::all()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
            ];
        });

        return view('admin.report.categories', compact('categories'));
    }

    // public function exportCategories()
    // {
    //     return Excel::download(new CategoriesExport, 'รายงานหมวดหมู่.xlsx');
    // }

    // Request Report
    public function requestReport()
    {
        $requests = BorrowRequest::with('user', 'equipment')
            ->whereIn('status', ['approved', 'rejected', 'cancelled'])
            ->latest()
            ->get()
            ->map(function ($req) {
                return [
                    'req_id' => $req->req_id,
                    'user_name' => $req->user->name ?? 'N/A',
                    'equipment_name' => $req->equipment->name ?? 'N/A',
                    'date' => $req->created_at->format('Y-m-d'),
                    'status' => ucfirst($req->status),
                    'reason' => $req->reject_reason ?? $r->cancel_reason ?? '-'
                ];
            });

        return view('admin.report.requests', compact('requests'));
    }

    public function export(Request $request)
    {
        $type = $request->input('type');
        $filename = $type . '-report.xlsx';

        return Excel::download(new MultiFilteredExport($request, $type), $filename);
    }

    // public function exportRequests()
    // {
    //     return Excel::download(new RequestExport, 'รายงานคำขอการยืม.xlsx');
    // }
}

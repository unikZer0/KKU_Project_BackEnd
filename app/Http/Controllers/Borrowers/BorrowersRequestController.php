<?php

namespace App\Http\Controllers\Borrowers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BorrowersRequestController extends Controller
{
    public function myRequests(Request $request)
    {
        // Step 4: Fetch borrow requests for logged-in user
        $user = $request->user(); // or auth()->user()
        $requests = $user->borrowRequests()->with('equipment')->get();

        return response()->json($requests);
    }
}

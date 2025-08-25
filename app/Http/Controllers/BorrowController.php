<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
    ]);

    Borrow::create([
        'product_id' => $request->product_id,
        'user_id'    => auth()->id(),
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
    ]);

    return redirect()->route('products.show', $request->product_id)
                     ->with('success', 'Borrow request submitted!');
}
}

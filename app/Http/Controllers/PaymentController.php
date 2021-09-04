<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(){

        $payment = Payment::orderBy('created_at', 'desc')
        ->paginate(10);

        return view('other.payment.payment', compact('payment'));
    }

    public function view($id){

        $payment = Payment::where('id', $id)
        ->first();

        return view('other.payment.payment_details', compact('payment'));
    }

    public function update(Request $request, $id){

        Payment::where('id', $id)
        ->update([
            'status' => $request->status,
        ]);

        session()->flash('success', 'Payment status has been changed');
        return redirect()->route('payment.index');
    }
}

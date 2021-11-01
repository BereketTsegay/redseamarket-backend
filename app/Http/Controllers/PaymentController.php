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

    public function documentUpload(Request $request, $id){

        $request->validate([
            'document'  => 'required|mimes:png,jpg,jpeg,gif,pdf,doc,docx,'
        ]);

        if($request->hasFile('document')){

            $document = uniqid() . '.' . $request->document->getClientOriginalExtension();

            $request->document->storeAs('public/document', $document);

            $document = 'storage/document/' . $document;

            Payment::where('ads_id', $id)
            ->update([
                'document'  => $document,
            ]);
        }

        session()->flash('success', 'Document uploaded');
        return redirect()->back();


    }
}

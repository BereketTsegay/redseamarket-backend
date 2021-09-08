<?php

namespace App\Http\Controllers;

use App\Common\Status;
use App\Models\RejectReason;
use Illuminate\Http\Request;

class RejectResonController extends Controller
{
    public function index(){

        $reson = RejectReason::orderBy('reson')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('other.reject_reson.reject_reson', compact('reson'));
    }

    public function store(Request $request){
        
        $request->validate(([
            'type'          => 'required',
            'reson'         => 'required',
            'description'   => 'required',
        ]));

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        $reson                  = new RejectReason();
        $reson->reson           = $request->reson;
        $reson->type            = $request->type;
        $reson->description     = $request->description;
        $reson->status          = $status;
        $reson->save();

        session()->flash('success', 'Reason has been created');
        return redirect()->route('reject.index');
    }

    public function update(Request $request){

        $request->validate(([
            'type'          => 'required',
            'reson'         => 'required',
            'description'   => 'required',
        ]));

        if($request->status == 'checked'){
            $status = 1;
        }
        else{
            $status = 0;
        }

        RejectReason::where('id', $request->reject_id)
        ->update([
            'reson'         => $request->reson,
            'type'          => $request->type,
            'description'   => $request->description,
            'status'        => $status,
        ]);

        session()->flash('success', 'Reason has been updated');
        return redirect()->route('reject.index');
    }
}

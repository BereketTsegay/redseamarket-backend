<?php

namespace App\Http\Controllers;

use App\Models\TermsConditions;
use Illuminate\Http\Request;

class TermsConditionsController extends Controller
{
    public function index(){

        $terms = TermsConditions::get();

        return view('other.terms.terms', compact('terms'));
    }

    public function store(Request $request) {
        
        $request->validate([
            'title'     => 'required',
            'terms'     => 'required',
        ]);

        $terms            = new TermsConditions();
        $terms->title     = $request->title;
        $terms->terms     = $request->terms;
        $terms->save();

        session()->flash('success', 'Terms & Condition has been stored');
        return redirect()->route('terms.index');
    }

    public function update(Request $request){
       
        $request->validate([
            'title'     => 'required',
            'terms'     => 'required',
        ]);

        TermsConditions::where('id', $request->id)
        ->update([
            'title'     => $request->title,
            'terms'     => $request->terms,
        ]);

        session()->flash('success', 'Terms & Condition has been update');
        return redirect()->route('terms.index');
    }

    public function delete($id){

        TermsConditions::destroy($id);

        session()->flash('success', 'Terms & Condition has been deleted');
        return redirect()->route('terms.index');
    }
}

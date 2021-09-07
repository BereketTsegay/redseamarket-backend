<?php

namespace App\Http\Controllers;

use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    public function index(){

        $privacy = PrivacyPolicy::get();

        return view('other.privacy.privacy', compact('privacy'));
    }

    public function store(Request $request) {
        
        $request->validate([
            'title'     => 'required',
            'privacy'   => 'required',
        ]);

        $privacy            = new PrivacyPolicy();
        $privacy->title     = $request->title;
        $privacy->policy    = $request->privacy;
        $privacy->save();

        session()->flash('success', 'Privacy Policy has been stored');
        return redirect()->route('privacy.index');
    }

    public function update(Request $request){
       
        $request->validate([
            'title'     => 'required',
            'privacy'   => 'required',
        ]);

        PrivacyPolicy::where('id', $request->id)
        ->update([
            'title'     => $request->title,
            'policy'    => $request->privacy,
        ]);

        session()->flash('success', 'Privacy Policy has been update');
        return redirect()->route('privacy.index');
    }

    public function delete($id){

        PrivacyPolicy::destroy($id);

        session()->flash('success', 'Privacy Policy has been deleted');
        return redirect()->route('privacy.index');
    }
}

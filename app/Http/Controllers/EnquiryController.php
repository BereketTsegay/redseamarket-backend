<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $enquiry = Enquiry::orderBy('created_at', 'desc')
        ->paginate(10);

        return view('other.enquiry.enquiry', compact('enquiry'));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Enquiry  $enquiry
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       $enquiry = Enquiry::where('id', $id)
        ->first();

        $enquiry->update([
            'status' => 1,
        ]);

        return view('other.enquiry.view_enquiry', compact('enquiry'));
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\CurrencyCode;
use App\Models\AdsCountry;

class ConntryController extends Controller
{

    public function CountryIndex(){

        $datas=Country::orderBy('name')->get();
        return view('country.country_index',compact('datas'));

    }
    public function CountryCreate(){

        $datas=Country::orderBy('name')->get();

        return view('country.country_add',compact('datas'));

    }

    public function CountryStore(Request $request){
      // return $request;
        $request->validate([
            'name'      => 'required|unique:countries,name',
            'code'      => 'required',
            'phonecode' => 'required',
            'phonelength' => 'required',
            'flag'        => 'required',
        ]);

        if($request->status){
            $status=1;
        }
        else{
            $status=0; 
        }

        if($request->hasFile('flag')){
            $file = uniqid().'.'.$request->flag->getClientOriginalExtension();

            $request->flag->storeAs('public/flag', $file);

            $flag = 'storage/flag/'.$file;
        }

        $data=new Country();
        $data->name=$request->name;
        $data->code=$request->code;
        $data->phonecode=$request->phonecode;
        $data->phone_length=$request->phonelength;
        $data->status=$status;
        $data->flag=$flag;
        $data->save();

        session()->flash('success', 'Country has been created');
        return redirect()->route('countries.index');

    }

    public function CountryEdit($id){
        $data=Country::find($id);
        return view('country.country_update',compact('data'));

    }

    public function CountryUpdate(Request $request,$id){
        $request->validate([
            'name'         => 'required|unique:countries,name,'.$id,
            'code'         => 'required',
            'phonecode'    => 'required',
            'phonelength' => 'required',

        ]);

        if($request->status){
            $status=1;
        }
        else{
            $status=0; 
        }

        $data=Country::find($id);

        
        if($request->hasFile('flag')){
            $file = uniqid().'.'.$request->flag->getClientOriginalExtension();

            $request->flag->storeAs('public/flag', $file);

            $flag = 'storage/flag/'.$file;
            $data->flag=$flag;

        }

        $data->name=$request->name;
        $data->code=$request->code;
        $data->phonecode=$request->phonecode;
        $data->phone_length=$request->phonelength;
        $data->status=$status;
        $data->update();

        session()->flash('success', 'Country has been updated');
        return redirect()->route('countries.index');
    }

    public function CountryDelete($id){
        Country::where('id', $id)->delete();

        session()->flash('success', 'Country has been deleted');
        return redirect()->route('countries.index');
    }


    public function CurrencyIndex(){

        $datas=CurrencyCode::orderBy('currency_name')->get();
        return view('currency.index',compact('datas'));

    }
    public function CurrencyCreate(){

        $datas=Country::orderBy('name')->get();

        return view('currency.add',compact('datas'));

    }


    public function CurrencyStore(Request $request){

        $request->validate([
            'currency_name'     => 'required',
            'currency_code'     => 'required',
            'currency_value'    => 'required',
            'country'        => 'required|unique:currency_codes,country_id',
        ]);

        $data=new CurrencyCode();
        $data->currency_name=$request->currency_name;
        $data->currency_code=$request->currency_code;
        $data->country_id=$request->country;
        $data->value=$request->currency_value;
        $data->save();

        session()->flash('success', 'Currency has been created');
        return redirect()->route('country_currency.index');

    }

    public function CurrencyEdit($id){
        $data=CurrencyCode::find($id);
        $countries=Country::orderBy('name')->get();

        return view('currency.update',compact('data','countries'));

    }

    public function CurrencyUpdate(Request $request,$id){
        $request->validate([
            'currency_name'     => 'required',
            'currency_code'     => 'required',
            'currency_value'    => 'required',
            'country'        => 'required|unique:currency_codes,country_id,'.$id,
        ]);

        $data=CurrencyCode::find($id);
        $data->currency_name=$request->currency_name;
        $data->currency_code=$request->currency_code;
        $data->country_id=$request->country;
        $data->value=$request->currency_value;
        $data->update();

        session()->flash('success', 'Currency has been updated');
        return redirect()->route('country_currency.index');
    }

    public function CurrencyDelete($id){
        CurrencyCode::where('id', $id)->delete();

        session()->flash('success', 'Currency has been deleted');
        return redirect()->route('country_currency.index');
    }

   
}

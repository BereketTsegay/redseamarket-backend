<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\State;
use App\Models\Country;

class CityStateController extends Controller
{

    public function stateIndex(){

        $sates=State::all();

        return view('state.state_index',compact('sates'));

    }
    public function stateCreate(){
        $countries=Country::all();
        return view('state.state_add',compact('countries'));

        
    }
    public function stateStore(Request $request){

        $data= new State();
        $data->name=$request->name;
        $data->country_id=$request->country;
        $data->save();
        session()->flash('success', 'State has been created');
        return back();
    }
    public function stateEdit($id){
        $data=State::find($id);
        $countries=Country::all();
        return view('state.state_update',compact('countries','data'));
    }
    public function stateUpdate(Request $request,$id){
        $data= State::find($id);
        $data->name=$request->name;
        $data->country_id=$request->country;
        $data->update();
        session()->flash('success', 'State has been updated');
        return back();
        
    }
    public function stateDelete($id){
        State::where('id', $id)->delete();

        session()->flash('success', 'State has been deleted');
        return redirect()->route('states.index');
    }


    #################### city ##############################3

    public function cityIndex(){
        $cities=City::all();
        return view('city.city_index',compact('cities'));
    }
    public function cityCreate(){
        $states=State::orderBy('name')->get();
        return view('city.city_add',compact('states'));  
    }
    public function cityStore(Request $request){
        $data= new City();
        $data->name=$request->name;
        $data->state_id=$request->state;
        $data->save();
        session()->flash('success', 'City has been created');
        return back();
    }
    public function cityEdit($id){
        $data=City::find($id);
        $states=State::orderBy('name')->get();
        return view('city.city_update',compact('states','data'));
    }
    public function cityUpdate(Request $request,$id){
        $data= City::find($id);
        $data->name=$request->name;
        $data->state_id=$request->state;
        $data->update();
        session()->flash('success', 'City has been updated');
        return back();
    }
    public function cityDelete($id){
        City::where('id', $id)->delete();

        session()->flash('success', 'City has been deleted');
        return redirect()->route('cities.index');  
    }
}

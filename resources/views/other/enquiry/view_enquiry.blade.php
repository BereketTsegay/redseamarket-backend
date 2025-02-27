@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Ads Enquiry Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('enquiry.index') }}">Ads Enquiry</a></li>
                <li class="breadcrumb-item active">Ads Enquiry Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $enquiry->customer_name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Email :</p>
                                    <p class="col-md-6">{{ $enquiry->email }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Phone :</p>
                                    <p class="col-md-6">{{ $enquiry->phone }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{{ $enquiry->status == 0 ? 'Not Readed' : 'Readed' }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Date :</p>
                                    <p class="col-md-6">{{ date('d-M-Y H:i:s A', strtotime($enquiry->created_at)) }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Message :</p>
                                    <p class="col-md-6">{{ $enquiry->message }}</p>
                                </div>
                                <div class="row">
                                    <a class="btn btn-primary col-md-3" href="{{url("/ad/view/$enquiry->id")}}">View Add</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection



@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Banner Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('banner.index') }}">Banner</a></li>
                <li class="breadcrumb-item active">Banner Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $banner->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Country :</p>
                                    <p class="col-md-6">{{ $banner->Country->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $banner->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ asset($banner->image) }}" target="blank"><img src="{{ asset($banner->image) }}" alt="image" width="250px"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

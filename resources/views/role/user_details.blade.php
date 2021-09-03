@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Admin User Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin_user.index') }}">Admin User</a></li>
                <li class="breadcrumb-item active">Admin User Details</li>
            </ol>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $user->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Email :</p>
                                    <p class="col-md-6">{{ $user->email }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Phone :</p>
                                    <p class="col-md-6">{{ $user->phone }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $user->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Role :</p>
                                    <p class="col-md-6">{{ $user->UserRole->Role->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

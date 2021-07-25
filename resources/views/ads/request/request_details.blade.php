@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Ad Request Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ad_request.index') }}">Ad Request</a></li>
                <li class="breadcrumb-item active">Ad Request Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    

@endpush
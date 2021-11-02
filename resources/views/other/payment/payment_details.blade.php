@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Payment Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('payment.index') }}">Payment</a></li>
                <li class="breadcrumb-item active">Payment Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Payment Id :</p>
                                    <p class="col-md-6">{{ $payment->payment_id }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Date :</p>
                                    <p class="col-md-6">{{ date('d-M-Y h:i:s A', strtotime($payment->created_at)) }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Amount :</p>
                                    <p class="col-md-6">{{ $payment->amount }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{{ $payment->status }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $payment->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Email :</p>
                                    <p class="col-md-6">{{ $payment->email }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Phone :</p>
                                    <p class="col-md-6">{{ $payment->phone }}</p>
                                </div>
                                @if($payment->ads_id != 0)
                                    <a href="{{ route('ads.view', $payment->ads_id) }}"><button class="btn btn-primary">View Ad</button></a>
                                @endif
                                <button class="btn btn-secondary" data-toggle="modal" data-target="#updateStatus">Update Status</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')

<div class="modal fade" id="updateStatus" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('payment.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Payment Status Update</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Status">Status</label>
                            <Select name="status" id="Status" class="form-control">
                                <option value="">Select</option>
                                <option {{ $payment->status == 'Payment started' ? 'selected' : '' }} value="Payment started">Payment started</option>
                                <option {{ $payment->status == 'Success' ? 'selected' : ''}} value="Success">Success</option>
                                <option {{ $payment->status == 'Payment pending' ? 'selected' : ''}} value="Payment pending">Payment pending</option>
                            </Select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endpush
@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Contact Us Enquiry Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('contact.index') }}">Contact Us Enquiry</a></li>
                <li class="breadcrumb-item active">Contact Us Enquiry Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $contact->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Email :</p>
                                    <p class="col-md-6">{{ $contact->email }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Phone :</p>
                                    <p class="col-md-6">{{ $contact->phone }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{{ $contact->status == 0 ? 'Not Readed' : 'Readed' }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Date :</p>
                                    <p class="col-md-6">{{ date('d-M-Y H:i:s A', strtotime($contact->created_at)) }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Message :</p>
                                    <p class="col-md-6">{{ $contact->message }}</p>
                                </div>
                                @if ($contact->replay)
                                    <div class="row">
                                        <p class="col-md-6">Replay :</p>
                                        <p class="col-md-6">{{ $contact->replay }}</p>
                                    </div>
                                @endif
                                <div class="row">
                                    <button class="btn btn-primary col-md-3" data-toggle="modal" data-target="#exampleModal">Replay</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    <div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="dialog">
        <div class="modal-content">
            <form action="{{ route('send.mail.replay', $contact->id) }}" method="post">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="Replay">Replay</label>
                        <textarea name="replay" id="Replay" cols="30" rows="5" placeholder="Email Replay" class="form-control"></textarea>
                    </div>
                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Replay</button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endpush

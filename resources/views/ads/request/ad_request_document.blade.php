@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Ad Request Document</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ad_request.index') }}">Ad Request</a></li>
                <li class="breadcrumb-item active">Ad Request Document</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            
                            <div class="col-md-12">
                                <h5 class="my-4">Image</h5>
                                <div class="row">
                                    @foreach ($ad->Image as $row) 
                                        <a href="{{ asset($row->image) }}" target="blank" class="col-md-4"><img class="img-thumbnail" src="{{ asset($row->image) }}" alt="image"></a>
                                    @endforeach
                                </div>

                                <div class="row mt-4">
                                    <p class="col-md-6">Transaction Id :  {{$ad->Payment->payment_id}}</p>
                                    <p class="col-md-6">Amount(USD) :  {{$ad->Payment->amount}}</p>
                                </div>
                                <form action="{{route('user.add.wallet')}}" method="POST">
                                    @csrf
                                    <input type="hidden" value="{{$ad->User->id}}" name="user_id">
                                <div class="row mt-4">
                                    
                                        <div class="form-group col-md-4">
                                            <label for="category">Ad Paid Amount(USD)</label>
                                            <input class="form-control" type="text" value="{{$ad->Payment->amount}}" readonly>
                                        </div>
                                     
                                        <div class="form-group col-md-4">
                                            <label for="category">Wallet Amount(USD)</label>
                                            <input class="form-control" type="text" name="wallet" value="{{$ad->User->wallet}}" >
                                        </div>
                                        <div class="form-group col-md-4 my-4">
                                            <button type="submit" class="btn btn-success">Submit</button>
                                        </div>

                                </div>
                            </form>
                                   
                            </div>
                        </div>
                      
                        
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')

@if (Session::has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            text: '{{ Session::get('success') }}',
        })
    </script>
@endif

@endpush
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
                               
                                 <p class="my-4" style="text-align: right;font-size: 20px;">
                                    <i class="fas fa-wallet"></i> ${{$ad->User->wallet}}</p>

                                <div class="row">
                                    {{-- @foreach ($ad->Image as $row) 
                                        <a href="{{ asset($row->image) }}" target="blank" class="col-md-4"><img class="img-thumbnail" src="{{ asset($row->image) }}" alt="image"></a>
                                    @endforeach --}}
                                    @if($ad->Payment)

                                    @foreach($ad->PaymentDoc as $payment)
                                    @if($payment->document)

                                    @if(Str::afterLast($payment->document, '.') == 'pdf') 
                                    <a href="{{ asset($payment->document) }}" target="blank" class="col-md-4">{{$loop->iteration}}-View Document</a>
                                    @else
                                    <a href="{{ asset($payment->document) }}" target="blank" class="col-md-4"><img class="img-thumbnail" src="{{ asset($payment->document) }}" alt="image"></a>
                                    @endif
                                     
                                    @endif
                                   @endforeach
                                  @endif
                                </div>

                                <div class="row mt-4">
                                    <p class="col-md-6">Transaction Id :   @foreach($ad->PaymentDoc as $ad_payment) {{$loop->iteration}} - {{$ad_payment->payment_id}}  @endforeach</p>
                                    <p class="col-md-3">Ad payment :  ${{$ad->Payment->amount}}</p>
                                    {{-- <p class="col-md-3">Wallet(USD) :  {{$ad->User->wallet}}</p> --}}
                                    
                                </div>
                                <form action="{{route('user.add.wallet')}}" method="POST">
                                    @csrf
                                    <input type="hidden" value="{{$ad->User->id}}" name="user_id">
                                <div class="row mt-4">
                                    <input type="hidden" name="wallet" value="{{$ad->User->wallet}}">
                                    <input type="hidden" name="ad_id" value="{{$ad->id}}">

                                                                            
                                        <div class="form-group col-md-4">
                                            <label for="category">Wallet Amount Add(+)</label>
                                            <input class="form-control" type="text" name="addwallet" value="0" >
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label for="category">Wallet Amount Cut(-)</label>
                                            <input class="form-control" type="text" name="cutwallet" value="0" >
                                        </div>
                                        <div class="form-group col-md-4 my-4">
                                            <button type="submit" class="btn btn-success">Verify/Accept</button>
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
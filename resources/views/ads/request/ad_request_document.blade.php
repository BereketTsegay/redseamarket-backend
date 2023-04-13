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
                                     {{-- {{dd(json_decode($payment->document))}} --}}
                                     @foreach(json_decode($payment->document) as $document)
                                    @if(Str::afterLast($document, '.') == 'pdf') 
                                    <a href="{{ asset($document) }}" target="blank" class="col-md-4">{{$loop->iteration}}-View Document</a>
                                    @else
                                    <a href="{{ asset($document) }}" target="blank" class="col-md-4"><img class="img-thumbnail" src="{{ asset($document) }}" alt="image"></a>
                                    @endif
                                     @endforeach
                                    @endif
                                   @endforeach
                                  @endif
                                </div>

                                <div class="row mt-4">
                                    <p class="col-md-6">Transaction Id :   @foreach($ad->PaymentDoc as $ad_payment) {{$loop->iteration}} - {{$ad_payment->payment_id}}  @endforeach</p>
                                    <p class="col-md-3">Ad payment : {{$ad->Currency->currency_code}} {{$ad->Payment->amount}}</p>
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
                                            <button type="button" onclick="rejectAd({{$ad->id}})" class="btn btn-danger my-1" data-toggle="modal" data-target="#rejectModal">Reject</button>

                                        </div>

                                </div>
                            </form>
                                   
                            </div>
                        </div>
                      
                        
                    </div>
                </div>
            </div>


            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    All Transactions
                </div>
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            
                            <div class="col-md-12">

                                <table id="datatablesSimple" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Transaction id</th>
                                            <th>Ad </th>
                                            <th>Action</th>
                                                                                        
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($payments as $payment)

                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$payment->payment_id}}</td>
                                            <td>{{$payment->ad->title}}</td>
                                            <td>
                                                @if(is_array(json_decode($payment->document)))
                                                 @foreach(json_decode($payment->document) as $document)
                                                <a href="{{asset($document)}}" target="_blank">view slip</a>
                                                @endforeach
                                                @else
                                                <a href="{{asset($payment->document)}}" target="_blank">view slip</a>

                                                @endif
                                            </td>
                                        </tr>

                                        @endforeach
                                        
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('reject.ads') }}" method="POST">
                            @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="rejectModalLabel">Reject Ad Request</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                {{-- <span aria-hidden="true">&times;</span> --}}
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="container">
                                <div class="form-group">
                                    <label for="Reason">Reason</label>
                                    <select name="reason" class="form-control" id="Reason" required>
                                        <option value="">Select</option>
                                        @foreach ($reason as $row3)
                                            <option value="{{ $row3->id }}">{{ $row3->reson }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="description">Note</label>
                                    <textarea name="description" class="form-control" id="description"  rows="3"></textarea>
                                </div>
                                <input type="hidden" name="ad_id" id="rejectAd_id">
                                <div class="form-group" id="reson_description">
                                    
                                </div>
                                {{-- <input type="hidden" name="ad_id" value="" id="rejectAd_id"> --}}
                                <div class="form-group" id="reson_description">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Reject</button>
                        </div>
                    </form>
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

<script>
     rejectAd = id => {
    $('#rejectAd_id').val(id);
    }

    $('#Reason').on('change', function(){
        let id = $(this).val();
        $.ajax({
            url: '/get/reject/reson',
            method: 'get',
            data:{id:id},
            success:function(data){
                let description = `<p class="my-2">${data.description}</p>`;

                $('#reson_description').html(description);
            }
        })
    });
</script>

@endpush
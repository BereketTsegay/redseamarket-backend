@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <h1 class="mt-4">Ad Request</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Ad Request</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header" id="myTab">
                    <ul class="nav nav-justified">
                        <li class="nav-item"><a data-toggle="tab" href="#tab-pending" class="nav-link active">Pending</a></li>
                        <li class="nav-item"><a data-toggle="tab" href="#tab-reject" class="nav-link">Rejected</a></li>
                        <li class="nav-item"><a data-toggle="tab" href="#tab-refund" class="nav-link">Refunded</a></li>
                    </ul>
                </div>
                {{-- <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Ad Request
                </div> --}}
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-pending" role="tabpanel">
                            <div class="col-md-12 text-right mb-2 float-end">
                                <form action="#" method="POST">
                                    @csrf
                                {{-- <button type="button" class="btn btn-outline-secondary table-btn" data-toggle="modal" data-target=".penfilterModal">Filter <i class="pe-7s-edit btn-icon-wrapper">
                                    </i></button> --}}
                                
                                {{-- <button type="submit" class="btn btn-outline-secondary table-btn ml-2">Export PDF <i class="pe-7s-note2 btn-icon-wrapper"> </i></button> --}}
                                </form>
                            </div>
                    <table class="table table-striped table-bordered">
                        <thead>
                            @if(count($adsRequest) == 0)
                            <p class="text-center">No data found !</p>
                            @else
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Title</th>
                                    <th>User</th>
                                    <th>Action</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @php
                                $i= ($adsRequest->currentpage() - 1 ) * $adsRequest->perpage() + 1;
                            @endphp
                            @foreach ($adsRequest as $row)
                                <tr>
                                    <th scope="row">{{ $i }}</th>
                                    <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                                    <td>{{ $row->Category->name }}</td>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->User->name }}</td>

                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu text-center">
                                                @if ($row->featured_flag && $row->Payment->payment_type != 0)
                                                    <a class="mb-2" href="{{ $row->Payment->document ? asset( $row->Payment->document ) : 'no-document' }}" target="blank"><button class="btn btn-warning">View Document</button></a>
                                                @endif
                                                <a href="{{ route('ad_request.details', $row->id) }}" ><button class="btn btn-secondary my-1">View</button></a>
                                                <form action="{{ route('ad.accept', $row->id) }}" method="POST">@csrf<button type="submit" class="btn btn-primary">Accept</button></form>
                                                <button type="button" onclick="rejectAd({{$row->id}})" class="btn btn-danger my-1" data-toggle="modal" data-target="#rejectModal">Reject</button>
                                                <button type="button" onclick="refundAd({{$row->id}})" class="btn btn-success my-1" data-toggle="modal" data-target="#refundModal">Refund</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                        {{ $adsRequest->links('pagination::bootstrap-4') }}
                    </table>
                        </div>

                        <div class="tab-pane" id="tab-reject" role="tabpanel">
                            <table class="table table-striped table-bordered">
                                <div class="col-md-12 text-right mb-2 float-end">
                                    <form action="#" method="POST">
                                        @csrf
                                    {{-- <button type="button" class="btn btn-outline-secondary table-btn" data-toggle="modal" data-target=".penfilterModal">Filter <i class="pe-7s-edit btn-icon-wrapper">
                                        </i></button> --}}
                                    
                                    {{-- <button type="submit" class="btn btn-outline-secondary table-btn ml-2">Export PDF <i class="pe-7s-note2 btn-icon-wrapper"> </i></button> --}}
                                    </form>
                                </div>
                                <thead>
                                    @if (count($adsRejected) == 0)
                                        <div class="text-center">No data found! </div>
                                    @else
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Title</th>
                                            <th>User</th>
                                            <th>Reject Reason</th>
                                            <th>View</th>
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    @php
                                        $i= ($adsRejected->currentpage() - 1 ) * $adsRejected->perpage() + 1;
                                    @endphp

                                    @foreach ($adsRejected as $row2)
                                        <tr>
                                            <th scope="row">{{ $i }}</th>
                                            <td>{{ date('d-m-Y', strtotime($row2->created_at)) }}</td>
                                            <td>{{ $row2->Category->name }}</td>
                                            <td>{{ $row2->title }}</td>
                                            <td>{{ $row2->User->name }}</td>
                                            <td class="w-50">{{ $row2->RejectionNote->reson }}</td>
                                            <td><a href="{{ route('ad_request.details', $row2->id) }}"><button class="btn btn-secondary">View</button></a></td>
                                            @php
                                                $i++;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{ $adsRejected->links('pagination::bootstrap-4') }}
                            </table>
                        </div>

                        <div class="tab-pane" id="tab-refund" role="tabpanel">
                            <table class="table table-striped table-bordered">
                                <div class="col-md-12 text-right mb-2 float-end">
                                    <form action="#" method="POST">
                                        @csrf
                                    {{-- <button type="button" class="btn btn-outline-secondary table-btn" data-toggle="modal" data-target=".penfilterModal">Filter <i class="pe-7s-edit btn-icon-wrapper">
                                        </i></button> --}}
                                    
                                    {{-- <button type="submit" class="btn btn-outline-secondary table-btn ml-2">Export PDF <i class="pe-7s-note2 btn-icon-wrapper"> </i></button> --}}
                                    </form>
                                </div>
                                <thead>
                                    @if (count($adsRefund) == 0)
                                        <div class="text-center">No data found! </div>
                                    @else
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Category</th>
                                            <th>Title</th>
                                            <th>User</th>
                                            <th>Reject Reason</th>
                                            <th>View</th>
                                        </tr>
                                    @endif
                                </thead>
                                <tbody>
                                    @php
                                        $i= ($adsRefund->currentpage() - 1 ) * $adsRefund->perpage() + 1;
                                    @endphp

                                    @foreach ($adsRefund as $row2)
                                        <tr>
                                            <th scope="row">{{ $i }}</th>
                                            <td>{{ date('d-m-Y', strtotime($row2->created_at)) }}</td>
                                            <td>{{ $row2->Category->name }}</td>
                                            <td>{{ $row2->title }}</td>
                                            <td>{{ $row2->User->name }}</td>
                                            <td class="w-50">{{ $row2->RejectionNote->reson }}</td>
                                            <td><a href="{{ route('ad_request.details', $row2->id) }}"><button class="btn btn-secondary">View</button></a></td>
                                            @php
                                                $i++;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                                {{ $adsRefund->links('pagination::bootstrap-4') }}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 

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
                            <select name="reason" class="form-control" id="Reason">
                                <option value="">Select</option>
                                @foreach ($reason as $row3)
                                    <option value="{{ $row3->id }}">{{ $row3->reson }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="ad_id" value="" id="rejectAd_id">
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

    <div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('refund.ads') }}" method="POST">
                    @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">Refund Ad Payment</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        {{-- <span aria-hidden="true">&times;</span> --}}
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group">
                            <label for="Reason">Reason</label>
                            <select name="reason" class="form-control" id="Reason">
                                <option value="">Select</option>
                                @foreach ($refund as $row4)
                                    <option value="{{ $row4->id }}">{{ $row4->reson }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="ad_id" id="refundAd_id">
                        <div class="form-group" id="reson_description">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Refund</button>
                </div>
            </form>
            </div>
        </div>
    </div>

@endsection

@push('script')

<script>

    rejectAd = id => {

        $('#rejectAd_id').val(id);
    }

    refundAd = id => {
        $('#refundAd_id').val(id);
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

@if (Session::has('success'))
    <script>
        Swal.fire({
            icon: 'success',
            text: '{{ Session::get('success') }}',
        })
    </script>
@endif

@if (Session::has('error'))
    <script>
        Swal.fire({
            icon: 'error',
            text: '{{ Session::get('error') }}',
        })
    </script>
@endif
@endpush
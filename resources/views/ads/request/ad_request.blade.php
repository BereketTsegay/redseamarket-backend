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
                                <button type="button" class="btn btn-outline-secondary table-btn" data-toggle="modal" data-target=".penfilterModal">Filter <i class="pe-7s-edit btn-icon-wrapper">
                                    </i></button>
                                
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
                                    <th>View</th>
                                    <th>Accept</th>
                                    <th>Reject</th>
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
                                    <td><a href="{{ route('ad_request.details', $row->id) }}"><button class="btn btn-secondary">View</button></a></td>
                                    <td><form action="{{ route('ad.accept', $row->id) }}" method="POST">@csrf<button type="submit" class="btn btn-primary">Accept</button></form></td>
                                    <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">Reject</button></td>
                                </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                        </div>

                        <div class="tab-pane" id="tab-reject" role="tabpanel">
                            <table class="table table-striped table-bordered">
                                <div class="col-md-12 text-right mb-2 float-end">
                                    <form action="#" method="POST">
                                        @csrf
                                    <button type="button" class="btn btn-outline-secondary table-btn" data-toggle="modal" data-target=".penfilterModal">Filter <i class="pe-7s-edit btn-icon-wrapper">
                                        </i></button>
                                    
                                    {{-- <button type="submit" class="btn btn-outline-secondary table-btn ml-2">Export PDF <i class="pe-7s-note2 btn-icon-wrapper"> </i></button> --}}
                                    </form>
                                </div>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Category</th>
                                        <th>Title</th>
                                        <th>User</th>
                                        <th>Reject Reason</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>Tiger Nixon</td>
                                        <td>System Architect</td>
                                        <td>61</td>
                                        <td>dsafdsf</td>
                                        <td class="w-50">fdsaf</td>
                                        <td><a href="#"><button class="btn btn-secondary">View</button></a></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td>Tiger Nixon</td>
                                        <td>System Architect</td>
                                        <td>61</td>
                                        <td>fdsaf</td>
                                        <td class="w-50">fdsaf</td>
                                        <td><a href="#"><button class="btn btn-secondary">View</button></a></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">3</th>
                                        <td>Tiger Nixon</td>
                                        <td>System Architect</td>
                                        <td>61</td>
                                        <td>fdsaf</td>
                                        <td class="w-50">fdsaf</td>
                                        <td><a href="#"><button class="btn btn-secondary">View</button></a></td>
                                    </tr>
                                </tbody>
                            </table>
                                </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
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
                                <option value="{{ $row3->id }}">{{ $row3->reason }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="reson_description">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Reject</button>
            </div>
        </div>
    </div>
  </div>

{{-- Reject Filter --}}
  <div class="modal fade rejfilterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" id="menuRejFilt" method="GET">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    {{-- <span aria-hidden="true">&times;</span> --}}
                </button>
            </div>
            <div class="modal-body row">
                <div class="position-relative col-md-6 form-group">
                    <div class="row">
                        <label class="col-sm-12 col-form-label label">From Date</label>
                        <div class="col-sm-12"><input type="text" id="fromDate" name="fromDate" class="form-control date-input" placeholder="Select Date" data-toggle="datepicker" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="position-relative col-md-6 form-group">
                    <div class="row">
                        <label class="col-sm-12 col-form-label label">To Date</label>
                        <div class="col-sm-12"><input type="text" id="toDate" name="toDate" class="form-control date-input" placeholder="Select Date" data-toggle="datepicker" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="location.reload()" class="btn btn-secondary" data-dismiss="modal">Reset</button>
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- Pending Filter --}}
<div class="modal fade penfilterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" id="menuRejFilt" method="GET">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    {{-- <span aria-hidden="true">&times;</span> --}}
                </button>
            </div>
            <div class="modal-body row">
                <div class="position-relative col-md-6 form-group">
                    <div class="row">
                        <label class="col-sm-12 col-form-label label">From Date</label>
                        <div class="col-sm-12"><input type="text" id="fromDate" name="fromDate" class="form-control date-input" placeholder="Select Date" data-toggle="datepicker" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="position-relative col-md-6 form-group">
                    <div class="row">
                        <label class="col-sm-12 col-form-label label">To Date</label>
                        <div class="col-sm-12"><input type="text" id="toDate" name="toDate" class="form-control date-input" placeholder="Select Date" data-toggle="datepicker" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="location.reload()" class="btn btn-secondary" data-dismiss="modal">Reset</button>
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
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
            text: {{ Session::get('success') }},
        })
    </script>
@endif

@if (Session::has('error'))
    <script>
        Swal.fire({
            icon: 'error',
            text: {{ Session::get('error') }},
        })
    </script>
@endif
@endpush
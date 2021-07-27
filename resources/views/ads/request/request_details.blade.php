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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Title :</p>
                                    <p class="col-md-6">{{ $ad->title }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Canonical Name :</p>
                                    <p class="col-md-6">{{ $ad->canonical_name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Category :</p>
                                    <p class="col-md-6">{{ $ad->Category->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Subcategory :</p>
                                    <p class="col-md-6">{{ $ad->Subcategory->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Country :</p>
                                    <p class="col-md-6">{{ $ad->Country->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">State :</p>
                                    <p class="col-md-6">{{ $ad->State->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">City :</p>
                                    <p class="col-md-6">{{ $ad->City->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Price :</p>
                                    <p class="col-md-6">{{ $ad->price }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $ad->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Negotiable :</p>
                                    <p class="col-md-6">{!! $ad->negotiable_flag == 1 ? '<span class="text-success">Yes</span>' : '<span class="text-secondary">No</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Featured :</p>
                                    <p class="col-md-6">{!! $ad->featured_flag == 1 ? '<span class="text-success">Yes</span>' : '<span class="text-secondary">No</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Description :</p>
                                    <p class="col-md-6">{{ $ad->description }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Created By :</p>
                                    <p class="col-md-6">
                                        @if($ad->customer_id == 0)
                                            Admin
                                        @else
                                            {{ $ad->User->name }}
                                        @endif
                                    </p>
                                </div>
                                @foreach ($ad->CustomValue as $item)
                                    <div class="row">
                                        <p class="col-md-6">{{ $item->Field->name }} :</p>
                                        <p class="col-md-6">{{ $item->value }}</p>
                                    </div>
                                @endforeach

                                @foreach ($ad->AdsFieldDependency as $row1)
                                    @if($row1->master_type == 'make')
                                        <div class="row">
                                            <p class="col-md-6">Make :</p>
                                            <p class="col-md-6">{{ $row1->Make->name }}</p>
                                        </div>
                                    @elseif($row1->master_type == 'model')
                                        <div class="row">
                                            <p class="col-md-6">Model :</p>
                                            <p class="col-md-6">{{ $row1->Model->name }}</p>
                                        </div>
                                    @elseif($row1->master_type == 'variant')
                                        <div class="row">
                                            <p class="col-md-6">Variant :</p>
                                            <p class="col-md-6">{{ $row1->Variant->name }}</p>
                                        </div>
                                    @elseif($row1->master_type == 'country')
                                        <div class="row">
                                            <p class="col-md-6">Country :</p>
                                            <p class="col-md-6">{{ $row1->Country->name }}</p>
                                        </div>
                                    @elseif($row1->master_type == 'state')
                                        <div class="row">
                                            <p class="col-md-6">State :</p>
                                            <p class="col-md-6">{{ $row1->State->name }}</p>
                                        </div>
                                    @elseif($row1->master_type == 'city')
                                        <div class="row">
                                            <p class="col-md-6">City :</p>
                                            <p class="col-md-6">{{ $row1->City->name }}</p>
                                        </div>
                                    @endif
                                @endforeach

                                @if($ad->sellerinformation_id != 0)
                                    <hr>
                                    <h5>Seller Details</h5>
                                    <hr>
                                    <div class="row">
                                        <p class="col-md-6">Name :</p>
                                        <p class="col-md-6">{{ $ad->SellerInformation->name }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Email :</p>
                                        <p class="col-md-6">{{ $ad->SellerInformation->email }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Phone :</p>
                                        <p class="col-md-6">{{ $ad->SellerInformation->phone }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Address :</p>
                                        <p class="col-md-6">{{ $ad->SellerInformation->address }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5 class="my-4">Image</h5>
                                <div class="row">
                                    @foreach ($ad->Image as $row) 
                                        <a href="{{ asset($row->image) }}" target="blank" class="col-md-4"><img class="img-thumbnail" src="{{ asset($row->image) }}" alt="image"></a>
                                    @endforeach
                                </div>
                                
                            </div>
                        </div>
                        <form action="{{ route('ad.accept', $ad->id) }}" method="POST">@csrf
                            <button type="submit" class="btn btn-primary my-4">Accept</button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">Reject</button>
                        </form>
                        
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
@endpush
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
                                    <p class="col-md-6">{!! $ad->Subcategory ? $ad->Subcategory->name : '<span class="text-secondary">Null</span>' !!}</p>
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
                                    <p class="col-md-6">{{ $ad->City ? $ad->City->name : $ad->State->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Price :</p>
                                    <p class="col-md-6">{{ $ad->price }}</p>
                                </div>
                                @if($ad->featured_flag && $ad->Payment->payment_type != 0)
                                    @if($ad->Payment) 
                                        <div class="row">
                                            <p class="col-md-6">Payment Document :</p>
                                            <p class="col-md-6"><a href="{{ asset($ad->Payment->document ? $ad->Payment->document : '/no-document') }}" target='blank'>View Document</a></p>
                                        </div>
                                    @endif
                                @endif
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $ad->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Negotiable :</p>
                                    <p class="col-md-6">{!! $ad->negotiable_flag == 1 ? 'Yes' : 'No' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Featured :</p>
                                    <p class="col-md-6">{!! $ad->featured_flag == 1 ? 'Yes' : 'No' !!}</p>
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

                                @if ($ad->category_id == 1)
                                <div class="row">
                                    <p class="col-md-6">Make :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->Make->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Model :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->Model->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Variant :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->Variant->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Registration Year :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->registration_year }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Fuel :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->fuel_type }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Transmission :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->transmission }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Condition :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->condition }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Mileage :</p>
                                    <p class="col-md-6">{{ $ad->MotoreValue->milage }}</p>
                                </div>
                                    @if (count($ad->MotorFeatures) != 0)
                                    <div class="">
                                        <h4>Features</h4>
                                        <hr>
                                        @foreach ($ad->MotorFeatures as $feature)
                                            @if ($feature->value != '0')
                                                <div class="row">
                                                    <p class="col-md-6 capitalize">{{ $feature->value }}</p>
                                                    <p class="col-md-6">Yes</p>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    @endif
                                @elseif ($ad->category_id == 2)
                                    <div class="row">
                                        <p class="col-md-6">Size :</p>
                                        <p class="col-md-6">{{ $ad->PropertyRend->size }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Rooms :</p>
                                        <p class="col-md-6">{{ $ad->PropertyRend->room }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Furnished :</p>
                                        <p class="col-md-6">{{ $ad->PropertyRend->furnished }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Building Type :</p>
                                        <p class="col-md-6">{{ $ad->PropertyRend->building_type }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Parking :</p>
                                        <p class="col-md-6">{{ $ad->PropertyRend->parking == 1 ? 'Yes' : 'No' }}</p>
                                    </div>

                                @elseif ($ad->category_id == 3)
                                    <div class="row">
                                        <p class="col-md-6">Size :</p>
                                        <p class="col-md-6">{{ $ad->PropertySale->size }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Rooms :</p>
                                        <p class="col-md-6">{{ $ad->PropertySale->room }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Furnished :</p>
                                        <p class="col-md-6">{{ $ad->PropertySale->furnished }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Building Type :</p>
                                        <p class="col-md-6">{{ $ad->PropertySale->building_type }}</p>
                                    </div>
                                    <div class="row">
                                        <p class="col-md-6">Parking :</p>
                                        <p class="col-md-6">{{ $ad->PropertySale->parking == 1 ? 'Yes' : 'No' }}</p>
                                    </div>
                                @endif

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

                                @if($ad->Payment->document)   
                                    
                                @else
                                    
                                    <form action="{{ route('payment.document.upload', $ad->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">Upload Document :</div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="file" class="form-control @error('document') is-invalid @enderror" name="document">
                                                    @error('document')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-success">Upload</button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                @endif
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
                        @if($ad->status == 0)
                            <form action="{{ route('ad.accept', $ad->id) }}" method="POST">@csrf
                                <button type="submit" class="btn btn-primary my-4">Accept</button>
                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">Reject</button>
                            </form>
                        @endif
                        
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
                    <input type="hidden" name="ad_id" value="{{ $ad->id }}">
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
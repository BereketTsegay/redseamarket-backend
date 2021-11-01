@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Ad Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ads.index') }}">Ads</a></li>
                <li class="breadcrumb-item active">Ad Details</li>
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
                                    <p class="col-md-6">{{ $ad->Subcategory ? $ad->Subcategory->name : 'Null' }}</p>
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
                                    @if ($ad->city_id != 0)
                                    <p class="col-md-6">{{ $ad->City->name }}</p>
                                    @else
                                    <p class="col-md-6">{{ $ad->State->name }}</p>
                                    @endif
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
                                            <div class="row">
                                                <p class="col-md-6">{{ $feature->value }}</p>
                                            </div>
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
                                        @if ($item->file == 1)
                                        <a class="col-md-6" href="{{ asset($item->value) }}" target="blank" style="text-decoration: none;"><p>View File</p></a>
                                        @else
                                        <p class="col-md-6">{{ $item->value }}</p>
                                        @endif
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
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="deleteCustomField()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>
    
    <script>
        let ids = '';

        customFieldDelete = (id) => {
            ids = id;
        }

        deleteCustomField = () => {
            $('#custom_field_delete_form'+ids).submit();
        }
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
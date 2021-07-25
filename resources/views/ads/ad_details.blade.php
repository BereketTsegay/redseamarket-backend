@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Category Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
                <li class="breadcrumb-item active">Category Details</li>
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
                                <hr>
                                <h5>Seller Details</h5>
                                <hr>
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $ad->description }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Email :</p>
                                    <p class="col-md-6">{{ $ad->description }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Phone :</p>
                                    <p class="col-md-6">{{ $ad->description }}</p>
                                </div>
                                @if($ad->customer_id != 0)
                                    
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5 class="my-4">Image</h5>
                                <hr>
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
@endpush
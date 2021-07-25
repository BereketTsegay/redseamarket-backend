@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Subcategory Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('subcategory.index') }}">Subcategory</a></li>
                <li class="breadcrumb-item active">Subcategory Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $subcategory->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Canonical Name :</p>
                                    <p class="col-md-6">{{ $subcategory->canonical_name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Category :</p>
                                    @if($subcategory->category_id == 0)
                                        <p class="col-md-6">{{ $subcategory->SubcategoryReverse->name }}</p>
                                    @else
                                        <p class="col-md-6">{{ $subcategory->Category->name }}</p>
                                    @endif
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Type :</p>
                                    <p class="col-md-6">{{ $subcategory->type == 0 ? 'Flat' : 'Percentage' }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Value :</p>
                                    <p class="col-md-6">{{ $subcategory->percentage }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Sort Order :</p>
                                    <p class="col-md-6">{{ $subcategory->sort_order }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $subcategory->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Description :</p>
                                    <p class="col-md-6">{{ $subcategory->description }}</p>
                                </div>
                                <hr>
                                <h5>Custom Fields</h5>
                                <hr>
                                @foreach ($subcategory->CustomField as $row)
                                    <div class="row">
                                        <p class="col-md-6">{{ $row->Field->name }} :</p>
                                        <p class="col-md-6">{{ $row->Field->type }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-6">
                                <h5 class="my-4">Image</h5>
                                <hr>
                                <a href="{{ asset($subcategory->image) }}" target="blank"><img src="{{ asset($subcategory->image) }}" alt="image" width="250px"></a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    

@endpush
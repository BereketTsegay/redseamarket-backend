@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit Ads</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ads.index') }}">Ads</a></li>
                <li class="breadcrumb-item active">Edit Ads</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                @csrf
                                <div class="row">
                                    <div class="form-group my-2 col-md-6">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" autocomplete="off">
                                            @foreach ($category as $row)
                                                @if ($row->id == $ad->category_id)
                                                    <option selected value="{{ $row->id }}">{{ $row->name }}</option>
                                                @else
                                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                                @endif
                                            @endforeach
                                           
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('category')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="subcategory">Subcategory (Optional)</label>
                                        <select name="subcategory" id="subcategory" class="form-control @error('subcategory') is-invalid @enderror" autocomplete="off">
                                            <option value="{{ $ad->subcategory_id }}">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('subcategory')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group my-2 col-md-6">
                                        <label for="Title">Title</label>
                                        <input type="text" name="title" value="{{ $ad->title }}" class="slug form-control {{ Session::has('title_error') ? 'is-invalid' : '' }}r" placeholder="Title" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @if (Session::has('title_error'))
                                                {{ Session::get('title_error') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="CanonicalName">Canonical Name</label>
                                        <input type="text" id="canonical_name" name="canonical_name" value="{{ $ad->canonical_name }}" class="form-control @error('canonical_name') is-invalid @enderror" placeholder="Canonical Name" autocomplete="off" readonly>
                                        <div class="invalid-feedback">
                                            @error('canonical_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group my-2 col-md-6">
                                        <label for="Title">Title in Arabic</label>
                                        <input type="text" name="arabic_title" value="{{ $ad->title_arabic }}" class="slug form-control {{ Session::has('title_error') ? 'is-invalid' : '' }}" placeholder="Title in Arabic" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @if (Session::has('title_error'))
                                                {{ Session::get('title_error') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="country">Country</label>
                                        <select name="country" id="country" class="select2 form-control @error('country') is-invalid @enderror" autocomplete="off">
                                            @foreach ($country as $row1)
                                                @if ($ad->country_id == $row1->id)
                                                    <option selected value="{{ $row1->id }}">{{ $row1->name }}</option>
                                                @else
                                                    <option value="{{ $row1->id }}">{{ $row1->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('country')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group my-2 col-md-6">
                                        <label for="Price">Price</label>
                                        <input type="number" name="price" value="{{ $ad->price }}" class="form-control @error('price') is-invalid @enderror" placeholder="Price" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('price')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="state">State</label>
                                        <select name="State" id="state" class="select2  form-control @error('state') is-invalid @enderror" autocomplete="off">
                                            <option value="{{ $ad->state_id }}">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('state')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group my-2 col-md-6">
                                        <label for="Image">Image (Multiple)</label>
                                        <input type="file" name="image[]" autocomplete="off" class="form-control @error('image') is-invalid @enderror" accept=".png, .jpeg, .jpg" multiple>
                                        <div style="color: red;"><strong>Warning: </strong> Maximum of 5 images are allowed!</div>
                                        <div class="invalid-feedback">
                                            @error('image')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="city">City</label>
                                        <select name="city" id="city" class="select2  form-control @error('city') is-invalid @enderror" autocomplete="off">
                                            <option value="{{ $ad->city_id }}">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('city')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group my-2 col-md-6">
                                        <label for="Description">Description</label>
                                        <textarea name="description" class="form-control {{ Session::has('description_error') ? 'is-invalid' : '' }}" cols="30" rows="3" placeholder="Description" autocomplete="off">{{ $ad->description }}</textarea>
                                        <div class="invalid-feedback">
                                            @if (Session::has('description_error'))
                                                {{ Session::get('description_error') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="Description">Description in Arabic</label>
                                        <textarea name="description_arabic" class="form-control {{ Session::has('description_error') ? 'is-invalid' : '' }}" cols="30" rows="3" placeholder="Description in Arabic" autocomplete="off">{{ $ad->description_arabic }}</textarea>
                                        <div class="invalid-feedback">
                                            @if (Session::has('description_error'))
                                                {{ Session::get('description_error') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group my-2">
                                                <label for="Status">Active</label>
                                                <input type="checkbox" name="status" checked value="checked" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group my-2">
                                                <label for="Status">Price Negotiable</label>
                                                <input type="checkbox" name="negotiable" {{ $ad->negotiable_flag == 1 ? 'checked' : '' }} value="checked" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group my-2">
                                                <label for="Status">Featured</label>
                                                <input type="checkbox" name="featured" {{ $ad->featured_flag == 1 ? 'checked' : '' }} value="checked" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="custom_fields">
                                    
                                </div>

                                <hr class="my-2">
                                <h5>Seller information</h5>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="SellerName">Seller Name</label>
                                        <input type="text" name="seller_name" value="{{ $ad->SellerInformation ? $ad->SellerInformation->name : '' }}" class="form-control @error('seller_name') is-invalid @enderror" placeholder="Seller Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('seller_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Phone">Phone</label>
                                        <input type="phone" name="phone" value="{{ $ad->SellerInformation ? $ad->SellerInformation->phone : '' }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('phone')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group my-2">
                                            <label for="PhoneHide">Phone Hide</label>
                                            <input type="checkbox" {{ $ad->SellerInformation ? $ad->SellerInformation->phone_hide_flag == 1 ? 'checked' : '' : ''}} name="phone_hide_flag" value="checked" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="Email">Email</label>
                                        <input type="email" name="email" value="{{ $ad->SellerInformation ? $ad->SellerInformation->email : ''}}" class="form-control @error('email') is-invalid @enderror" placeholder="Email" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Address">Address</label>
                                        <textarea name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" cols="30" rows="3" placeholder="Address" autocomplete="off">{{ $ad->SellerInformation ? $ad->SellerInformation->address : '' }}</textarea>
                                        <div class="invalid-feedback">
                                            @error('customer_address')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3">
                                <div class="position-relative row form-group">
                                    <div class="col-lg-2 col-md-4">
                                        <p class="label">Location <span class="float-right d-none d-md-block d-lg-block"></span></p></label>
                                    </div>
                                    <div class="col-lg-10 col-md-8">
                                        <input class="form-control map-input" value="{{ old('address') }}" id="address-input" name="address" placeholder="Enter Location">
                                        @error('address')
                                            <span class="help-block text-danger">
                                                {{ $message }} 
                                            </span>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="address_latitude" value="{{ old('address_latitude') ?? 0 }}" id="address-latitude">
                                    <input type="hidden" name="address_longitude" value="{{ old('address_longitude') ?? 0  }}" id="address-longitude">
                                </div>
                                <div class="my-4" id="address-map-container" style="width:100%;height:400px; ">
                                    <div style="width: 100%; height: 100%" id="address-map"></div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary my-3">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')

    <script>

        $('.slug').keyup(function() {
            $('#canonical_name').val(getSlug($(this).val()));
        });

        function getSlug(str) {
            return str.toLowerCase().replace(/ +/g, '-').replace(/[^-\w]/g, '');
        }

        $('form[id="adStoreForm"]').validate({
            rules : {
                category: {
                        required : true,
                    },
                title: {
                        required: true,
                    },
                price: {
                        required: true,
                    },
                state: {
                        required: true,
                    },
                'image[]': {
                        required: true,
                        extension: "jpg|jpeg|png",
                    },
                country:{
                        required: true,
                    },
                description: {
                        required: true,
                    },


                make: {
                        required: true,
                    },
                model: {
                        required: true,
                    },
                varient: { 
                        required: true,
                    },
                registered_year: {
                        required: true,
                        number: true
                },
                fuel: {
                        required: true,
                    },
                transmission: {
                        required: true,
                    },
                condition: {
                        required: true,
                    },
                mileage: {
                        required: true,
                        number: true
                    },

                size: {
                        required: true,
                        number: true
                    },
                rooms: {
                        required: true,
                        number: true
                    },
                furnished: {
                        required: true,
                    },
                building: {
                        required: true,
                    },
                },

            
            submitHandler: function(form) {
                form.submit();
            }
        });


        $(document).ready(function() {
            $('.select2').select2();
        });

        $(document).on('change', '#Make', function(){
            let id = $(this).val();
            let newOption = '';

            $.ajax({
                url : '/global/vehicle/model/get',
                type : 'get',
                data : {id:id},
                success:function(data){
                    newOption += `<option value="">Select Model</option>`;

                    for(let i = 0; i < data.length; i++){

                        newOption += `<option value="${data[i].id}">${data[i].name}</option>`;

                    }

                    $('#Model').html(newOption);
                }
            });

        });

        $(document).on('change', '#Model', function(){
            let id = $(this).val();
            let newOption = '';

            $.ajax({
                url : '/global/vehicle/varient/get',
                type : 'get',
                data : {id:id},
                success:function(data){
                    newOption += `<option value="">Select Varient</option>`;

                    for(let i = 0; i < data.length; i++){

                        newOption += `<option value="${data[i].id}">${data[i].name}</option>`;

                    }

                    $('#Varient').html(newOption);
                }
            });

        });

        $(document).on('change', '#category', function(){
            let id = $(this).val();
            let option = '';
            let custom_field = '';
            let select_id = '';
            let dependencyOption = '';
            let makeOption = '';


            if(id == 1){

                $.ajax({
                    url : '/get/master/dependency',
                    async : false,
                    type : 'get',
                    data : {master:'Make'},
                    success:function(result){
                        
                        
                        makeOption += '<option value="">Select</option>';
                        
                        for(let i = 0; i < result.length; i++){
                            makeOption += `<option value="${result[i].id}">${result[i].name}</option>`;
                        }
                    }
                });

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Make">Make </label>
                                    <select class="select2 form-control @error('make') 'is-invalid' @enderror" name="make" id="Make">
                                        ${makeOption}
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('make')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Model">Model </label>
                                    <select class="form-control @error('model') 'is-invalid' @enderror" name="model" id="Model">
                                        <option>Select Model</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('model')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Varient">Variant </label>
                                    <select class="form-control @error('varient') 'is-invalid' @enderror" name="varient" id="Varient">
                                        <option>Select Variant</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('varient')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="RegisterdYear">Registerd Year </label>
                                    <input type="number" class="form-control @error('registered_year') 'is-invalid' @enderror" name="registered_year" id="RegisterdYear" placeholder="Registerd Year" >
                                    <div class="invalid-feedback">
                                        @error('registered_year')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Fuel">Fuel Type </label>
                                    <select class="form-control @error('fuel') 'is-invalid' @enderror" name="fuel" id="Fuel">
                                        <option value="" >Select</option>
                                        <option value="Petrol">Petrol</option>
                                        <option value="Diesel">Diesel</option>
                                        <option value="LPG Gas">LPG Gas</option>
                                        <option value="Electric">Electric</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('fuel')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row">
                                    <label for="Transmission">Transmission </label>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Manual">Manual </label>
                                        <input type="radio" class="" name="transmission" id="Manual" value="Manual" >
                                    </div>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Automatic">Automatic </label>
                                        <input type="radio" class="@error('transmission') 'is-invalid' @enderror" name="transmission" id="Automatic" value="Automatic" >
                                        <div class="invalid-feedback">
                                            @error('transmission')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row mx-2">
                                    <label for="Condition">Condition </label>
                                    <div class="custom-form-control col-md-6">
                                        <label for="New">New </label>
                                        <input type="radio" class="" name="condition" id="New" value="New" >
                                    </div>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Used">Used </label>
                                        <input type="radio" class="@error('condition') 'is-invalid' @enderror" name="condition" id="Used" value="Used" >
                                        <div class="invalid-feedback">
                                            @error('condition')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Mileage">Mileage </label>
                                    <input type="number" class="form-control @error('mileage') 'is-invalid' @enderror" name="mileage" id="Mileage" placeholder="Mileage" >
                                    <div class="invalid-feedback">
                                        @error('mileage')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row mx-2">
                                    <label for="Transmission">Features </label>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox" class="" name="features[]" id="AirConditioner" value="Air Conditioner" >
                                        <label for="AirConditioner">Air Conditioner </label>
                                    </div>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox" class="" name="features[]" id="GPS" value="GPS" >
                                        <label for="GPS">GPS </label>
                                    </div>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox" class="" name="features[]" id="SecuritySystem" value="Security System" >
                                        <label for="SecuritySystem">Security System </label>
                                    </div>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox" class="@error('features') 'is-invalid' @enderror" name="features[]" id="SpareTire" value="Spare Tire" >
                                        <label for="SpareTire" >Spare Tire </label>
                                        <div class="invalid-feedback">
                                            @error('features')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

            }
            else if(id == 2 || id == 3){
                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Size">Size </label>
                                    <input type="number" class="form-control @error('size') 'is-invalid' @enderror" name="size" id="Size" placeholder="Size" >
                                    <div class="invalid-feedback">
                                        @error('size')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Room">Rooms </label>
                                    <input type="number" class="form-control @error('rooms') 'is-invalid' @enderror" name="rooms" id="Room" placeholder="Rooms" >
                                    <div class="invalid-feedback">
                                        @error('rooms')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row mx-2">
                                    <label for="Furnished">Furnished </label>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Yes">Yes </label>
                                        <input type="radio" class="@error('furnished') 'is-invalid' @enderror" name="furnished" id="Yes" value="Yes" >
                                        <div class="invalid-feedback">
                                            @error('furnished')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="custom-form-control col-md-6">
                                        <label for="No">No </label>
                                        <input type="radio" class="@error('furnished') 'is-invalid' @enderror" name="furnished" id="No" value="No" >
                                        <div class="invalid-feedback">
                                            @error('furnished')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Building">Building Type </label>
                                    <select class="form-control @error('building') 'is-invalid' @enderror" name="building" id="Building">
                                        <option value="" >Select</option>
                                        <option value="Apartment">Apartment</option>
                                        <option value="House">House</option>
                                        <option value="Store">Store</option>
                                        <option value="Office">Office</option>
                                        <option value="Plot of land">Plot of land</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('building')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 my-2 row mx-2">
                                    <div class="custom-form-control col-md-12">
                                        <label for="Parking">Parking </label>
                                        <input type="checkbox" class="custom-control-input @error('parking') 'is-invalid' @enderror" name="parking" id="Parking" value="Parking" >
                                        <div class="invalid-feedback">
                                        @error('parking')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    </div>
                                </div>`;
            }

            $.ajax({
                url : '/change/subcategory',
                type : 'get',
                data : {id:id},
                success:function(data){

                    option += `<option value="">Select</option>`;

                    for(let i = 0; i < data.length; i++){
                        option += `<option value="${data[i].id}">${data[i].name}</option>`;
                        for(let j = 0; j < data[i].subcategory_child.length; j++){
                            option += `<option value="${data[i].subcategory_child[j].id}"> -----| ${data[i].subcategory_child[j].name}</option>`;
                        }
                    }

                    $('#subcategory').html(option);
                }
            });

            $.ajax({
                url : '/get/custom/field',
                type : 'get',
                data : {id:id},
                success:function(data){
                    
                    // let selectoption  = '<option value="">Select</option>';
                    // let identity = 'select_identity';

                    for(let i = 0; i < data.length; i++){
                        
                        // for(let j = 0; j < data[i].field.length; j++){
                            
                            switch (data[i].field.type){
                                case 'text':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                        </div>`;
                                    break;
                                case 'textarea':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <textarea type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}></textarea>
                                        </div>`;
                                    break;
                                case 'checkbox':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="checkbox" class="" name="${data[i].field.name}" value="checked" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                        </div>`;
                                    break;
                                // case 'checkbox_multiple':
                                //     for(let k = 0; k < data[i].field.field_option.length; k++){
                                //         custom_field += `<div class="form-group col-md-6 my-2">
                                //                             <div class="col-md-6">
                                //                                 <label for="">${data[i].field.field_option[k].value} </label>
                                //                                 <input type="checkbox" name="${data[i].field.field_option[k].value}" value="checked" id="${data[i].field.field_option[k].value}">
                                //                             </div>
                                //                         </div>`;
                                //     }
                                //     break;
                                case 'select':
                                    
                                    let preSelect = `<div class="col-md-6 form-group my-2">
                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                        <select class="form-control" name="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                        <option>Select</option>`;

                                    let preOption = '';

                                    for(let l = 0; l < data[i].field.field_option.length; l++){
                                        preOption += `<option value="${data[i].field.field_option[l].id}">${data[i].field.field_option[l].value}</option>`;
                                    }

                                    let postSelect = `</select>
                                        </div>`;

                                    custom_field += preSelect + preOption + postSelect;
                                        
                                    break;
                                case 'radio':
                                    for(let k = 0; k < data[i].field.field_option.length; k++){
                                        custom_field += `<div class="form-group col-md-6 my-2">
                                                            <div class="col-md-6">
                                                                <label for="">${data[i].field.field_option[k].value} </label>
                                                                <input type="radio" name="${data[i].field.name}" value="${data[i].field.field_option[k].value}" id="${data[i].field.field_option[k].value}">
                                                            </div>
                                                        </div>`;
                                    }
                                    break;
                                case 'file':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="file" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                        </div>`;
                                    break;
                                case 'url':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                        </div>`;
                                    break;
                                case 'number':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="number" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                        </div>`;
                                    break;
                                case 'date':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="date" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                        </div>`;
                                    break;

                                case 'dependency':
                                    for(let l = 0; l < data[i].field.dependency.length; l++){
                                        custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.dependency[l].master}">${data[i].field.dependency[l].master} </label>
                                            <select class="form-control" onChange="masterChange('${data[i].field.dependency[l].master}')" name="${data[i].field.dependency[l].master}" id="select_dependency_${data[i].field.dependency[l].master}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                <option value="">Select</option>
                                            </select>
                                        </div>`;

                                        if(l == 0){

                                            select_id = `select_dependency_${data[i].field.dependency[l].master}`;
                                            
                                            $.ajax({
                                                url : '/get/master/dependency',
                                                async : false,
                                                type : 'get',
                                                data : {master:data[i].field.dependency[l].master},
                                                success:function(result){
                                                    
                                                    
                                                    dependencyOption += '<option value="">Select</option>';
                                                    
                                                    for(let i = 0; i < result.length; i++){
                                                        dependencyOption += `<option value="${result[i].id}">${result[i].name}</option>`;
                                                    }
                                                }
                                            });
                                        }
                                    }
                                    
                                    break;
                            }
                        // }
                    }

                    $('#custom_fields').html(custom_field);
                       
                    // $('#select_identity').html(selectoption);
                    
                    $(`#${select_id}`).html(dependencyOption);
                }
            });

            masterChange = (master_type) => {
                
                if(master_type == 'Country'){

                    let value = $('#select_dependency_Country').val();
                    let option = '';

                    $.ajax({
                        url : '/global/state/get',
                        type : 'get',
                        data : {id:value},
                        success:function(data){

                            option += `<option value="">Select</option>`;

                            for(let i = 0; i < data.length; i++){
                                option += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }

                            $('#select_dependency_State').html(option);
                        }
                    });

                }
                else if(master_type == 'State'){
                    
                    let value = $('#select_dependency_State').val();
                    let option = '';

                    $.ajax({
                        url : '/global/city/get',
                        type : 'get',
                        data : {id:value},
                        success:function(data){

                            option += `<option value="">Select</option>`;

                            for(let i = 0; i < data.length; i++){
                                option += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }

                            $('#select_dependency_City').html(option);
                        }
                    });
                }
                else if(master_type == 'Make'){

                    let value = $('#select_dependency_Make').val();
                    let option = '';

                    $.ajax({
                        url : '/global/vehicle/model/get',
                        type : 'get',
                        data : {id:value},
                        success:function(data){

                            option += `<option value="">Select</option>`;

                            for(let i = 0; i < data.length; i++){
                                option += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }

                            $('#select_dependency_Model').html(option);
                        }
                    });
                }
                else if(master_type == 'Model'){
                    
                    let value = $('#select_dependency_Model').val();
                    let option = '';
                    
                    $.ajax({
                        url : '/global/vehicle/varient/get',
                        type : 'get',
                        data : {id:value},
                        success:function(data){
                            
                            option += `<option value="">Select</option>`;

                            for(let i = 0; i < data.length; i++){
                                option += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }
                            
                            $('#select_dependency_Variant').html(option);
                        }
                    });
                }
            }

        });

        $(document).ready(function(){

            $(document).on('change', '#country', function(){
            
                let id = $(this).val();
                let option = '';

                $.ajax({
                    url : '/global/state/get',
                    type : 'get',
                    data : {id:id},
                    success:function(data){

                        option += `<option value="">Select</option>`;

                        for(let i = 0; i < data.length; i++){
                            option += `<option value="${data[i].id}">${data[i].name}</option>`;
                        }

                        $('#state').html(option);
                    }
                });
            });

            $(document).on('change', '#state', function(){

                let id = $(this).val();
                let option = '';

                $.ajax({
                    url : '/global/city/get',
                    type : 'get',
                    data : {id:id},
                    success:function(data){

                        option += `<option value="">Select</option>`;

                        for(let i = 0; i < data.length; i++){
                            option += `<option value="${data[i].id}">${data[i].name}</option>`;
                        }

                        $('#city').html(option);
                    }
                });
            });
            
        });

    </script>

    {{-- Location picker --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initialize" async defer></script>
        
    <script>
        function initialize() {

            $('#address-input').on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });
            const locationInputs = document.getElementsByClassName("map-input");

            const autocompletes = [];
            const geocoder = new google.maps.Geocoder;
            for (let i = 0; i < locationInputs.length; i++) {

                const input = locationInputs[i];
                const fieldKey = input.id.replace("-input", "");
                const isEdit = document.getElementById(fieldKey + "-latitude").value != '' && document.getElementById(fieldKey + "-longitude").value != '';

                const latitude = parseFloat(document.getElementById(fieldKey + "-latitude").value) || 23.4241;
                const longitude = parseFloat(document.getElementById(fieldKey + "-longitude").value) || 53.8478;

                const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
                    center: {lat: latitude, lng: longitude},
                    zoom: 13
                });
                const marker = new google.maps.Marker({
                    map: map,
                    draggable:true,
                    position: {lat: latitude, lng: longitude},
                });
                
                marker.setVisible(isEdit);

                const autocomplete = new google.maps.places.Autocomplete(input);
                autocomplete.key = fieldKey;
                autocompletes.push({input: input, map: map, marker: marker, autocomplete: autocomplete});
                
            }

            for (let i = 0; i < autocompletes.length; i++) {
                
                const input = autocompletes[i].input;
                const autocomplete = autocompletes[i].autocomplete;
                const map = autocompletes[i].map;
                const marker = autocompletes[i].marker;

                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    marker.setVisible(false);
                    const place = autocomplete.getPlace();

                    geocoder.geocode({'placeId': place.place_id}, function (results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            const lat = results[0].geometry.location.lat();
                            const lng = results[0].geometry.location.lng();
                            setLocationCoordinates(autocomplete.key, lat, lng);
                        }
                    });

                    if (!place.geometry) {
                        // window.alert("No details available for input: '" + place.name + "'");
                        customAlert.alert('Something went wrong please try againg');
                        input.value = "";
                        return;
                        
                    }

                    if (place.geometry.viewport) {
                        map.fitBounds(place.geometry.viewport);
                    } else {
                        map.setCenter(place.geometry.location);
                        map.setZoom(17);
                    }
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                });

                google.maps.event.addListener(marker, 'dragend', function(){

                    geocodePosition(autocomplete.key, marker.getPosition());

                });
            }
        }


        function setLocationCoordinates(key, lat, lng) {
            const latitudeField = document.getElementById(key + "-" + "latitude");
            const longitudeField = document.getElementById(key + "-" + "longitude");
            latitudeField.value = lat;
            longitudeField.value = lng;
        }

        function geocodePosition(key, pos){
            geocoder = new google.maps.Geocoder();
                geocoder.geocode({ latLng:pos }, function(results, status){
                    if (status === google.maps.GeocoderStatus.OK) {
                        const lat = results[0].geometry.location.lat();
                        const lng = results[0].geometry.location.lng();
                        setLocationCoordinates(key, lat, lng);

                        const formated_address = results[0].formatted_address;

                        document.getElementById('address-input').value = formated_address;
                        
                    }
                });
        }

            // custom alert

            function CustomAlert(){
                this.alert = function(message,title){
                    document.body.innerHTML = document.body.innerHTML + '<div id="dialogoverlay"></div><div id="dialogbox" class="slit-in-vertical"><div><div id="dialogboxhead"></div><div id="dialogboxbody"></div><div id="dialogboxfoot"></div></div></div>';
                    
                    let dialogoverlay = document.getElementById('dialogoverlay');
                    let dialogbox = document.getElementById('dialogbox');
                    
                    let winH = window.innerHeight;
                    dialogoverlay.style.height = winH+"px";

                    dialogbox.style.top = "100px";

                    dialogoverlay.style.display = "block";
                    dialogbox.style.display = "block";
                    
                    document.getElementById('dialogboxhead').style.display = 'block';

                    if(typeof title === 'undefined') {
                    document.getElementById('dialogboxhead').style.display = 'none';
                    } else {
                    document.getElementById('dialogboxhead').innerHTML = '<i class="fa fa-exclamation-circle" aria-hidden="true"></i> '+ title;
                    }
                    
                    document.getElementById('dialogboxbody').innerHTML = message;
                    document.getElementById('dialogboxfoot').innerHTML = '<button class="pure-material-button-contained active" onclick="okFunction()">OK</button>';

                }
                
                this.ok = function(){
                    document.getElementById('dialogbox').style.display = "none";
                    document.getElementById('dialogoverlay').style.display = "none";
                }
                }

                let customAlert = new CustomAlert();

                okFunction = () => {
                    customAlert.ok()
                    window.location.href = window.location;
                }
                
    </script>

    <script>
        $(document).ready(function(){

            let category = $("#category :selected").val();
            let subcategory = $("#subcategory :selected").val();
            let country = $("#country :selected").val();
            let state = $("#state :selected").val();
            let city = $("#city :selected").val();
            let id = '';
            let custom_field = '';
            let select_id = '';
            let dependencyOption = '';
            let adId = '{{ $ad->id }}';
            let adDependency = '';
            let adCustomValue = '';
            
            let option = '';
            id = category;
            
            if(id == 1){

                let makeOption = '';
                let modelOption = '';
                let varientOption = '';
                let make = '{{ $ad->category_id == 1 ? $ad->MotoreValue->make_id : '' }}';
                let model = '{{ $ad->category_id == 1 ? $ad->MotoreValue->model_id : '' }}';
                let variant = '{{ $ad->category_id == 1 ? $ad->MotoreValue->varient_id : '' }}';
                let register = '{{ $ad->category_id == 1 ? $ad->MotoreValue->registration_year : '' }}';
                let fuel = '{{$ad->category_id == 1 ? $ad->MotoreValue->fuel_type : '' }}';
                let transmission = '{{ $ad->category_id == 1 ? $ad->MotoreValue->transmission : '' }}';
                let condition = '{{ $ad->category_id == 1 ? $ad->MotoreValue->condition : '' }}';
                let milage = '{{ $ad->category_id == 1 ? $ad->MotoreValue->milage : ''}}';

                let aircondition = false;
                let gps = false;
                let security = false;
                let tire = false;

                $.ajax({
                    url : '/get/master/dependency',
                    async : false,
                    type : 'get',
                    data : {master:'Make'},
                    success:function(result){
                        
                        for(let i = 0; i < result.length; i++){
                            if(make == result[i].id){
                                makeOption += `<option selected value="${result[i].id}">${result[i].name}</option>`;
                            }
                            else{
                                makeOption += `<option value="${result[i].id}">${result[i].name}</option>`;
                            }
                        }
                    }
                });
                
                $.ajax({
                    url : '/global/vehicle/model/get',
                    type : 'get',
                    data : {id:make},
                    async: false,
                    success:function(data){

                        for(let i = 0; i < data.length; i++){
                            if(model == data[i].id){
                                
                                modelOption += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                            }
                            else{
                                modelOption += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }
                        }
                    }
                });

                $.ajax({
                    url : '/global/vehicle/varient/get',
                    type : 'get',
                    data : {id:model},
                    async: false,
                    success:function(data){

                        for(let i = 0; i < data.length; i++){
                            if(variant == data[i].id){
                                
                                varientOption += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                            }
                            else{
                                varientOption += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }
                        }
                    }
                });


                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Make">Make </label>
                                    <select class="select2 form-control @error('make') 'is-invalid' @enderror" name="make" id="Make">
                                        ${makeOption}
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('make')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Model">Model </label>
                                    <select class="form-control @error('model') 'is-invalid' @enderror" name="model" id="Model">
                                        ${modelOption}
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('model')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Variant">Variant </label>
                                    <select class="form-control @error('varient') 'is-invalid' @enderror" name="varient" id="Varient">
                                        ${varientOption}
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('varient')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="RegisterdYear">Registerd Year </label>
                                    <input type="number" value=${register} class="form-control @error('registered_year') 'is-invalid' @enderror" name="registered_year" id="RegisterdYear" placeholder="Registerd Year" >
                                    <div class="invalid-feedback">
                                        @error('registered_year')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Fuel">Fuel Type </label>
                                    <select class="form-control @error('fuel') 'is-invalid' @enderror" name="fuel" id="Fuel">
                                        <option ${fuel == 'Petrol' ? 'selected' : ''} value="Petrol">Petrol</option>
                                        <option ${fuel == 'Diesel' ? 'selected' : ''} value="Diesel">Diesel</option>
                                        <option ${fuel == 'LPG Gas' ? 'selected' : ''} value="LPG Gas">LPG Gas</option>
                                        <option ${fuel == 'Electric' ? 'selected' : ''} value="Electric">Electric</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('fuel')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row">
                                    <label for="Transmission">Transmission </label>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Manual">Manual </label>
                                        <input ${transmission == 'Manual' ? 'checked' : ''} type="radio" class="" name="transmission" id="Manual" value="Manual" >
                                    </div>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Automatic">Automatic </label>
                                        <input ${transmission == 'Automatic' ? 'checked' : ''} type="radio" class="@error('transmission') 'is-invalid' @enderror" name="transmission" id="Automatic" value="Automatic" >
                                        <div class="invalid-feedback">
                                            @error('transmission')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row mx-2">
                                    <label for="Transmission">Condition </label>
                                    <div class="custom-form-control col-md-6">
                                        <label for="New">New </label>
                                        <input ${condition == 'New' ? 'checked' : ''} type="radio" class="" name="condition" id="New" value="New" >
                                    </div>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Used">Used </label>
                                        <input ${condition == 'Used' ? 'checked' : ''} type="radio" class="@error('condition') 'is-invalid' @enderror" name="condition" id="Used" value="Used" >
                                        <div class="invalid-feedback">
                                            @error('condition')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Mileage">Mileage </label>
                                    <input type="number" value="${milage}" class="form-control @error('mileage') 'is-invalid' @enderror" name="mileage" id="Mileage" placeholder="Mileage" >
                                    <div class="invalid-feedback">
                                        @error('mileage')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;
                
                $.ajax({
                    url : '/get/motor/feature',
                    type : 'get',
                    data : {id:adId},
                    async:false,
                    success:function(data1){

                        aircondition = data1.some(data => data.value == 'Air Conditioner');
                        gps = data1.some(data => data.value == 'GPS');
                        security = data1.some(data => data.value == 'Security System');
                        tire = data1.some(data => data.value == 'Spare Tire');
                        
                    }
                });
                
                custom_field += `<div class="col-md-6 form-group my-2 row mx-2">
                                    <label for="Transmission">Features </label>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox" ${aircondition == true ? 'checked' : ''} class="" name="features[]" id="AirConditioner" value="Air Conditioner" >
                                        <label for="AirConditioner">Air Conditioner </label>
                                    </div>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox"${gps ? 'checked' : ''} class="" name="features[]" id="GPS" value="GPS" >
                                        <label for="GPS">GPS </label>
                                    </div>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox" ${security ? 'checked' : ''} class="" name="features[]" id="SecuritySystem" value="Security System" >
                                        <label for="SecuritySystem">Security System </label>
                                    </div>
                                    <div class="custom-form-control col-md-12">
                                        <input type="checkbox" ${tire ? 'checked' : ''} class="@error('features') 'is-invalid' @enderror" name="features[]" id="SpareTire" value="Spare Tire" >
                                        <label for="SpareTire" >Spare Tire </label>
                                        <div class="invalid-feedback">
                                            @error('features')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

            }
            else if(id == 2){

                let size = '{{ $ad->category_id == 2 ? $ad->PropertyRend->size : "" }}';
                let room = '{{ $ad->category_id == 2 ? $ad->PropertyRend->room : "" }}';
                let furnished = '{{ $ad->category_id == 2 ? $ad->PropertyRend->furnished : "" }}';
                let building_type = '{{ $ad->category_id == 2 ? $ad->PropertyRend->building_type : "" }}';
                let parking = '{{ $ad->category_id == 2 ? $ad->PropertyRend->parking : "" }}';

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Size">Size </label>
                                    <input type="number" value=${size} class="form-control @error('size') 'is-invalid' @enderror" name="size" id="Size" placeholder="Size" >
                                    <div class="invalid-feedback">
                                        @error('size')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Room">Rooms </label>
                                    <input type="number" value=${room} class="form-control @error('rooms') 'is-invalid' @enderror" name="rooms" id="Room" placeholder="Rooms" >
                                    <div class="invalid-feedback">
                                        @error('rooms')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row mx-2">
                                    <label for="Furnished">Furnished </label>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Yes">Yes </label>
                                        <input type="radio" ${furnished == 'Yes' ? 'checked' : ''} class="@error('furnished') 'is-invalid' @enderror" name="furnished" id="Yes" value="Yes" >
                                        <div class="invalid-feedback">
                                            @error('furnished')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="custom-form-control col-md-6">
                                        <label for="No">No </label>
                                        <input type="radio" ${furnished == 'No' ? 'checked' : ''} class="@error('furnished') 'is-invalid' @enderror" name="furnished" id="No" value="No" >
                                        <div class="invalid-feedback">
                                            @error('furnished')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Building">Building Type </label>
                                    <select class="form-control @error('building') 'is-invalid' @enderror" name="building" id="Building">
                                        <option value="" >Select</option>
                                        <option ${building_type == 'Apartment' ? 'selected' : ''} value="Apartment">Apartment</option>
                                        <option ${building_type == 'House' ? 'selected' : ''} value="House">House</option>
                                        <option ${building_type == 'Store' ? 'selected' : ''} value="Store">Store</option>
                                        <option ${building_type == 'Office' ? 'selected' : ''} value="Office">Office</option>
                                        <option ${building_type == 'Plot of land' ? 'selected' : ''} value="Plot of land">Plot of land</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('building')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 my-2 row mx-2">
                                    <div class="custom-form-control col-md-12">
                                        <label for="Parking">Parking </label>
                                        <input type="checkbox" ${parking == 1 ? 'checked' : ''} class="custom-control-input @error('parking') 'is-invalid' @enderror" name="parking" id="Parking" value="Parking" >
                                        <div class="invalid-feedback">
                                        @error('parking')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    </div>
                                </div>`;
            }
            else if(id == 3){

                let size = '{{ $ad->category_id == 3 ? $ad->PropertySale->size : "" }}';
                let room = '{{ $ad->category_id == 3 ? $ad->PropertySale->room : "" }}';
                let furnished = '{{ $ad->category_id == 3 ? $ad->PropertySale->furnished : "" }}';
                let building_type = '{{ $ad->category_id == 3 ? $ad->PropertySale->building_type : "" }}';
                let parking = '{{ $ad->category_id == 3 ? $ad->PropertySale->parking : "" }}';

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Size">Size </label>
                                    <input type="number" value=${size} class="form-control @error('size') 'is-invalid' @enderror" name="size" id="Size" placeholder="Size" >
                                    <div class="invalid-feedback">
                                        @error('size')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Room">Rooms </label>
                                    <input type="number" value=${room} class="form-control @error('rooms') 'is-invalid' @enderror" name="rooms" id="Room" placeholder="Rooms" >
                                    <div class="invalid-feedback">
                                        @error('rooms')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2 row mx-2">
                                    <label for="Furnished">Furnished </label>
                                    <div class="custom-form-control col-md-6">
                                        <label for="Yes">Yes </label>
                                        <input type="radio" ${furnished == 'Yes' ? 'checked' : ''} class="@error('furnished') 'is-invalid' @enderror" name="furnished" id="Yes" value="Yes" >
                                        <div class="invalid-feedback">
                                            @error('furnished')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="custom-form-control col-md-6">
                                        <label for="No">No </label>
                                        <input type="radio" ${furnished == 'No' ? 'checked' : ''} class="@error('furnished') 'is-invalid' @enderror" name="furnished" id="No" value="No" >
                                        <div class="invalid-feedback">
                                            @error('furnished')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 form-group my-2">
                                    <label for="Building">Building Type </label>
                                    <select class="form-control @error('building') 'is-invalid' @enderror" name="building" id="Building">
                                        <option value="" >Select</option>
                                        <option ${building_type == 'Apartment' ? 'selected' : ''} value="Apartment">Apartment</option>
                                        <option ${building_type == 'House' ? 'selected' : ''} value="House">House</option>
                                        <option ${building_type == 'Store' ? 'selected' : ''} value="Store">Store</option>
                                        <option ${building_type == 'Office' ? 'selected' : ''} value="Office">Office</option>
                                        <option ${building_type == 'Plot of land' ? 'selected' : ''} value="Plot of land">Plot of land</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        @error('building')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>`;

                custom_field += `<div class="col-md-6 my-2 row mx-2">
                                    <div class="custom-form-control col-md-12">
                                        <label for="Parking">Parking </label>
                                        <input type="checkbox" ${parking == 1 ? 'checked' : ''} class="custom-control-input @error('parking') 'is-invalid' @enderror" name="parking" id="Parking" value="Parking" >
                                        <div class="invalid-feedback">
                                        @error('parking')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    </div>
                                </div>`;
            }


                $.ajax({
                    url : '/change/subcategory',
                    type : 'get',
                    data : {id:id},
                    success:function(data){

                        for(let i = 0; i < data.length; i++){
                            
                            if(data[i].id == subcategory){
                                option += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                                
                            }
                            else{
                                option += `<option value="${data[i].id}">${data[i].name}</option>`;
                                for(let j = 0; j < data[i].subcategory_child.length; j++){
                                    if(data[i].id == data[i].subcategory_child[j].id){
                                        option += `<option selected value="${data[i].subcategory_child[j].id}"> -----| ${data[i].subcategory_child[j].name}</option>`;
                                    }
                                    else{
                                        option += `<option value="${data[i].subcategory_child[j].id}"> -----| ${data[i].subcategory_child[j].name}</option>`;
                                    }
                                }
                            }
                        }

                        $('#subcategory').html(option);
                    }
                });


            
                id = country;
                let option1 = '';

                $.ajax({
                    url : '/global/state/get',
                    type : 'get',
                    data : {id:id},
                    success:function(data){

                        for(let i = 0; i < data.length; i++){
                            
                            if(data[i].id == state){
                                option1 += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                            }
                            else{
                                option1 += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }
                        }

                        $('#state').html(option1);
                    }
                });

            
                id = state;
                let option2 = '';

                $.ajax({
                    url : '/global/city/get',
                    type : 'get',
                    data : {id:id},
                    success:function(data){

                        for(let i = 0; i < data.length; i++){
                            if(data[i].id == state){
                                option2 += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                            }
                            else{
                                option2 += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }
                            
                        }

                        $('#city').html(option2);
                    }
                });

                $.ajax({
                    url : '/ads/related/field',
                    type : 'get',
                    data : {id:adId},
                    async: false,
                    success:function(data){
                        adDependency = data.adsDependency;
                        adCustomValue = data.customValue;
                    }
                });

                id = category;

                $.ajax({
                    url : '/get/custom/field',
                    type : 'get',
                    data : {id:id},
                    success:function(data){
                        
                        // let selectoption  = '<option value="">Select</option>';
                        // let identity = 'select_identity';
                        
                        for(let i = 0; i < data.length; i++){
                            
                                switch (data[i].field.type){
                                    case 'text':
                                        if(adCustomValue[i]){
                                            if(adCustomValue[i].type == 'text' && adCustomValue[i].field_name == data[i].field.name){
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                    <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                    <input type="text" class="form-control" name="${data[i].field.name}" value=${adCustomValue[i].value} id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                </div>`;
                                            }
                                            else{
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                        }
                                        else{
                                            custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                        }
                                        break;
                                    case 'textarea':
                                        if(adCustomValue[i]){
                                            if(adCustomValue[i].type == 'textarea' && adCustomValue[i].field_name == data[i].field.name){
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <textarea type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>${adCustomValue[i].value}</textarea>
                                                    </div>`;
                                                }
                                                else{
                                                    custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <textarea type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}></textarea>
                                                    </div>`;
                                                }
                                        }
                                        else{
                                            custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <textarea type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}></textarea>
                                                    </div>`;
                                        }
                                        break;
                                    case 'checkbox':
                                        if(adCustomValue[i]){
                                            if(adCustomValue[i].type == 'checkbox' && adCustomValue[i].field_name == data[i].field.name && adCustomValue[i].value == 1){
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="checkbox" class="" name="${data[i].field.name}" checked value="checked" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                            else{
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="checkbox" class="" name="${data[i].field.name}" value="checked" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                        }
                                        else{
                                            custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="checkbox" class="" name="${data[i].field.name}" value="checked" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                        }
                                        break;
                                    // case 'checkbox_multiple':
                                    //     // let newFieldArray = adCustomValue.filter(function(data){
                                    //     //     return data.type == 'checkbox_multiple';
                                    //     // });
                                        
                                    //     for(let k = 0; k < data[i].field.field_option.length; k++){
                                            
                                    //         //    for(let n = 0; n < newFieldArray.length; n++){ 
                                    //                 if(adCustomValue[i].type == 'checkbox_multiple' && adCustomValue[i].field_name == data[i].field.name && adCustomValue[i].option_id == data[i].field.field_option[k].id){
                                    //                     custom_field += `<div class="form-group col-md-6 my-2">
                                    //                                         <div class="col-md-6">
                                    //                                             <label for="">${data[i].field.field_option[k].value} </label>
                                    //                                             <input type="checkbox" name="${data[i].field.field_option[k].value}" checked value="checked" id="${data[i].field.field_option[k].value}">
                                    //                                         </div>
                                    //                                     </div>`;
                                                                       
                                                                       
                                    //                 }
                                    //                 else{
                                    //                     custom_field += `<div class="form-group col-md-6 my-2">
                                    //                                         <div class="col-md-6">
                                    //                                             <label for="">${data[i].field.field_option[k].value} </label>
                                    //                                             <input type="checkbox" name="${data[i].field.field_name}" value="checked" id="${data[i].field.field_option[k].value}">
                                    //                                         </div>
                                    //                                     </div>`;
                                    //                 }
                                    //             // }

                                               
                                    //     }
                                    //     break;
                                    case 'select':
                                    
                                        let preSelect = `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <select class="form-control" name="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>`;

                                        let preOption = '';

                                        for(let l = 0; l < data[i].field.field_option.length; l++){
                                            if(adCustomValue[i]){
                                                if(adCustomValue[i].type == 'select' && adCustomValue[i].field_name == data[i].field.name && data[i].field.field_option[l].id == adCustomValue[i].option_id){
                                                    preOption += `<option selected value="${data[i].field.field_option[l].id}">${data[i].field.field_option[l].value}</option>`;
                                                }
                                                else{
                                                    preOption += `<option value="${data[i].field.field_option[l].id}">${data[i].field.field_option[l].value}</option>`;
                                                }
                                            }
                                            else{
                                                preOption += `<option value="${data[i].field.field_option[l].id}">${data[i].field.field_option[l].value}</option>`;
                                            }
                                        }

                                        let postSelect = `</select>
                                            </div>`;

                                        custom_field += preSelect + preOption + postSelect;
                                            
                                        break;
                                    case 'radio':
                                        for(let k = 0; k < data[i].field.field_option.length; k++){
                                            if(adCustomValue[i]){
                                                if(adCustomValue[i].type == 'radio' && adCustomValue[i].field_name == data[i].field.name && data[i].field.field_option[k].id == adCustomValue[i].option_id){
                                                    custom_field += `<div class="form-group col-md-6 my-2">
                                                                    <div class="col-md-6">
                                                                        <label for="">${data[i].field.field_option[k].value} </label>
                                                                        <input type="radio" checked name="${data[i].field.name}" value="${data[i].field.field_option[k].value}" id="${data[i].field.field_option[k].value}">
                                                                    </div>
                                                                </div>`;
                                                }
                                                else{
                                                    custom_field += `<div class="form-group col-md-6 my-2">
                                                                    <div class="col-md-6">
                                                                        <label for="">${data[i].field.field_option[k].value} </label>
                                                                        <input type="radio" name="${data[i].field.name}" value="${data[i].field.field_option[k].value}" id="${data[i].field.field_option[k].value}">
                                                                    </div>
                                                                </div>`;
                                                }
                                            }
                                            else{
                                                custom_field += `<div class="form-group col-md-6 my-2">
                                                                    <div class="col-md-6">
                                                                        <label for="">${data[i].field.field_option[k].value} </label>
                                                                        <input type="radio" name="${data[i].field.name}" value="${data[i].field.field_option[k].value}" id="${data[i].field.field_option[k].value}">
                                                                    </div>
                                                                </div>`;
                                            }
                                        }
                                        break;
                                    case 'file':
                                        custom_field += `<div class="col-md-6 form-group my-2">
                                                <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                <input type="file" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>

                                            </div>`;
                                        break;
                                    case 'url':
                                        if(adCustomValue[i]){
                                            if(adCustomValue[i].type == 'url' && adCustomValue[i].field_name == data[i].field.name){
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="text" class="form-control" name="${data[i].field.name}" value=${adCustomValue[i].value} id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                            else{
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                        }
                                        else{
                                            custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                        }
                                        break;
                                    case 'number':
                                        if(adCustomValue[i]){
                                            if(adCustomValue[i].type == 'number' && adCustomValue[i].field_name == data[i].field.name){
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="number" class="form-control" name="${data[i].field.name}" value=${adCustomValue[i].value} id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                            else{
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="number" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                        }
                                        else{
                                            custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="number" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                        }
                                        break;
                                    case 'date':
                                        if(adCustomValue[i]){
                                            if(adCustomValue[i].type == 'date' && adCustomValue[i].field_name == data[i].field.name){
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                        <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                        <input type="date" class="form-control" name="${data[i].field.name}" value=${adCustomValue[i].value} id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                        </div>`;
                                            }
                                            else{
                                                custom_field += `<div class="col-md-6 form-group my-2">
                                                    <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                    <input type="date" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                            }
                                        }
                                        else{
                                            custom_field += `<div class="col-md-6 form-group my-2">
                                                    <label for="${data[i].field.name}">${data[i].field.name} </label>
                                                    <input type="date" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    </div>`;
                                        }
                                        break;

                                    case 'dependency':
                                        for(let l = 0; l < data[i].field.dependency.length; l++){
                                            custom_field += `<div class="col-md-6 form-group my-2">
                                                <label for="${data[i].field.dependency[l].master}">${data[i].field.dependency[l].master} </label>
                                                <select class="form-control" onChange="masterChange('${data[i].field.dependency[l].master}')" name="${data[i].field.dependency[l].master}" id="select_dependency_${data[i].field.dependency[l].master}" ${data[i].field.required == 1 ? 'required' : ''}>
                                                    <option value="${adDependency.length > l ? adDependency[l].master_id : '' }">${adDependency.length > l ? adDependency[l].name : 'select'}</option>
                                                </select>
                                            </div>`;

                                            if(l == 0){

                                                select_id = `select_dependency_${data[i].field.dependency[l].master}`;
                                                
                                                $.ajax({
                                                    url : '/get/master/dependency',
                                                    async : false,
                                                    type : 'get',
                                                    data : {master:data[i].field.dependency[l].master},
                                                    success:function(result){
                                                       
                                                        dependencyOption += '<option value="">Select</option>';
                                                        
                                                        for(let i = 0; i < result.length; i++){
                                                            if(adDependency[l].master_id == result[i].id){
                                                                dependencyOption += `<option selected value="${result[i].id}">${result[i].name}</option>`;
                                                            }
                                                            else{
                                                                dependencyOption += `<option value="${result[i].id}">${result[i].name}</option>`;
                                                            }
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                        
                                        break;
                                }
                            
                        }

                        $('#custom_fields').html(custom_field);
                        
                        // $('#select_identity').html(selectoption);
                        
                        $(`#${select_id}`).html(dependencyOption);

                        
                    }
                });

                for(let q = 0; q < adDependency.length; q++){

                    let masId = adDependency[q].master_id;
                    let selectId = '';
                    let newOption = '';

                    if(adDependency[q].master_type == 'make'){
                        selectId = 'select_dependency_Make';

                        $.ajax({
                            url : '/global/vehicle/model/get',
                            type : 'get',
                            data : {id:masId},
                            async : false,
                            success:function(data){

                                for(let i = 0; i < data.length; i++){
                                    newOption += `<option value="${data[i].id}">${data[i].name}</option>`;
                                }

                                $('#select_dependency_Model').append(newOption);
                            }
                        });

                    }
                    else if(adDependency[q].master_type == 'model'){
                        selectId = 'select_dependency_Model';

                        $.ajax({
                            url : '/global/vehicle/varient/get',
                            type : 'get',
                            data : {id:masId},
                            async : false,
                            success:function(data){
                                
                                for(let i = 0; i < data.length; i++){
                                    newOption += `<option value="${data[i].id}">${data[i].name}</option>`;
                                }
                                
                                $('#select_dependency_Variant').append(newOption);
                            }
                        });
                    }
                }

                
                masterChange = (master_type) => {
                
                    if(master_type == 'Country'){

                        let value = $('#select_dependency_Country').val();
                        let option = '';

                        $.ajax({
                            url : '/global/state/get',
                            type : 'get',
                            data : {id:value},
                            success:function(data){

                                option += `<option value="">Select</option>`;

                                for(let i = 0; i < data.length; i++){
                                    option += `<option value="${data[i].id}">${data[i].name}</option>`;
                                }

                                $('#select_dependency_State').html(option);
                            }
                        });

                    }
                    else if(master_type == 'State'){
                        
                        let value = $('#select_dependency_State').val();
                        let option = '';

                        $.ajax({
                            url : '/global/city/get',
                            type : 'get',
                            data : {id:value},
                            success:function(data){

                                option += `<option value="">Select</option>`;

                                for(let i = 0; i < data.length; i++){
                                    option += `<option value="${data[i].id}">${data[i].name}</option>`;
                                }

                                $('#select_dependency_City').html(option);
                            }
                        });
                    }
                    else if(master_type == 'Make'){

                        let value = $('#select_dependency_Make').val();
                        let option = '';

                        $.ajax({
                            url : '/global/vehicle/model/get',
                            type : 'get',
                            data : {id:value},
                            success:function(data){

                                option += `<option value="">Select</option>`;

                                for(let i = 0; i < data.length; i++){
                                    option += `<option value="${data[i].id}">${data[i].name}</option>`;
                                }

                                $('#select_dependency_Model').html(option);
                            }
                        });
                    }
                    else if(master_type == 'Model'){
                        
                        let value = $('#select_dependency_Model').val();
                        let option = '';
                        
                        $.ajax({
                            url : '/global/vehicle/varient/get',
                            type : 'get',
                            data : {id:value},
                            success:function(data){
                                
                                option += `<option value="">Select</option>`;

                                for(let i = 0; i < data.length; i++){
                                    option += `<option value="${data[i].id}">${data[i].name}</option>`;
                                }
                                
                                $('#select_dependency_Variant').html(option);
                            }
                        });
                    }
                }

        });
    </script>

@endpush

@push('style')
    <style>
        @import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css");

        /* ---------------Animation---------------- */

        .slit-in-vertical {
        -webkit-animation: slit-in-vertical 0.45s ease-out both;
                animation: slit-in-vertical 0.45s ease-out both;
        }

        @-webkit-keyframes slit-in-vertical {
        0% {
            -webkit-transform: translateZ(-800px) rotateY(90deg);
                    transform: translateZ(-800px) rotateY(90deg);
            opacity: 0;
        }
        54% {
            -webkit-transform: translateZ(-160px) rotateY(87deg);
                    transform: translateZ(-160px) rotateY(87deg);
            opacity: 1;
        }
        100% {
            -webkit-transform: translateZ(0) rotateY(0);
                    transform: translateZ(0) rotateY(0);
        }
        }
        @keyframes slit-in-vertical {
        0% {
            -webkit-transform: translateZ(-800px) rotateY(90deg);
                    transform: translateZ(-800px) rotateY(90deg);
            opacity: 0;
        }
        54% {
            -webkit-transform: translateZ(-160px) rotateY(87deg);
                    transform: translateZ(-160px) rotateY(87deg);
            opacity: 1;
        }
        100% {
            -webkit-transform: translateZ(0) rotateY(0);
                    transform: translateZ(0) rotateY(0);
        }
        }

        /*---------------#region Alert--------------- */

        #dialogoverlay{
        display: none;
        opacity: .8;
        position: fixed;
        top: 0px;
        left: 0px;
        background: #707070;
        width: 100%;
        z-index: 10;
        }

        #dialogbox{
        display: none;
        position: absolute;
        background: rgb(236, 238, 238);
        border-radius:7px; 
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.575);
        transition: 0.3s;
        width: 40%;
        z-index: 10;
        top:0;
        left: 0;
        right: 0;
        margin: auto;
        }

        #dialogbox:hover {
        box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.911);
        }

        .container {
        padding: 2px 16px;
        }

        .pure-material-button-contained {
        position: relative;
        display: inline-block;
        box-sizing: border-box;
        border: none;
        border-radius: 4px;
        padding: 0 16px;
        min-width: 64px;
        height: 36px;
        vertical-align: middle;
        text-align: center;
        text-overflow: ellipsis;
        text-transform: uppercase;
        color: rgb(var(--pure-material-onprimary-rgb, 255, 255, 255));
        /* background-color: rgb(var(--pure-material-primary-rgb, 0, 77, 70)); */
        /* background-color: rgb(1, 47, 61) */
        background-color: #ef4c63;
        box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 2px 2px 0 rgba(0, 0, 0, 0.14), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
        font-family: var(--pure-material-font, "Roboto", "Segoe UI", BlinkMacSystemFont, system-ui, -apple-system);
        font-size: 14px;
        font-weight: 500;
        line-height: 36px;
        overflow: hidden;
        outline: none;
        cursor: pointer;
        transition: box-shadow 0.2s;
        }

        .pure-material-button-contained::-moz-focus-inner {
        border: none;
        }

        /* ---------------Overlay--------------- */

        .pure-material-button-contained::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgb(var(--pure-material-onprimary-rgb, 255, 255, 255));
        opacity: 0;
        transition: opacity 0.2s;
        }

        /* Ripple */
        .pure-material-button-contained::after {
        content: "";
        position: absolute;
        left: 50%;
        top: 50%;
        border-radius: 50%;
        padding: 50%;
        width: 32px; /* Safari */
        height: 32px; /* Safari */
        background-color: rgb(var(--pure-material-onprimary-rgb, 255, 255, 255));
        opacity: 0;
        transform: translate(-50%, -50%) scale(1);
        transition: opacity 1s, transform 0.5s;
        }

        /* Hover, Focus */
        .pure-material-button-contained:hover,
        .pure-material-button-contained:focus {
        box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.2), 0 4px 5px 0 rgba(0, 0, 0, 0.14), 0 1px 10px 0 rgba(0, 0, 0, 0.12);
        }

        .pure-material-button-contained:hover::before {
        opacity: 0.08;
        }

        .pure-material-button-contained:focus::before {
        opacity: 0.24;
        }

        .pure-material-button-contained:hover:focus::before {
        opacity: 0.3;
        }

        /* Active */
        .pure-material-button-contained:active {
        box-shadow: 0 5px 5px -3px rgba(0, 0, 0, 0.2), 0 8px 10px 1px rgba(0, 0, 0, 0.14), 0 3px 14px 2px rgba(0, 0, 0, 0.12);
        }

        .pure-material-button-contained:active::after {
        opacity: 0.32;
        transform: translate(-50%, -50%) scale(0);
        transition: transform 0s;
        }

        /* Disabled */
        .pure-material-button-contained:disabled {
        color: rgba(var(--pure-material-onsurface-rgb, 0, 0, 0), 0.38);
        background-color: rgba(var(--pure-material-onsurface-rgb, 0, 0, 0), 0.12);
        box-shadow: none;
        cursor: initial;
        }

        .pure-material-button-contained:disabled::before {
        opacity: 0;
        }

        .pure-material-button-contained:disabled::after {
        opacity: 0;
        }

        #dialogbox > div{ 
        background:#FFF; 
        margin:8px; 
        }

        #dialogbox > div > #dialogboxhead{ 
        background: rgb(250, 252, 252); 
        font-size:19px; 
        padding:10px; 
        color:rgb(7, 7, 7); 
        font-family: Verdana, Geneva, Tahoma, sans-serif ;
        }

        #dialogbox > div > #dialogboxbody{ 
        background:rgb(232, 235, 234); 
        padding:20px; 
        color:rgb(3, 3, 3); 
        font-family: Verdana, Geneva, Tahoma, sans-serif ;
        }

        #dialogbox > div > #dialogboxfoot{ 
        background: rgb(250, 252, 252); 
        padding:10px; 
        text-align:right; 
        }
        /*#endregion Alert*/
    </style>
@endpush
@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Create Ads</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ads.index') }}">Ads</a></li>
                <li class="breadcrumb-item active">Create Ads</li>
            </ol>
            
            <div class="card mb-4">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data" id="adStoreForm">
                            <div class="row">
                                
                                    @csrf
                                <div class="row">
                                    <div class="form-group my-2 col-md-6">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" autocomplete="off">
                                            <option value="" >Select</option>
                                            @foreach ($category as $row)
                                                <option {{ old('category') == $row->id ? 'selected' : '' }} value="{{ $row->id }}">{{ $row->name }}</option>
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
                                            <option value={{old('subcategory')}}>Select</option>
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
                                        <input type="text" name="title" value="{{ old('title') }}" class="slug form-control {{ Session::has('title_error') ? 'is-invalid' : '' }}" placeholder="Title" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @if (Session::has('title_error'))
                                                {{ Session::get('title_error') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="CanonicalName">Canonical Name</label>
                                        <input type="text" id="canonical_name" name="canonical_name" value="{{ old('canonical_name') }}" class="form-control @error('canonical_name') is-invalid @enderror" placeholder="Canonical Name" autocomplete="off" readonly>
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
                                        <input type="text" name="arabic_title" value="{{ old('arabic_title') }}" class="slug form-control {{ Session::has('title_error') ? 'is-invalid' : '' }}" placeholder="Title in Arabic" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @if (Session::has('title_error'))
                                                {{ Session::get('title_error') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="country">Country</label>
                                        <select name="country" id="country" class="select2 form-control @error('country') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                            @foreach ($country as $row1)
                                                
                                                <option {{ old('country') == $row1->id ? 'selected' : '' }} value="{{ $row1->id }}">{{ $row1->name }}</option>
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
                                        <input type="number" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" placeholder="Price" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('price')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="state">State</label>
                                        <select name="state" id="state" class="select2 form-control @error('state') is-invalid @enderror" autocomplete="off">
                                            <option value={{ old('state') }}>Select State</option>
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
                                            @error('image.*')
                                                {{ $message }}
                                            @enderror
                                            
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="city">City</label>
                                        <select name="city" id="city" class="select2  form-control @error('city') is-invalid @enderror" autocomplete="off">
                                            <option value={{old('city')}}>Select</option>
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
                                        <textarea name="description" class="form-control {{ Session::has('description_error') ? 'is-invalid' : '' }}" cols="30" rows="3" placeholder="Description" autocomplete="off">{{ old('description') }}</textarea>
                                        <div class="invalid-feedback">
                                            @if (Session::has('description_error'))
                                                {{ Session::get('description_error') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group my-2 col-md-6">
                                        <label for="Description">Description in Arabic</label>
                                        <textarea name="description_arabic" class="form-control {{ Session::has('description_error') ? 'is-invalid' : '' }}" cols="30" rows="3" placeholder="Description in Arabic" autocomplete="off">{{ old('description_arabic') }}</textarea>
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
                                                <input type="checkbox" name="negotiable" value="checked" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group my-2">
                                                <label for="Status">Featured</label>
                                                <input type="checkbox" name="featured" value="checked" autocomplete="off">
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
                                        <input type="text" name="seller_name" value="{{ $user->name }}" class="form-control @error('seller_name') is-invalid @enderror" placeholder="Seller Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('seller_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Phone">Phone</label>
                                        <input type="number" name="Phone" value="{{ $user->phone }}" class="form-control @error('Phone') is-invalid @enderror" placeholder="Phone" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('Phone')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group my-2">
                                            <label for="PhoneHide">Phone Hide</label>
                                            <input type="checkbox" name="phone_hide_flag" value="checked" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="Email">Email</label>
                                        <input type="email" name="email" value="{{ $user->email }}" class="form-control @error('email') is-invalid @enderror" placeholder="Email" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('email')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Address">Address</label>
                                        <textarea name="customer_address" class="form-control @error('customer_address') is-invalid @enderror" cols="30" rows="3" placeholder="Address" autocomplete="off">{{ old('customer_address') }}</textarea>
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
                            <button type="submit" class="btn btn-primary my-3">Create</button>
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
                // title: {
                //         required: true,
                //     },
                price: {
                        required: true,
                    },
                state: {
                        required: true,
                    },
                'image[]': {
                        required: true,
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
                    },

                size: {
                        required: true,
                    },
                rooms: {
                        required: true,
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
                                    <label for="Variant">Varient </label>
                                    <select class="form-control @error('varient') 'is-invalid' @enderror" name="varient" id="Varient">
                                        <option>Select variant</option>
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
                                    <label for="Transmission">Condition </label>
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
                                            <textarea type="text" class="form-control" name="${data[i].field.id}" id="${data[i].field.name}" placeholder="${data[i].field.name}" ${data[i].field.required == 1 ? 'required' : ''}></textarea>
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
                                //                                 <input type="checkbox" name="${data[i].field.field_option[k].value}" value="checked" id="${data[i].field.field_option[k].value}" ${data[i].field.required == 1 ? 'required' : ''}>
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
                                    
                                    custom_field += `<div class="form-group col-md-6 my-2 row">
                                        <label for="${data[i].field.name}">${data[i].field.name} </label>`;
                                        
                                    for(let k = 0; k < data[i].field.field_option.length; k++){
                                        custom_field += `<div class="col-md-4">
                                                                <label for="">${data[i].field.field_option[k].value} </label>
                                                                <input type="radio" name="${data[i].field.name}" value="${data[i].field.field_option[k].value}" id="${data[i].field.field_option[k].value}">
                                                            </div>`;
                                    }
                                    custom_field += `</div>`;
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

            let country_id = $('#country :selected').val();
            let state_id = $('#state :selected').val();
            let city_id = $('#city :selected').val();
            let category_id = $('#category :selected').val();
            let subcategory_id = $('#subcategory :selected').val();
            let stateOption = '';


                $.ajax({
                    url : '/global/state/get',
                    type : 'get',
                    data : {id:country_id},
                    success:function(data){

                        for(let i = 0; i < data.length; i++){
                            if(state_id == data[i].id){
                                stateOption += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                            }
                            else{
                                stateOption += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }
                        }

                        $('#state').html(stateOption);
                    }
                });
            

                let cityOption = '';

                $.ajax({
                    url : '/global/city/get',
                    type : 'get',
                    data : {id:state_id},
                    success:function(data){

                        for(let i = 0; i < data.length; i++){
                            if(city_id == data[i].id){
                                cityOption += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                            }
                            else{
                                cityOption += `<option value="${data[i].id}">${data[i].name}</option>`;
                            }
                        }

                        $('#city').html(cityOption);
                    }
                });

                let subcategoryOption = '<option value="">Select Subcategory</option>';

                $.ajax({
                    url : '/change/subcategory',
                    type : 'get',
                    data : {id:category_id},
                    success:function(data){
                        
                        for(let i = 0; i < data.length; i++){
                            if(subcategory_id == data[i].id){
                                subcategoryOption += `<option selected value="${data[i].id}">${data[i].name}</option>`;
                            }
                            else{
                                subcategoryOption += `<option value="${data[i].id}">${data[i].name}</option>`;
                                for(let j = 0; j < data[i].subcategory_child.length; j++){
                                    if(ubcategory_id == data[i].subcategory_child[j].id){
                                        subcategoryOption += `<option selected value="${data[i].subcategory_child[j].id}"> -----| ${data[i].subcategory_child[j].name}</option>`;
                                    }
                                    else{
                                        subcategoryOption += `<option value="${data[i].subcategory_child[j].id}"> -----| ${data[i].subcategory_child[j].name}</option>`;
                                    }
                                }
                            }

                        }
                        
                        $('#subcategory').html(subcategoryOption);
                    }
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
            console.log(lat, lng);
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
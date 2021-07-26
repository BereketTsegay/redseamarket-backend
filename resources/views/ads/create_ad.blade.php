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
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('ads.store') }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control @error('category') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                            @foreach ($category as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                           
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('category')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Title">Title</label>
                                        <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="Title" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('title')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Price">Price</label>
                                        <input type="text" name="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror" placeholder="Price" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('price')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="state">State</label>
                                        <select name="state" id="state" class="select2 form-control @error('state') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select State</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('state')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Sort Order</label>
                                        <input type="text" name="sort_order" value="{{ old('sort_order') }}" class="form-control @error('sort_order') is-invalid @enderror" placeholder="Sort Order" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('sort_order')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
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
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="subcategory">Subcategory (Optional)</label>
                                        <select name="subcategory" id="subcategory" class="form-control @error('subcategory') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('subcategory')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="CanonicalName">Canonical Name</label>
                                        <input type="text" name="canonical_name" value="{{ old('canonical_name') }}" class="form-control @error('canonical_name') is-invalid @enderror" placeholder="Canonical Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('canonical_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="country">Country</label>
                                        <select name="country" id="country" class="select2 form-control @error('country') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                            @foreach ($country as $row1)
                                                <option value="{{ $row1->id }}">{{ $row1->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('country')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="city">City</label>
                                        <select name="city" id="city" class="select2  form-control @error('city') is-invalid @enderror" autocomplete="off">
                                            <option value="1">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('city')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Image">Image (Multiple)</label>
                                        <input type="file" name="image[]" autocomplete="off" class="form-control @error('image') is-invalid @enderror" multiple>
                                        <div class="invalid-feedback">
                                            @error('image')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Description">Description</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" cols="30" rows="3" placeholder="Description" autocomplete="off">{{ old('description') }}</textarea>
                                        <div class="invalid-feedback">
                                            @error('description')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="custom_fields">
                                    
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
        $(document).ready(function() {
            $('.select2').select2();
        });

        $(document).on('change', '#category', function(){
            let id = $(this).val();
            let option = '';
            let custom_field = '';
            let select_id = '';
            let dependencyOption = '';

            $.ajax({
                url : '/change/subcategory',
                type : 'get',
                data : {id:id},
                success:function(data){

                    option += `<option value="">Select</option>`;

                    for(let i = 0; i < data.length; i++){
                        option += `<option value="${data[i].id}">${data[i].name}</option>`;
                    }

                    $('#subcategory').html(option);
                }
            });

            $.ajax({
                url : '/get/custom/field',
                type : 'get',
                data : {id:id},
                success:function(data){
                    
                    let selectoption = '<option value="">Select</option>';
                    let identity = 'select_identity';

                    for(let i = 0; i < data.length; i++){
                        
                        // for(let j = 0; j < data[i].field.length; j++){
                            
                            switch (data[i].field.type){
                                case 'text':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}">
                                        </div>`;
                                    break;
                                case 'textarea':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <textarea type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}"></textarea>
                                        </div>`;
                                    break;
                                case 'checkbox':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="checkbox" class="" name="${data[i].field.name}" value="checked" id="${data[i].field.name}" placeholder="${data[i].field.name}">
                                        </div>`;
                                    break;
                                case 'checkbox_multiple':
                                    for(let k = 0; k < data[i].field.field_option.length; k++){
                                        custom_field += `<div class="form-group col-md-6 my-2">
                                                            <div class="col-md-6">
                                                                <label for="">${data[i].field.field_option[k].value} </label>
                                                                <input type="checkbox" name="${data[i].field.field_option[k].value}" value="checked" id="${data[i].field.field_option[k].value}">
                                                            </div>
                                                        </div>`;
                                    }
                                    break;
                                case 'select':
                                    
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <select class="form-control" name="${data[i].field.name}" id="${identity}">
                                            </select>
                                        </div>`;
                                        
                                        for(let l = 0; l < data[i].field.field_option.length; l++){
                                            selectoption += `<option value="${data[i].field.field_option[l].id}">${data[i].field.field_option[l].value}</option>`;
                                        }
                                        
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
                                            <input type="file" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}">
                                        </div>`;
                                    break;
                                case 'url':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="text" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}">
                                        </div>`;
                                    break;
                                case 'number':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="number" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}">
                                        </div>`;
                                    break;
                                case 'date':
                                    custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.name}">${data[i].field.name} </label>
                                            <input type="date" class="form-control" name="${data[i].field.name}" id="${data[i].field.name}" placeholder="${data[i].field.name}">
                                        </div>`;
                                    break;

                                case 'dependency':
                                    for(let l = 0; l < data[i].field.dependency.length; l++){
                                        custom_field += `<div class="col-md-6 form-group my-2">
                                            <label for="${data[i].field.dependency[l].master}">${data[i].field.dependency[l].master} </label>
                                            <select class="form-control" onChange="masterChange('${data[i].field.dependency[l].master}')" name="${data[i].field.dependency[l].master}" id="select_dependency_${data[i].field.dependency[l].master}">
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
                    
                    $('#select_identity').html(selectoption);
                    
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
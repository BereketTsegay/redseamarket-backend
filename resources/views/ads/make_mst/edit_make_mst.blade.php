@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit Make Master</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('make_mst.index') }}">Make Master</a></li>
                <li class="breadcrumb-item active">Edit Make aster</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('make_mst.update', $make_mst->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Make Master Name</label>
                                        <input type="text" name="make_mst_name" value="{{ $make_mst->name }}" class="slug form-control @error('make_mst_name') is-invalid @enderror" placeholder="MakeMaster Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('make_mst_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- <div class="form-group my-2">
                                        <label for="Name">Icon Class</label>
                                        <select name="icon_class" id="" class="form-control @error('icon_class') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select Icon</option>
                                            @foreach ($icon as $row)
                                            @if($make_mst->icon_class_id == $row->id)
                                            <option selected value="{{ $row->id }}">{{ $row->name }}</option>
                                            @else
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('icon_class')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div> --}}
                                    {{-- <div class="form-group my-2">
                                        <label for="State">State</label>
                                        <select name="state" id="state" class=" select2 form-control @error('state') is-invalid @enderror" autocomplete="off">
                                            <option value="{{ $make_mst->state_id }}">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('state')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="City">City</label>
                                        <select name="city" id="city" class=" select2 form-control @error('city') is-invalid @enderror" autocomplete="off">
                                            <option value="{{ $make_mst->city_id }}">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('city')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div> --}}
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Sort Order</label>
                                        <input type="text" name="sort_order" value="{{ $make_mst->sort_order }}" class="form-control @error('sort_order') is-invalid @enderror" placeholder="Sort Order" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('sort_order')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <div class="row">
                                            <div class="col-md-6 my-2">
                                                <label for="Status">Active</label>
                                                <input type="checkbox" name="status" {{ $make_mst->status == 1 ? 'checked' : '' }} value="checked" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="Image">Image</label>
                                        <input type="file" name="image" autocomplete="off" class="form-control @error('image') is-invalid @enderror">
                                        <div class="invalid-feedback">
                                            @error('image')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
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

    // $(document).ready(function() {
    //     $('.select2').select2();
    // });

    // $(document).on('change', '#country', function(){
        
    //     let id = $(this).val();
    //     let option = '';

    //     $.ajax({
    //         url : '/global/state/get',
    //         type : 'get',
    //         data : {id:id},
    //         success:function(data){

    //             option += `<option value="">Select</option>`;

    //             for(let i = 0; i < data.length; i++){
    //                 option += `<option value="${data[i].id}">${data[i].name}</option>`;
    //             }

    //             $('#state').html(option);
    //         }
    //     });
    // });

    // $(document).on('change', '#state', function(){

    //     let id = $(this).val();
    //     let option = '';

    //     $.ajax({
    //         url : '/global/city/get',
    //         type : 'get',
    //         data : {id:id},
    //         success:function(data){

    //             option += `<option value="">Select</option>`;

    //             for(let i = 0; i < data.length; i++){
    //                 option += `<option value="${data[i].id}">${data[i].name}</option>`;
    //             }

    //             $('#city').html(option);
    //         }
    //     });
    // });

    // $(window).on('load', function(){
        
    //     let id = $('#country').find(':selected').val();
    //     let state = $('#state').find(':selected').val();
    //     let option = '';

    //     $.ajax({
    //         url : '/global/state/get',
    //         type : 'get',
    //         data : {id:id},
    //         success:function(data){

    //             for(let i = 0; i < data.length; i++){
    //                 if(data[i].id == state){
    //                     option += `<option selected value="${data[i].id}">${data[i].name}</option>`;
    //                 }
    //                 else{
    //                     option += `<option value="${data[i].id}">${data[i].name}</option>`;
    //                 }
    //             }

    //             $('#state').html(option);
    //         }
    //     });
    // });

    // $(window).on('load', function(){

    //     let id = $('#state').find(':selected').val();
    //     let city = $('#city').find(':selected').val();
    //     let option = '';

    //     $.ajax({
    //         url : '/global/city/get',
    //         type : 'get',
    //         data : {id:id},
    //         success:function(data){

    //             for(let i = 0; i < data.length; i++){

    //                 if(data[i].id == city){
    //                     option += `<option selected value="${data[i].id}">${data[i].name}</option>`;
    //                 }
    //                 else{
    //                     option += `<option value="${data[i].id}">${data[i].name}</option>`;
    //                 }
    //             }

    //             $('#city').html(option);
    //         }
    //     });
    // });
</script>
 
@endpush
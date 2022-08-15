@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit VarientMaster</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('varient_mst.index') }}">VarientMaster</a></li>
                <li class="breadcrumb-item active">Edit VarientMaster</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('varient_mst.update', $varient_mst->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">VarientMaster Name</label>
                                        <input type="text" name="varient_mst_name" value="{{ $varient_mst->name }}" class="slug form-control @error('varient_mst_name') is-invalid @enderror" placeholder="VarientMaster Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('varient_mst_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    </div><div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="Name">Make Master</label>
                                        <select name="model_id"  class="slug form-control @error('model_id') is-invalid @enderror" >
                                          <option value="">Select Make</option>
                                          @foreach($models as $model)
                                          <option value="{{$model->id}}" {{($model->id==$varient_mst->model_id)?"selected":""}}>{{$model->name}}</option>
                                          @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('model_id')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    </div><div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Sort Order</label>
                                        <input type="text" name="order" value="{{ $varient_mst->order }}" class="form-control @error('order') is-invalid @enderror" placeholder="Sort Order" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('order')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <div class="row">
                                            <div class="col-md-6 my-2">
                                                <label for="Status">Active</label>
                                                <input type="checkbox" name="status" {{ $varient_mst->status == 1 ? 'checked' : '' }} value="checked" autocomplete="off">
                                            </div>
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
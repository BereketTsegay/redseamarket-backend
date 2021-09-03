@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Create Category</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
                <li class="breadcrumb-item active">Create Category</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Category Name</label>
                                        <input type="text" name="category_name" value="{{ old('category_name') }}" class="slug form-control @error('category_name') is-invalid @enderror" placeholder="Category Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('category_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- <div class="form-group my-2">
                                        <label for="Name">Icon Class</label>
                                        <select name="icon_class" id="" class="form-control @error('icon_class') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select Icon</option>
                                            @foreach ($icon as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('icon_class')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div> --}}
                                    {{-- <div class="form-group my-2">
                                        <label for="Name">State</label>
                                        <select name="state" id="state" class="select2 form-control @error('state') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('city')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Name">City</label>
                                        <select name="city" id="city" class="select2 form-control @error('city') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('city')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div> --}}
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Sort Order</label>
                                        <input type="text" name="sort_order" value="{{ old('sort_order') }}" class="form-control @error('sort_order') is-invalid @enderror" placeholder="Sort Order" autocomplete="off">
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
                                                <input type="checkbox" checked name="status" value="checked" autocomplete="off">
                                            </div>
                                            <div class="col-md-6 my-2">
                                                <label for="Status">Display In Front Page</label>
                                                <input type="checkbox" {{ old('display_flag') == 'on' ? 'checked' : '' }} name="display_flag" autocomplete="off">
                                                @if(Session::has('status_error'))
                                                <p class="text-danger">{{ Session::get('status_error') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="Name">Canonical Name</label>
                                        <input type="text" name="canonical_name" value="{{ old('canonical_name') }}" id="canonical_name" class="form-control @error('canonical_name') is-invalid @enderror" placeholder="Canonical Name" autocomplete="off" readonly>
                                        <div class="invalid-feedback">
                                            @error('canonical_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- <div class="form-group my-2">
                                        <label for="Name">Country</label>
                                        <select name="country" id="country" class="select2 form-control @error('country') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                            @foreach ($country as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>                                                
                                            @endforeach
                                            
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('country')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div> --}}
                                    <div class="form-group my-2">
                                        <label for="Image">Image</label>
                                        <input type="file" name="image" autocomplete="off" class="image1 form-control @error('image') is-invalid @enderror">
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
</script>


@endpush
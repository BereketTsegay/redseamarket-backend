@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit City</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cities.index') }}">City</a></li>
                <li class="breadcrumb-item active">Edit City</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('cities.update',$data->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">City Name</label>
                                        <input type="text" name="name" value="{{ $data->name }}" class="slug form-control @error('name') is-invalid @enderror" placeholder=" Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                    <div class="col-md-6">
                                        <div class="form-group my-2">
                                            <label for="Name">State</label>
                                            <select name="state" id="" class="form-control @error('state') is-invalid @enderror" autocomplete="off">
                                                <option value="">Select State</option>
                                                @foreach ($states as $state)
                                                <option value="{{ $state->id }}" {{$data->state_id == $state->id ? 'selected': '' }}>{{ $state->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                @error('state')
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
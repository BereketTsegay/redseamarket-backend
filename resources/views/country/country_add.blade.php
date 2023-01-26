@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Create Country</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('country_currency.index') }}">Country</a></li>
                <li class="breadcrumb-item active">Create Country</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('countries.store') }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Country Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="slug form-control @error('name') is-invalid @enderror" placeholder=" Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group my-2">
                                        <label for="SortOrder">Country Code</label>
                                        <input type="text" name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror" placeholder="code" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('code')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                  
                                </div>
                                    <div class="col-md-6">
                                    
                                       
                                        <div class="form-group my-2">
                                            <label for="SortOrder">Phone Code</label>
                                            <input type="text" name="phonecode" value="{{ old('phonecode') }}" class="form-control @error('phonecode') is-invalid @enderror" placeholder="phone code" autocomplete="off">
                                            <div class="invalid-feedback">
                                                @error('phonecode')
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


@endpush
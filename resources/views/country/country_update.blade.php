@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit Country</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('country_currency.index') }}">Country</a></li>
                <li class="breadcrumb-item active">Edit Country</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('countries.update',$data->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Country Name</label>
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
                                        <label for="SortOrder">Country Code</label>
                                        <input type="text" name="code" value="{{ $data->code }}" class="form-control @error('code') is-invalid @enderror" placeholder="code" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('code')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                  
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    
                                       
                                        <div class="form-group my-2">
                                            <label for="SortOrder">Phone Code</label>
                                            <input type="text" name="phonecode" value="{{ $data->phonecode }}" class="form-control @error('phonecode') is-invalid @enderror" placeholder="phone code" autocomplete="off">
                                            <div class="invalid-feedback">
                                                @error('phonecode')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>
                                </div>

                                <div class="col-md-6">
                                    
                                       
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Phone Number Length(for validation)</label>
                                        <input type="number" name="phonelength" value="{{ $data->phone_length }}" class="form-control @error('phonelength') is-invalid @enderror" placeholder="length" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('phonelength')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                            </div>
                                
                            </div>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="flag">Flag</label>
                                        <input type="file" name="flag" autocomplete="off" class="flag form-control @error('flag') is-invalid @enderror">
                                        <div class="invalid-feedback">
                                            @error('flag')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="status"  id="flexCheckChecked" @if($data->status) checked @endif>
                                        <label class="form-check-label" for="flexCheckChecked">
                                         Active
                                        </label>
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


@endpush
@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Create Currency</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('country_currency.index') }}">Currency</a></li>
                <li class="breadcrumb-item active">Create Currency</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('country_currency.store') }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Currency Name</label>
                                        <input type="text" name="currency_name" value="{{ old('currency_name') }}" class="slug form-control @error('currency_name') is-invalid @enderror" placeholder="Currency Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('currency_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group my-2">
                                        <label for="SortOrder">Currency Prefix</label>
                                        <input type="text" name="currency_code" value="{{ old('currency_code') }}" class="form-control @error('currency_code') is-invalid @enderror" placeholder="Currency Prefix" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('currency_code')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>

                                  
                                </div>
                                    <div class="col-md-6">
                                    
                                        <div class="form-group my-2">
                                            <label for="Name">Country</label>
                                            <select name="country" id="" class="form-control @error('country') is-invalid @enderror" autocomplete="off">
                                                <option value="">Select Country</option>
                                                @foreach ($datas as $county)
                                                <option value="{{ $county->id }}">{{ $county->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                @error('country')
                                                    {{ $message }}
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group my-2">
                                            <label for="SortOrder">Currency Value(1)</label>
                                            <input type="text" name="currency_value" value=" {{ old('currency_value') }}" class="form-control @error('currency_value') is-invalid @enderror" placeholder="usd" autocomplete="off">
                                            <div class="invalid-feedback">
                                                @error('currency_value')
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
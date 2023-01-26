@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Expiry Category</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
                <li class="breadcrumb-item active">Expiry Category</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('category.expiry.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    @csrf
                                    
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Ads Expire Days</label>
                                        <input type="text" name="expire_days" value="{{ $category->expire_days}}" class="form-control @error('expire_days') is-invalid @enderror" placeholder="Expire Days" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('expire_days')
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

 

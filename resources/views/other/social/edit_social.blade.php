@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit Social Links</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Social Links</a></li>
                <li class="breadcrumb-item active">Edit Social Links</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('social.update', $social->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Name</label>
                                        <input type="text" name="name" value="{{ $social->name }}" class="form-control @error('name') is-invalid @enderror" placeholder="Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Name">Icon Class</label>
                                        <select name="icon" id="" class="form-control @error('icon') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select Icon</option>
                                            @foreach ($icon as $row)
                                            @if($social->icon == $row->id)
                                            <option selected value="{{ $row->id }}">{{ $row->name }}</option>
                                            @else
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('icon')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Status">Active</label>
                                        <input type="checkbox" name="status" {{ $social->status == 1 ? 'checked' : '' }} autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="Url">Url</label>
                                        <input type="text" name="url" value="{{ $social->url }}" class="form-control @error('url') is-invalid @enderror" placeholder="Url" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('url')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
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

@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit Subcategory</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('subcategory.index') }}">Subcategory</a></li>
                <li class="breadcrumb-item active">Edit Subcategory</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('subcategory.update', $subcategory->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Category</label>
                                        <select name="category" id="" class="form-control @error('category') is-invalid @enderror" autocomplete="off">
                                            
                                            @foreach ($category as $row)
                                                @if($subcategory->category_id == $row->id)
                                                <option selected value="category_{{ $row->id }}">{{ $row->name }}</option>
                                                @else
                                                <option value="category_{{ $row->id }}">{{ $row->name }}</option>
                                                @endif

                                                @foreach ($row->Subcategory as $item)
                                                @if($item->parent_id == 0)
                                                    @if($subcategory->parent_id == $item->id)
                                                    <option selected value="subcategory_{{ $row->id }}_{{ $item->id }}">----| {{ $item->name }}</option>
                                                    @else
                                                    <option value="subcategory_{{ $row->id }}_{{ $item->id }}">----| {{ $item->name }}</option>
                                                    @endif
                                                @endif
                                                @endforeach
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('category')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Name">Canonical Name</label>
                                        <input type="text" name="canonical_name" value="{{ $subcategory->canonical_name }}" id="canonical_name" class="form-control @error('canonical_name') is-invalid @enderror" placeholder="Canonical Name" readonly autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('canonical_name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Type">Type</label>
                                        <select name="type" id="Type" class="form-control @error('type') is-invalid @enderror" autocomplete="off">
                                            @if ($subcategory->type == 0)
                                                <option selected value="0">Flat</option>
                                                <option value="1">Percentage</option>
                                            @else
                                                <option value="0">Flat</option>
                                                <option selected value="1">Percentage</option>
                                            @endif
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('type')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Sort Order</label>
                                        <input type="text" name="sort_order" value="{{ $subcategory->sort_order }}" class="form-control @error('sort_order') is-invalid @enderror" placeholder="Sort Order" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('sort_order')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Status">Active</label>
                                        <input type="checkbox" name="status" {{ $subcategory->status == 1 ? 'checked' : '' }} value="checked" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="Name">Subcategory Name</label>
                                        <input type="text" name="subcategory_name" value="{{ $subcategory->name }}" class="slug form-control @error('subcategory_name') is-invalid @enderror" placeholder="Subategory Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('subcategory_name')
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
                                    <div class="form-group my-2">
                                        <label for="SortOrder">Value</label>
                                        <input type="text" name="value" value="{{ $subcategory->percentage }}" class="form-control @error('value') is-invalid @enderror" placeholder="Value" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('value')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="Description">Description</label>
                                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" cols="30" rows="3" placeholder="Description" autocomplete="off">{{ $subcategory->description }}</textarea>
                                        <div class="invalid-feedback">
                                            @error('description')
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
</script>
@endpush
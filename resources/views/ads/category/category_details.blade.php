@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Category Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
                <li class="breadcrumb-item active">Category Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $category->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Canonical Name :</p>
                                    <p class="col-md-6">{{ $category->canonical_name }}</p>
                                </div>
                                {{-- <div class="row">
                                    <p class="col-md-6">Country :</p>
                                    <p class="col-md-6">{{ $category->Country->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">State :</p>
                                    <p class="col-md-6">{{ $category->State->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">City :</p>
                                    <p class="col-md-6">{{ $category->City->name }}</p>
                                </div> --}}
                                <div class="row">
                                    <p class="col-md-6">Sort Order :</p>
                                    <p class="col-md-6">{{ $category->sort_order }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $category->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Description :</p>
                                    <p class="col-md-6">{{ $category->description }}</p>
                                </div>
                                @if(count($category->CustomField) !=0 )
                                <hr>
                                <h5>Custom Fields</h5>
                                <hr>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Field</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($category->CustomField as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $row->Field->name }}</td>
                                                <td>{{ $row->Field->type }}</td>
                                                <td><button onclick="customFieldDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal"><i class="fas fa-trash"></i></button>
                                                <form id="custom_field_delete_form{{$row->id}}" action="{{ route('custom_field.deletefromcategory', $row->id ) }}" method="POST">@csrf</form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5 class="my-4">Image</h5>
                                <hr>
                                <a href="{{ asset($category->image) }}" target="blank"><img src="{{ asset($category->image) }}" alt="image" width="250px"></a>
                                <hr>
                                {{-- <h5 class="my-4">Icon</h5>
                                <hr>
                                <i class="{{ $category->Icon->name }}" style="font-size: 50px"></i> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="deleteCustomField()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>
    
    <script>
        let ids = '';

        customFieldDelete = (id) => {
            ids = id;
        }

        deleteCustomField = () => {
            $('#custom_field_delete_form'+ids).submit();
        }
    </script>
@endpush
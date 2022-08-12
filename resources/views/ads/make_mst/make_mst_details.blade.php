@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">MakeMaster Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('make_mst.index') }}">MakeMaster</a></li>
                <li class="breadcrumb-item active">MakeMaster Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $make_mst->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Sort Order :</p>
                                    <p class="col-md-6">{{ $make_mst->sort_order }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $make_mst->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="my-4">Image</h5>
                                <hr>
                                <a href="{{ asset($make_mst->image) }}" target="blank"><img src="{{ asset($make_mst->image) }}" alt="image" width="250px"></a>
                               
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
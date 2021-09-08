@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#createIconModal">Create Featured Dealer</button>
            
            <h2 class="mt-4">Featured Dealer</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Featured Dealer</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Featured Dealer
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($featured as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->dealer_name }}</td>
                                    <td><a href="{{ asset($row->dealer_image) }}" target="blank"><img src="{{ asset($row->dealer_image) }}" width="50px" alt=""></a></td>
                                    @if($row->status == 1)
                                    <td class="text-success">Active</td>
                                    @else
                                    <td class="text-secondary">Disabled</td>
                                    @endif
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu text-center">
                                                <button type="button" onclick="iconEdit({{$row->id}}, '{{$row->dealer_name}}')" class="btn btn-secondary" data-toggle="modal" data-target="#editIconModal">Edit</button>
                                                <button type="button" onclick="iconDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteIconModal">Delete</button>
                                                <form id="delete_icon_form{{$row->id}}" action="{{ route('dealer.delete', $row->id) }}" method="POST">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    
<div class="modal fade" id="deleteIconModal" tabindex="-1" role="dialog" aria-labelledby="deleteIconModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="deleteIconModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deteteIcon()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
  </div>

  {{-- Create Icon Modal --}}

    <div class="modal fade" id="createIconModal" tabindex="-1" role="dialog" aria-labelledby="createIconModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('dealer.store') }}" method="POST" id="createIcon" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createIconModalLabel">Create Icon</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group my-2">
                                <label for="Name">Name</label>
                                <input type="text" name="name" class="form-control" autocomplete="off" placeholder="Name">
                            </div>
                            <div class="form-group my-2">
                                <label for="SortOrder">Image</label>
                                <input type="file" name="image" class="form-control" autocomplete="off" >
                            </div>
                            <div class="form-group my-2">
                                <label for="Status">Active</label>
                                <input type="checkbox" checked name="status" value="checked" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


{{-- Edit Icon Modal --}}

    <div class="modal fade" id="editIconModal" tabindex="-1" role="dialog" aria-labelledby="editIconModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('dealer.update') }}" method="POST" id="editIcon" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editIconModalLabel">Edit Icon</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group my-2">
                                <label for="Name">Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" autocomplete="off" placeholder="Name">
                            </div>
                            <div class="form-group my-2">
                                <label for="SortOrder">Image</label>
                                <input type="file" name="image" class="form-control" autocomplete="off" >
                            </div>
                            <div class="form-group my-2">
                                <label for="Status">Active</label>
                                <input type="checkbox" checked name="status" value="checked" autocomplete="off">
                            </div>

                            <input type="hidden" name="id" id="editId">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

  <script>
        
        let ids = '';

        iconDelete = id => {
            ids = id
        }

        deteteIcon = () => {
            
            $('#delete_icon_form'+ids).submit();
        }

        iconEdit = (iconId, name) => {
            $('#editId').val(iconId);
            $('#edit_name').val(name);
        }

        $('form[id="createIcon"]').validate({
            rules : {
                name: {
                        required : true,
                    },
                image: {
                        required: true,
                    },
            },
            submitHandler: function(form) {
                form.submit();
            }
            
        });

        $('form[id="editIcon"]').validate({
            rules : {
                name: {
                        required : true,
                    },
                image: {
                        required: true,
                    },
            },
            submitHandler: function(form) {
                form.submit();
            }
            
        });
  </script>

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
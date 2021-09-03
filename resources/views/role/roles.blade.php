@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="#" data-toggle="modal" data-target="#createTestimonialModal"><button type="button" class="btn btn-primary float-end">Create Role</button></a>
            
            <h2 class="mt-4">Roles</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Roles
                        </div>
                        <div class="card-body">
                            <table id="datatablesSimple" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        {{-- <th>View</th> --}}
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($role as $row)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->description }}</td>
                                            <td>
                                                @if ($row->status == 1)
                                                    <span class="text-success">Active</span>
                                                @else
                                                    <span class="text-secondary">Disabled</span>
                                                @endif
                                            </td>
                                            {{-- <td><a href="{{ route('testimonial.view', $row->id) }}"><button class="btn btn-primary">View</button></a></td> --}}
                                            {{-- <td><a href="javascript:void(0);"><button onclick="editRole({{$row->id}}, '{{$row->name}}', '{{$row->description}}')" class="btn btn-secondary" data-toggle="modal" data-target="#editRoleModal">Edit</button></a></td> --}}
                                            <td><a href="{{ route('role.edit', $row->id) }}"><button class="btn btn-secondary">Edit</button></a></td>
                                            <td><button type="button" onclick="bannerDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteBannerModal">Delete</button>
                                            <form id="delete_Banner_form{{$row->id}}" action="{{ route('role.delete', $row->id) }}" method="POST">
                                                @csrf
                                            </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Task Role Maping
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <form action="{{ route('task_role.store') }}" method="POST" id="roleMaping">
                                    @csrf
                                    <div class="form-group mb-2">
                                        <label for="Role">Role</label>
                                        <select name="role" class="form-control" id="role">
                                            <option value="">Select Role</option>
                                            @foreach ($role as $row1)
                                                <option {{ old('role') == $row1->id ? 'selected' : '' }} value="{{ $row1->id }}">{{ $row1->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="row">
                                        <label for="Task">Task List</label>
                                        @foreach ($task as $item)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="checkbox" name="task[]" value="{{ $item->id }}" id="">
                                                    <label for="Task">{{ $item->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Store</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    
<div class="modal fade" id="deleteBannerModal" tabindex="-1" role="dialog" aria-labelledby="deleteBannerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="deleteBannerModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deteteBanner()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createTestimonialModal" tabindex="-1" role="dialog" aria-labelledby="createTestimonialModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('role.store') }}" method="POST" enctype="multipart/form-data" id="testimonial">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title" id="createTestimonialModalModalLabel">Create Roles</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Name">Name</label>
                            <input type="text" value="{{ old('name') }}" name="name" class="form-control" id="Name" placeholder="Name">
                        </div>
                        <div class="form-group my-2">
                            <label for="Description">Description</label>
                            <textarea name="description" class="form-control" id="Description" cols="30" rows="3" placeholder="Description">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group my-2">
                            <input type="checkbox" name="status" checked>
                            <label for="status">Status</label>
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

{{-- <div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('role.update') }}" method="POST" enctype="multipart/form-data" id="editRole">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title" id="editRoleModalLabel">Create Roles</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Name">Name</label>
                            <input type="text" value="{{ old('name') }}" name="name" class="form-control" id="editName" placeholder="Name">
                        </div>
                        <div class="form-group my-2">
                            <label for="Description">Description</label>
                            <textarea name="description" class="form-control" id="editDescription" cols="30" rows="3" placeholder="Description">{{ old('description') }}</textarea>
                        </div>
                        <input type="hidden" name="id" id="editId">
                        <div class="form-group my-2">
                            <input type="checkbox" name="status" checked>
                            <label for="status">Status</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}

  <script>
        
        let ids = '';

        bannerDelete = id => {
            ids = id
        }

        deteteBanner = () => {
            
            $('#delete_Banner_form'+ids).submit();
        }

        $('form[id="testimonial"]').validate({
            rules : {
                name: {
                        required : true,
                    },
                description: {
                        required: true,
                    },
                },

            submitHandler: function(form) {
                form.submit();
            }
        });

        $('form[id="roleMaping"]').validate({
            rules : {
                role: {
                        required : true,
                    },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        // editRole = (id, name, description) => {

        //     $('#editName').val(name);
        //     $('#editId').val(id);
        //     $('#editDescription').val(description);
        // }

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
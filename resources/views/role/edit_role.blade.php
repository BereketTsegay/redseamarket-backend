@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit Role</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Role</a></li>
                <li class="breadcrumb-item active">Edit Role</li>
            </ol>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            {{-- <i class="fas fa-table me-1"></i> --}}
                            Edit Roles
                        </div>
                        <div class="card-body">
                            <form action="{{ route('role.update') }}" method="POST" enctype="multipart/form-data" id="editRole">
                                @csrf
                                <div class="container">
                                    <div class="form-group my-2">
                                        <label for="Name">Name</label>
                                        <input type="text" value="{{ $role->name }}" name="name" class="form-control" id="editName" placeholder="Name">
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Description">Description</label>
                                        <textarea name="description" class="form-control" id="editDescription" cols="30" rows="3" placeholder="Description">{{ $role->description }}</textarea>
                                    </div>
                                    <input type="hidden" name="id" id="editId">
                                    <div class="form-group my-2">
                                        <input type="checkbox" name="status" {{ $role->status == 1 ? 'checked' : '' }}>
                                        <label for="status">Status</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary mb-4">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            {{-- <i class="fas fa-table me-1"></i> --}}
                            Edit Task Role Maping 
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <form action="{{ route('task_role.update', $role->id) }}" method="POST" id="roleMaping">
                                    @csrf
                                    <div class="row">
                                        <label for="Task">Task List</label>
                                        @foreach ($task as $item)
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <input type="checkbox" name="task[]" {{ $item->TaskRole ? 'checked' : ''}} value="{{ $item->id }}" id="">
                                                    <label for="Task">{{ $item->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update</button>
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
    <script>
        $('form[id="editRole"]').validate({
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
    </script>
@endpush

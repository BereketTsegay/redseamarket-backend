@extends('layout')

@section('content')
    <main>
        <div class="container-fluid px-4">

            <a href="{{ route('admin_user.create') }}"><button type="button" class="btn btn-primary float-end">Create User</button></a>
            
            <h1 class="mt-4">Admin Users</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Admin Users</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Admin Users
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                                    <td>{{ $row->email }}</td>
                                    <td>
                                        @if ($row->status == 1)
                                            <span class="text-success">Active</span>
                                        @else
                                            <span class="text-disable">Disabled</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu text-center">
                                                <a href="{{ route('admin_user.view', $row->id) }}"><button class="btn btn-primary my-2">View</button></a>
                                                <a href="{{ route('admin_user.edit', $row->id) }}"><button class="btn btn-secondary my-2">Edit</button></a>
                                                <button type="button" onclick="userDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button>
                                                <form id="deleteUserForm{{$row->id}}" action="{{ route('user.delete', $row->id) }}" method="POST">
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
    
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <button type="button" onclick="deleteUser()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
  </div>


  <script>

      let user_id = '';

      userDelete = (id) => {
        user_id = id;

      }

      deleteUser = () => {
        
          $('#deleteUserForm'+user_id).submit();
      }

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
@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="{{ route('cities.create') }}"><button type="button" class="btn btn-primary float-end">Create City</button></a>
            
            <h2 class="mt-4">City</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">City</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    City
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>City</th>
                                <th>State </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cities as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>@if($row->State){{ $row->State->name }}@endif</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu text-center">

                                                    <a href="{{ route('cities.edit', $row->id) }}"><button class="btn btn-secondary my-2">Edit</button></a>
                                                   
                                                    <button type="button" onclick="itemDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteItemModal">Delete</button>
                                                    <form id="delete_item_form{{$row->id}}" action="{{ route('cities.delete', $row->id) }}" method="POST">
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
    
<div class="modal fade" id="deleteItemModal" tabindex="-1" role="dialog" aria-labelledby="deleteMakeMasterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="deleteMakeMasterModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deteteCity()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

  <script>
        
        let ids = '';

        itemDelete = id => {
            ids = id
        }

        deteteCity = () => {
            
            $('#delete_item_form'+ids).submit();
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
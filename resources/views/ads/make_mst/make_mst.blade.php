@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="{{ route('make_mst.create') }}"><button type="button" class="btn btn-primary float-end">Create Make Master</button></a>
            
            <h2 class="mt-4">Make Masters</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Make Master</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Make Master
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($make_mst as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
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
                                                <a href="{{ route('make_mst.view', $row->id) }}"><button class="btn btn-primary my-2" class="">View</button></a>

                                                    <a href="{{ route('make_mst.edit', $row->id) }}"><button class="btn btn-secondary my-2">Edit</button></a>
                                                    @if(!($row->ModelMsts()->exists()||$row->MotorCustomeValues()->exists()))
                                                    <button type="button" onclick="make_mstDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteMakeMasterModal">Delete</button>
                                                    <form id="delete_make_mst_form{{$row->id}}" action="{{ route('make_mst.delete', $row->id) }}" method="POST">
                                                        @csrf
                                                    </form>
                                                    @endif
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
    
<div class="modal fade" id="deleteMakeMasterModal" tabindex="-1" role="dialog" aria-labelledby="deleteMakeMasterModalLabel" aria-hidden="true">
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
                <button type="button" onclick="deteteMakeMaster()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

  <script>
        
        let ids = '';

        make_mstDelete = id => {
            ids = id
        }

        deteteMakeMaster = () => {
            
            $('#delete_make_mst_form'+ids).submit();
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
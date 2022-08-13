@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="{{ route('model_mst.create') }}"><button type="button" class="btn btn-primary float-end">Create ModelMaster</button></a>
            
            <h2 class="mt-4">ModelMasters</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">ModelMaster</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    ModelMaster
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Make Name</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($model_mst as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->MakeMst->name }}</td>
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
                                             
                                                    <a href="{{ route('model_mst.edit', $row->id) }}"><button class="btn btn-secondary my-2">Edit</button></a>
                                                    @if(!($row->VarientMsts()->exists()||$row->MotorCustomeValues()->exists()))
                                                    <button type="button" onclick="model_mstDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteModelMasterModal">Delete</button>
                                                    <form id="delete_model_mst_form{{$row->id}}" action="{{ route('model_mst.delete', $row->id) }}" method="POST">
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
    
<div class="modal fade" id="deleteModelMasterModal" tabindex="-1" role="dialog" aria-labelledby="deleteModelMasterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="deleteModelMasterModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deteteModelMaster()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

  <script>
        
        let ids = '';

        model_mstDelete = id => {
            ids = id
        }

        deteteModelMaster = () => {
            
            $('#delete_model_mst_form'+ids).submit();
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
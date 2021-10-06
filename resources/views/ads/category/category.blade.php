@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="{{ route('category.create') }}"><button type="button" class="btn btn-primary float-end">Create Category</button></a>
            
            <h2 class="mt-4">Categories</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Category</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Category
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Canonical Name</th>
                                <th>Active</th>
                                <th>Action</th>
                                {{-- <th>Edit</th>
                                <th>Delete</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($category as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->canonical_name }}</td>
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
                                                <a href="{{ route('category.view', $row->id) }}"><button class="btn btn-primary my-2" class="">View</button></a>
                                                
                                                @if ($row->reserved_flag == 1)
                                                    <a href="#" title="Can't delete, reserved category"><button class="btn btn-secondary my-1">Can't edit, reserved category</button></a>
                                                @else
                                                    <a href="{{ route('category.edit', $row->id) }}"><button class="btn btn-secondary my-2">Edit</button></a>
                                                @endif
                                                
                                                @if ($row->reserved_flag == 1)
                                                    <button type="button" class="btn btn-danger my-1" title="Can't delete, reserved category">Can't Delete, reserved category</button>
                                                @else
                                                    <button type="button" onclick="categoryDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteCategoryModal">Delete</button>
                                                    <form id="delete_category_form{{$row->id}}" action="{{ route('category.delete', $row->id) }}" method="POST">
                                                        @csrf
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    {{-- <td><a href="{{ route('category.view', $row->id) }}"><button class="btn btn-primary">View</button></a></td>
                                    <td>
                                        @if ($row->reserved_flag == 1)
                                            <a href="#" title="Can't delete, reserved category"><button class="btn btn-secondary">Can't edit, reserved category</button></a>
                                        @else
                                            <a href="{{ route('category.edit', $row->id) }}"><button class="btn btn-secondary">Edit</button></a>
                                        @endif
                                        
                                    </td>
                                    <td>
                                        @if ($row->reserved_flag == 1)
                                            <button type="button" class="btn btn-danger" title="Can't delete, reserved category">Can't Delete, reserved category</button>
                                        @else
                                            <button type="button" onclick="categoryDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteCategoryModal">Delete</button>
                                            <form id="delete_category_form{{$row->id}}" action="{{ route('category.delete', $row->id) }}" method="POST">
                                                @csrf
                                            </form>
                                        @endif
                                    </td> --}}
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
    
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="deleteCategoryModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deteteCategory()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

  <script>
        
        let ids = '';

        categoryDelete = id => {
            ids = id
        }

        deteteCategory = () => {
            
            $('#delete_category_form'+ids).submit();
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
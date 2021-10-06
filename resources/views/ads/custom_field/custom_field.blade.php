@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="{{ route('custom_field.create') }}"><button type="button" class="btn btn-primary float-end">Create Custom Field</button></a>
            
            <h2 class="mt-4">Custom Field</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Custom Field</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Custom Field
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Options</th>
                                <th>Status</th>
                                <th>Action</th>
                                {{-- <th>Edit</th>
                                <th>Delete</th> --}}
                                <th>Add to Category</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($field as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->type }}</td>
                                    <td><span class="badge badge-primary">{{ count($row->CategoryField) }} Category</span></td>
                                    <td>{!! $row->option == 1 ? '<span class="badge badge-primary mx-2">'. count($row->FieldOption) .'</span>' : 'Null' !!}</td>
                                    <td>{!! $row->status == 1 ? '<span class="text-success">Active<span>' : '<span class="text-secondary">Disabled<span>' !!}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                              Action
                                            </button>
                                            <div class="dropdown-menu text-center">
                                                <a href="{{ route('custom_field.view', $row->id) }}"><button type="button" class="btn btn-primary my-2">View</button></a>
                                                <a href="{{ route('custom_field.edit', $row->id) }}"><button type="button" class="btn btn-secondary my-2">Edit</button></a>
                                                <button type="button" onclick="customFieldDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                                                <form id="custom_field_delete_form{{$row->id}}" action="{{ route('custom_field.delete', $row->id) }}" method="POST">@csrf</form>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- <td><a href="{{ route('custom_field.view', $row->id) }}"><button type="button" class="btn btn-primary">View</button></a></td>
                                    <td><a href="{{ route('custom_field.edit', $row->id) }}"><button type="button" class="btn btn-secondary">Edit</button></a></td>
                                    <td><button type="button" onclick="customFieldDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                                        <form id="custom_field_delete_form{{$row->id}}" action="{{ route('custom_field.delete', $row->id) }}" method="POST">@csrf</form>
                                    </td> --}}
                                    <td><button type="button" onclick="addtoCategory({{$row->id}}, '{{$row->name}}')" class="btn btn-info" data-toggle="modal" data-target="#addtoCategoryModal">Add to Category</button></a></td>
                                    <td>
                                        @if($row->option == 1)
                                            <a href="{{ route('custom_field.option.index', $row->id) }} "><button type="button" class="btn btn-success">Option</button></a>
                                        @endif
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

{{-- Add to category modal --}}

<div class="modal fade" id="addtoCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addtoCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('custom_field.addtocategory') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addtoCategoryModalLabel">Add To Category</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group my-2">
                        <label for="Category">Category</label>
                        <Select name="category" class="form-control" id="Category" autocomplete="off">
                            <option value="">Select Category</option>
                            @foreach ($category as $item)
                                <option value="category_{{ $item->id }}">{{ $item->name }}</option>
                                {{-- @foreach ($item->Subcategory as $row1)
                                    <option value="subcategory_{{ $item->id }}_{{ $row1->id }}">-----| {{ $row1->name }}</option>
                                @endforeach --}}
                            @endforeach
                        </Select>
                    </div>
                    <div class="form-group my-2">
                        <input type="checkbox" name="disabled" id="disableFor" value="checked" autocomplete="off">
                        <Label for="disableFor">Disabled for Subcategories</Label>
                    </div>
                    <input type="hidden" name="field_id" id="field_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script>
        let ids = '';
        customFieldDelete = id => {
            ids = id;
        }

        deleteCustomField = () => {
            $('#custom_field_delete_form'+ids).submit();
        }


        addtoCategory = (id, name) => {
            $('#addtoCategoryModalLabel').html(name+' Add To Category');
            $('#field_id').val(id);
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

@if (Session::has('warning'))
<script>
    Swal.fire({
        icon: 'warning',
        text: "{{ Session::get('warning') }}",
    })
</script>
@endif
@endpush
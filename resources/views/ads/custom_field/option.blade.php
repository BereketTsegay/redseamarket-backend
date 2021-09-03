@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#optionCreate">Create Option</button>
            
            <h2 class="mt-4">Option for {{ $field->name }}</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('custom_field.index') }}">Custom Field</a></li>
                <li class="breadcrumb-item active">Option</li>
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
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($option as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->value }}</td>
                                    <td><button type="button" onclick="optionDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                                        <form id="option_delete_form{{$row->id}}" action="{{ route('custom_field.option.delete', $row->id) }}" method="POST">@csrf</form>
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
                <button type="button" onclick="deleteOption()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

{{-- Option Create Modal --}}

<div class="modal fade" id="optionCreate" tabindex="-1" role="dialog" aria-labelledby="optionCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('custom_field.option.create', $field->id) }}" method="POST" id="optionForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="optionCreateModalLabel">Create Option</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <Label for="Value">Value</Label>
                        <input type="text" name="value" id="value" class="form-control" placeholder="Value" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" onclick="" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <script>
        let ids = '';
        optionDelete = id => {
            ids = id;
        }

        deleteOption = () => {
            $('#option_delete_form'+ids).submit();
        }

        $('form[id="optionForm"]').validate({
            rules : {
                value: {
                        required : true,
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
        text: "{{ Session::get('success') }}",
    })
</script>
@endif

@if (Session::has('error'))
<script>
    Swal.fire({
        icon: 'error',
        text: "{{ Session::get('error') }}",
    })
</script>
@endif
@endpush
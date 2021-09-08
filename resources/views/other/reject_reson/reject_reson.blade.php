@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <button type="button" class="btn btn-primary float-end" data-toggle="modal" data-target="#createIconModal">Create Reson</button>
            
            <h2 class="mt-4">Reson</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Reson</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Reson
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Reson</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reson as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->reson }}</td>
                                    @if($row->type == 0)
                                    <td>Reject</td>
                                    @else
                                    <td>Refund</td>
                                    @endif
                                    <td>{{ $row->description }}</td>
                                    @if($row->status == 1)
                                    <td class="text-success">Active</td>
                                    @else
                                    <td class="text-secondary">Disabled</td>
                                    @endif
                                    <td><button type="button" onclick="resonEdit({{$row->id}}, '{{$row->reson}}', {{$row->type}}, '{{$row->description}}', {{$row->status}})" class="btn btn-secondary" data-toggle="modal" data-target="#editIconModal">Edit</button></td>
                                    
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
  
  {{-- Create reson Modal --}}

    <div class="modal fade" id="createIconModal" tabindex="-1" role="dialog" aria-labelledby="createIconModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('reject.store') }}" method="POST" id="rejectReson">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editIconModalLabel">Create Reson</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group my-2">
                                <label for="Name">Reson Type</label>
                                <select name="type" class="form-control" id="">
                                    <option value="">Select Type</option>
                                    <option value="0">Reject</option>
                                    <option value="1">Refund</option>
                                </select>
                            </div>
                            <div class="form-group my-2">
                                <label for="Name">Reson</label>
                                <input type="text" name="reson" class="form-control" autocomplete="off" placeholder="Reson">
                            </div>
                            <div class="form-group my-2">
                                <label for="SortOrder">Description</label>
                                <textarea type="text" name="description" class="form-control" autocomplete="off" placeholder="Sort Order"></textarea>
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


{{-- Edit reson Modal --}}

    <div class="modal fade" id="editIconModal" tabindex="-1" role="dialog" aria-labelledby="editIconModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('reject.update') }}" method="POST" id="rejecResonEdit">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editIconModalLabel">Edit Reson</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="form-group my-2">
                                <label for="Name">Reson Type</label>
                                <select name="type" class="form-control" id="resonTypeEdit">
                                    <option value="">Select Type</option>
                                    <option value="0">Reject</option>
                                    <option value="1">Refund</option>
                                </select>
                            </div>
                            <div class="form-group my-2">
                                <label for="Name">Reson</label>
                                <input type="text" name="reson" id="edit_reson" class="form-control" autocomplete="off" placeholder="Reson">
                            </div>
                            <div class="form-group my-2">
                                <label for="SortOrder">Description</label>
                                <input type="text" name="description" id="edit_description" class="form-control" autocomplete="off" placeholder="Description">
                            </div>
                            <div class="form-group my-2" id="status_edit">
                                
                            </div>

                            <input type="hidden" name="reject_id" id="editId">
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

        $('form[id="rejectReson"]').validate({
            rules : {
                type: {
                        required: true,
                },
                reson: {
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

        $('form[id="rejecResonEdit"]').validate({
            rules : {
                type: {
                        required: true,
                },
                reson: {
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


        resonEdit = (id, reson, type, description, status) => {

            let st = `<label for="Status">Active</label>
                    <input type="checkbox" ${status == 1 ? 'checked' : '' } name="status" value="checked" autocomplete="off">`

            let option =    `<option value="">Select Type</option>
                            <option ${type == 0 ? 'selected' : ''} value="0">Reject</option>
                            <option ${type == 1 ? 'selected' : ''} value="1">Refund</option>`;
            $('#editId').val(id);
            $('#edit_reson').val(reson);
            $('#edit_description').val(description);
            $('#resonTypeEdit').html(option);
            $('#status_edit').html(st);
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
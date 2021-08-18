@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Edit Custom Field</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('custom_field.index') }}">Custom Field</a></li>
                <li class="breadcrumb-item active">Edit Custom Field</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('custom_field.update', $field->id) }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Name</label>
                                        <input type="text" name="name" value="{{ $field->name }}" class="form-control @error('name') is-invalid @enderror" placeholder="Name" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('name')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Type">Type</label>
                                        <select name="type" id="select_type" class="form-control @error('type') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select Type</option>
                                            <option {{ $field->type == 'text' ? 'selected' : ''}} value="text">Text</option>
                                            <option {{ $field->type == 'textarea' ? 'selected' : ''}} value="textarea">Textarea</option>
                                            <option {{ $field->type == 'checkbox' ? 'selected' : ''}} value="checkbox">Checkbox</option>
                                            {{-- <option value="checkbox_multiple-1">Checkbox (Multiple)</option> --}}
                                            <option {{ $field->type == 'select' ? 'selected' : ''}} value="select-1">Select Box</option>
                                            <option {{ $field->type == 'radio' ? 'selected' : ''}} value="radio-1">Radio</option>
                                            <option {{ $field->type == 'file' ? 'selected' : ''}} value="file">File</option>
                                            <option {{ $field->type == 'url' ? 'selected' : ''}} value="url">URL</option>
                                            <option {{ $field->type == 'number' ? 'selected' : ''}} value="number">Number</option>
                                            <option {{ $field->type == 'date' ? 'selected' : ''}} value="date">Date</option>
                                            <option {{ $field->type == 'dependency' ? 'selected' : ''}} value="dependency">Dependency</option>
                                            {{-- <option value="date_time">Date Time</option>
                                            <option value="date_range">Date Range</option>
                                            <option value="video">Video (Youtube, Vimeo)</option> --}}
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('type')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group my-2 col-md-6">
                                            <label for="Status">Required</label>
                                            <input type="checkbox" {{ $field->required == 1 ? 'checked' : '' }} name="required" value="checked" autocomplete="off">
                                        </div>
                                        <div class="form-group my-2 col-md-6">
                                            <label for="Status">Active</label>
                                            <input type="checkbox" {{ $field->status == 1 ? 'checked' : '' }} name="status" value="checked" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="FieldLength">Field Length</label>
                                        <input type="text" name="field_length" value="{{ $field->max }}" class="form-control @error('field_length') is-invalid @enderror" placeholder="Field Length" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('field_length')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="DefaultValue">Default Value</label>
                                        <input type="text" name="default_value" value="{{ $field->default_value }}" class="form-control @error('default_value') is-invalid @enderror" placeholder="Default Value" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('default_value')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="Name">Description Area</label>
                                        <select name="description_area" id="" class="form-control @error('description_area') is-invalid @enderror" autocomplete="off">
                                            <option value="">Select</option>
                                            <option {{ $field->description_area_flag == 2 ? 'selected' : ''}} value="2">None</option>
                                            <option {{ $field->description_area_flag == 0 ? 'selected' : ''}} value="0">Top Section</option>
                                            <option {{ $field->description_area_flag == 1 ? 'selected' : ''}} value="1">Details Section</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            @error('description_area')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="dependency_area">
                                    
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary my-3">Update</button>
                        </form>
                        @if (count($field->Dependency) != 0)
                            <h5>Dependencies</h5>
                            <hr>
                            <div class="row">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Master</th>
                                            <th>Order</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($field->Dependency as $row)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $row->master }}</td>
                                                <td>{{ $row->order }}</td>
                                                <td><button type="button" onclick="deleteDependecy({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#dependencyDeleteModal"><i class="fas fa-trash"></i></button>
                                                    <form id="deleteDependecy_{{$row->id}}" action="{{ route('custom.dependency.delete', $row->id) }}" method="POST">@csrf</form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    <script>
        $('#select_type').on('change', function(){
            let value = $(this).val();
            let option = '<option value="">Select</option>';
            
            if(value == 'dependency'){
                $.ajax({
                    url : '/dependency/get',
                    type : 'get',
                    success:function(data){

                        let area = `<div class="col-md-6">
                                        <div class="form-group my-2">
                                            <label for="Dependency">Dependency</label>
                                            <select name="dependency_select" onchange="dependencySelect()" class="form-control" id="dependency_select">
                                            </select>
                                        </div>
                                        <div class="form-group my-2 mx-2" id="more_dependent">
                                        </div>
                                    </div>`;

                        for(let i = 0; i < data.length; i++){
                            option += `<option value="${data[i].master_name}">${data[i].master_name}</option>`;
                        }

                        $('#dependency_area').html(area);
                        $('#dependency_select').html(option);
                    }
                });
            }
        });

        dependencySelect = () => {
            
            let value = $('#dependency_select').val();
            let checkbox = `<label>More dependent </label>
                        <input type="checkbox" onclick="moreDependent('${value}')" name="${value}dependet">
                        <div class="form-group my-2" id="more_dependent${value}">
                        </div>`;

            $('#more_dependent').html(checkbox);
        }

        moreDependent = (value) => {
            
            let check = $(`input[name="${value}dependet"]:checked`).length;
            let cols = `<div id="dependency_select${value}"></div>`;
            let radio = ``;
            
            if(check == 1){
                $.ajax({
                    url : '/dependency/get/dependent',
                    type : 'get',
                    data : {data:value},
                    success:function(data){
                        
                        for(let i = 0; i < data.length; i++){
                            radio += `<label>${data[i].master_name}</label>
                                        <input type="radio" name="${data[i].master_name}" onclick="dependencySelect1('${data[i].master_name}', '${value}')" value="${data[i].master_name}">`;
                        }
                        
                        if(data.length == 0 ){
                            radio += 'No more dependent!'
                        }
                        $(`#more_dependent${value}`).html(cols);

                        $(`#dependency_select${value}`).html(radio);
                    }
                });
            }
            else{
                $(`#dependency_select${value}`).remove();
            }
        }

        dependencySelect1 = (value1, value) => {
            
            let checkLength = $(`#more_dependent_field${value}`).length;

            if(checkLength == 0 ){
                let checkbox = `<div class="form-group" id="more_dependent_field${value}">
                            <label>More dependent </label>
                            <input type="checkbox" onclick="moreDependent('${value1}')" name="${value1}dependet">
                                    <div class="form-group my-2" id="more_dependent${value1}">
                                    </div>
                                </div>`;
                $(`#dependency_select${value}`).append(checkbox);
            }
        }

    </script>

<div class="modal fade" id="dependencyDeleteModal" tabindex="-1" role="dialog" aria-labelledby="dependencyDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="dependencyDeleteModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" onclick="dependencyDelete()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

    <script>
        let ids = '';

        deleteDependecy = id => {
            ids = id;
        }

        dependencyDelete = () => {
            $('#deleteDependecy_'+ids).submit();
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
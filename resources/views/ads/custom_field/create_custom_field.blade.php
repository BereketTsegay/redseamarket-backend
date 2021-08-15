@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Create Custom Field</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('custom_field.index') }}">Custom Field</a></li>
                <li class="breadcrumb-item active">Create Custom Field</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <form action="{{ route('custom_field.store') }}" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    @csrf
                                    <div class="form-group my-2">
                                        <label for="Name">Name</label>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Name" autocomplete="off">
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
                                            <option {{ old('type') == 'text' ? 'selected' : '' }} value="text">Text</option>
                                            <option {{ old('type') == 'textarea' ? 'selected' : '' }} value="textarea">Textarea</option>
                                            <option {{ old('type') == 'checkbox' ? 'selected' : '' }} value="checkbox">Checkbox</option>
                                            {{-- <option value="checkbox_multiple-1">Checkbox (Multiple)</option> --}}
                                            <option {{ old('type') == 'select-1' ? 'selected' : '' }} value="select-1">Select Box</option>
                                            <option {{ old('type') == 'radio-1' ? 'selected' : '' }} value="radio-1">Radio</option>
                                            <option {{ old('type') == 'file' ? 'selected' : '' }} value="file">File</option>
                                            <option {{ old('type') == 'url' ? 'selected' : '' }} value="url">URL</option>
                                            <option {{ old('type') == 'number' ? 'selected' : '' }} value="number">Number</option>
                                            <option {{ old('type') == 'date' ? 'selected' : '' }} value="date">Date</option>
                                            <option {{ old('type') == 'dependency' ? 'selected' : '' }} value="dependency-2">Dependency</option>
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
                                            <input type="checkbox" name="required" value="checked" autocomplete="off">
                                        </div>
                                        <div class="form-group my-2 col-md-6">
                                            <label for="Status">Active</label>
                                            <input type="checkbox" checked name="status" value="checked" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group my-2">
                                        <label for="FieldLength">Field Length</label>
                                        <input type="text" name="field_length" value="{{ old('field_length') }}" class="form-control @error('field_length') is-invalid @enderror" placeholder="Field Length" autocomplete="off">
                                        <div class="invalid-feedback">
                                            @error('field_length')
                                                {{ $message }}
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group my-2">
                                        <label for="DefaultValue">Default Value</label>
                                        <input type="text" name="default_value" value="{{ old('default_value') }}" class="form-control @error('default_value') is-invalid @enderror" placeholder="Default Value" autocomplete="off">
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
                                            <option {{ old('description_area') == '2' ? 'selected' : '' }} value="2">None</option>
                                            <option {{ old('description_area') == '0' ? 'selected' : '' }} value="0">Top Section</option>
                                            <option {{ old('description_area') == '1' ? 'selected' : '' }} value="1">Details Section</option>
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
                                {{-- <hr>
                                <p>
                                    <a class="btn btn-secondary" data-toggle="collapse" href="#dependentArea" role="button" aria-expanded="false" aria-controls="dependentArea">
                                        Dependent
                                    </a>
                                </p>
                                <div class="collapse" id="dependentArea">
                                    <div class="container">
                                        <div class="col-md-6">
                                            <div class="form-group my-2">
                                                <label for="DependentName">Dependent Name</label>
                                                <input type="text" name="dependent_name" class="form-control" placeholder="Dependent Name" autocomplete="off">
                                            </div>
                                            <div class="form-group">
                                                <label for="Dependent">Dependent</label>
                                                <select name="dependent" id="Dependent" class="form-control" autocomplete="off">
                                                    <option value="">Select</option>
                                                    <option value="make">Make</option>
                                                    <option value="model">Model</option>
                                                    <option value="varient">Varient</option>
                                                </select>
                                            </div>
                                            <div class="form-group my-2" id="dependent_radio">
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                            <button type="submit" class="btn btn-primary my-3">Create</button>
                        </form>
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
            
            if(value == 'dependency-2'){
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
@endpush
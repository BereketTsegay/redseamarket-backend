@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h2 class="mt-4">Custom Field Details</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('custom_field.index') }}">Custom Field</a></li>
                <li class="breadcrumb-item active">Custom Field Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $field->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Type :</p>
                                    <p class="col-md-6">{{ $field->type }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Max Field Length :</p>
                                    <p class="col-md-6">{{ $field->max }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Default Value :</p>
                                    <p class="col-md-6">{{ $field->default_value == null ? 'Null' : $field->default_value }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Description Area :</p>
                                    @if($field->description_area_flag == 1)
                                    <p class="col-md-6">Detail Section</p>
                                    @elseif($field->description_area_flag == 0)
                                    <p class="col-md-6">Top Section</p>
                                    @else
                                    <p class="col-md-6">None</p>
                                    @endif
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $field->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Required :</p>
                                    <p class="col-md-6">{{ $field->required == 1 ? 'Required' : 'Not required' }}</p>
                                </div>
                            </div>
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
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($field->Dependency as $row)
                                                <tr>
                                                    <th scope="row">{{ $loop->iteration }}</th>
                                                    <td>{{ $row->master }}</td>
                                                    <td>{{ $row->order }}</td>
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
        </div>
    </main> 
@endsection

@push('script')
    

@endpush
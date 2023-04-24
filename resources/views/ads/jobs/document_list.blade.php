@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            
            <h2 class="mt-4">Job Request Documents</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Job Request Documents</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Job Request Documents
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Job title</th>
                                <th>Document</th>
                            </tr>
                        </thead>
                        <tbody>
                           @foreach($datas as $data)
                           <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$data->ad->title}}</td>
                            <td>    
                                <a href="{{ asset($data->document) }}" target="blank" class="col-md-4">View</a>
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





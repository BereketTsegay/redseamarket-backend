@extends('layout')

@section('content')
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Users</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Users
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Email</th>
                                <th>Verified</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Email</th>
                                <th>Verified</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            <tr>
                                <td>Tiger Nixon</td>
                                <td>System Architect</td>
                                <td>Edinburgh</td>
                                <td>61</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Garrett Winters</td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>63</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Ashton Cox</td>
                                <td>Junior Technical Author</td>
                                <td>San Francisco</td>
                                <td>66</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Cedric Kelly</td>
                                <td>Senior Javascript Developer</td>
                                <td>Edinburgh</td>
                                <td>22</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Airi Satou</td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>33</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Brielle Williamson</td>
                                <td>Integration Specialist</td>
                                <td>New York</td>
                                <td>61</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Herrod Chandler</td>
                                <td>Sales Assistant</td>
                                <td>San Francisco</td>
                                <td>59</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Rhona Davidson</td>
                                <td>Integration Specialist</td>
                                <td>Tokyo</td>
                                <td>55</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Airi Satou</td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>33</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Brielle Williamson</td>
                                <td>Integration Specialist</td>
                                <td>New York</td>
                                <td>61</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Herrod Chandler</td>
                                <td>Sales Assistant</td>
                                <td>San Francisco</td>
                                <td>59</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Rhona Davidson</td>
                                <td>Integration Specialist</td>
                                <td>Tokyo</td>
                                <td>55</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Airi Satou</td>
                                <td>Accountant</td>
                                <td>Tokyo</td>
                                <td>33</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Brielle Williamson</td>
                                <td>Integration Specialist</td>
                                <td>New York</td>
                                <td>61</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Herrod Chandler</td>
                                <td>Sales Assistant</td>
                                <td>San Francisco</td>
                                <td>59</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                            <tr>
                                <td>Rhona Davidson</td>
                                <td>Integration Specialist</td>
                                <td>Tokyo</td>
                                <td>55</td>
                                <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="#"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" class="btn btn-danger" data-toggle="modal" data-target="#exampleModal">Delete</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <button type="button" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
  </div>
@endpush
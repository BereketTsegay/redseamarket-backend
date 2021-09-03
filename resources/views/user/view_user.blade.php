@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">User Details</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></li>
                <li class="breadcrumb-item active">User Details</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <p class="col-md-6">Name :</p>
                                    <p class="col-md-6">{{ $user->name }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Email :</p>
                                    <p class="col-md-6">{{ $user->email }}</p>
                                </div>
                                <div class="row">
                                    <p class="col-md-6">Status :</p>
                                    <p class="col-md-6">{!! $user->status == 1 ? '<span class="text-success">Active</span>' : '<span class="text-secondary">Disabled</span>' !!}</p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-4 col-md-4">
                                <div class="card bg-danger text-white mb-4">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <p>{{ $inactiveAd }}</p>
                                                Unactivated Ads
                                            </div>
                                            <div class="col-md-3">
                                                <i class="fa fa-ban display-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="{{ route('user.ads', ['type' => 'inactive', 'id' => $user->id]) }}">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <p>{{ $activeAd }}</p>
                                                Activated Ads
                                            </div>
                                            <div class="col-md-3">
                                                <i class="fa fa-check-circle display-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="{{ route('user.ads', ['type' => 'active', 'id' => $user->id]) }}">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-4">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <p>{{ $favourite }}</p>
                                                Favourite Ads
                                            </div>
                                            <div class="col-md-3">
                                                <i class="fa fa-heart display-6"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="{{ route('user.ads', ['type' => 'favourite', 'id' => $user->id]) }}">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
    
    <script>
        let ids = '';

        customFieldDelete = (id) => {
            ids = id;
        }

        deleteCustomField = () => {
            $('#custom_field_delete_form'+ids).submit();
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
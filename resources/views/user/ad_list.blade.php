@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <h1 class="mt-4">User {{ $type }} ads</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></li>
                <li class="breadcrumb-item"><a href="{{ route('user.view', $id) }}">User Details</a></li>
                <li class="breadcrumb-item active">User {{ $type }} ads</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Ads
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Title</th>
                                <th>User</th>
                                <th>Active</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i= ($ad->currentpage() - 1 ) * $ad->perpage() + 1;
                            @endphp
                            @foreach ($ad as $row)
                            <tr>
                                <th scope="row">{{ $i }}</th>
                                <td>{{ date('d-m-Y', strtotime($row->created_at)) }}</td>
                                <td>{{ $row->Category->name }}</td>
                                <td>{{ $row->title }}</td>
                                @if ($row->customer_id == 0)
                                <td>Admin</td>
                                @else
                                <td>{{ $row->User->name }}</td>
                                @endif
                                <td>
                                    @if($row->status == \App\Common\Status::ACTIVE)
                                    <span class="text-success">Active</span>
                                    @else
                                    <span class="text-secondary">Disabled</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('ads.view', $row->id) }}"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="{{ route('ads.edit', $row->id) }}"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button onclick="adDelete({{$row->id}})" type="button" class="btn btn-danger" data-toggle="modal" data-target="#adDeleteModal">Delete</button>
                                <form id="ad_delete_form{{$row->id}}" action="{{ route('ads.delete', $row->id) }}" method="POST">@csrf</form>
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                        {{ $ad->links('pagination::bootstrap-4') }}
                    </table>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    
<div class="modal fade" id="adDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <button type="button" onclick="deleteAds()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>

    let ids = '';
    adDelete = (id) => {
        ids = id;
    }

    deleteAds = () => {

        $('#ad_delete_form'+ids).submit();
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
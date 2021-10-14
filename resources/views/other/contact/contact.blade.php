@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <h2 class="mt-4">Contact Us Enquiry</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Contact Us Enquiry</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Contact Us Enquiry
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contact as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->phone }}</td>
                                    @if ($row->status == 0)
                                        <td>Not Readed</td>
                                    @else
                                        <td>Readed</td>
                                    @endif
                                    
                                    <td>
                                        <a href="{{ route('contact.view', $row->id) }}"><button class="btn btn-primary">View</button></a>
                                    </td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                        {{ $contact->links('pagination::bootstrap-4') }}
                    </table>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
  
@if (Session::has('success'))
<script>
    Swal.fire({
        icon: 'success',
        text: '{!! Session::get('success') !!}',
    })
</script>
@endif

@if (Session::has('error'))
<script>
    Swal.fire({
        icon: 'error',
        text: '{!! Session::get('error') !!}',
    })
</script>
@endif
@endpush
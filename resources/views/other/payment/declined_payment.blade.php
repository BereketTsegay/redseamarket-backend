@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <h2 class="mt-4">Declined Payment</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Declined Payment</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Declined Payment
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User Name</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Lorem, ipsum dolor.</td>
                                    <td>Lorem ipsum dolor sit.</td>
                                    <td>1500</td>
                                    <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Lorem, ipsum dolor.</td>
                                    <td>Lorem ipsum dolor sit.</td>
                                    <td>1500</td>
                                    <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>Lorem, ipsum dolor.</td>
                                    <td>Lorem ipsum dolor sit.</td>
                                    <td>1500</td>
                                    <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>Lorem, ipsum dolor.</td>
                                    <td>Lorem ipsum dolor sit.</td>
                                    <td>1500</td>
                                    <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">5</th>
                                    <td>Lorem, ipsum dolor.</td>
                                    <td>Lorem ipsum dolor sit.</td>
                                    <td>1500</td>
                                    <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">6</th>
                                    <td>Lorem, ipsum dolor.</td>
                                    <td>Lorem ipsum dolor sit.</td>
                                    <td>1500</td>
                                    <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                </tr>
                                <tr>
                                    <th scope="row">7</th>
                                    <td>Lorem, ipsum dolor.</td>
                                    <td>Lorem ipsum dolor sit.</td>
                                    <td>1500</td>
                                    <td><a href="#"><button class="btn btn-primary">View</button></a></td>
                                </tr>
                        </tbody>
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

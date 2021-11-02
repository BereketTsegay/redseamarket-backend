@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <h2 class="mt-4">Payment</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Payment</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Payment
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Payment Id</th>
                                <th>User Name</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment Type</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $i= ($payment->currentpage() - 1 ) * $payment->perpage() + 1;
                            @endphp

                            @foreach ($payment as $row)
                                <tr>
                                    <th scope="row">{{ $i }}</th>
                                    <td>{{ $row->payment_id }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ date('d-M-Y h:i A', strtotime($row->created_at)) }}</td>
                                    <td>{{ $row->amount }}</td>
                                    <td>{{ $row->status }}</td>
                                    @if ($row->payment_type == 1)
                                        <td>Account Payment</td>
                                    @else
                                        <td>Online Payment</td>
                                    @endif
                                    <td><a href="{{ route('payment.view', $row->id) }}"><button class="btn btn-primary">View</button></a></td>
                                </tr>

                                @php
                                    $i++;
                                @endphp
                            @endforeach
                        </tbody>
                        {{ $payment->links('pagination::bootstrap-4') }}
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
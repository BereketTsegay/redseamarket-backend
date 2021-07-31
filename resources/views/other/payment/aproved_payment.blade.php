@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <h2 class="mt-4">Aproved Payment</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Aproved Payment</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Aproved Payment
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
    
<div class="modal fade" id="deleteBannerModal" tabindex="-1" role="dialog" aria-labelledby="deleteBannerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="deleteBannerModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deteteBanner()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createTestimonialModal" tabindex="-1" role="dialog" aria-labelledby="createTestimonialModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('testimonial.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title" id="createTestimonialModalModalLabel">Create Testimonial</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Name">Name</label>
                            <input type="text" name="name" class="form-control" id="Name" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="Designation">Designation</label>
                            <input type="text" class="form-control" name="designation" id="Designation" placeholder="Designation">
                        </div>
                        <div class="form-group my-2">
                            <label for="Description">Description</label>
                            <textarea name="description" class="form-control" id="Description" cols="30" rows="3" placeholder="Description"></textarea>
                        </div>
                        <div class="form-group my-2">
                            <label for="Image">Image</label>
                            <input type="file" name="image" class="form-control" id="Image">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

  <script>
        
        let ids = '';

        bannerDelete = id => {
            ids = id
        }

        deteteBanner = () => {
            
            $('#delete_Banner_form'+ids).submit();
        }
  </script>

@if (Session::has('success'))
<script>
    Swal.fire({
        icon: 'success',
        text: {{ Session::get('success') }},
    })
</script>
@endif

@if (Session::has('error'))
<script>
    Swal.fire({
        icon: 'error',
        text: {{ Session::get('error') }},
    })
</script>
@endif
@endpush
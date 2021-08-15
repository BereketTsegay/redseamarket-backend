@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="#" data-toggle="modal" data-target="#createTestimonialModal"><button type="button" class="btn btn-primary float-end">Create Testimonial</button></a>
            
            <h2 class="mt-4">Testimonial</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Testimonial</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Testimonial
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($testimonial as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->designation }}</td>
                                    <td><a href="{{ route('testimonial.view', $row->id) }}"><button class="btn btn-primary">View</button></a></td>
                                    <td><a href="{{ route('testimonial.edit', $row->id) }}"><button class="btn btn-secondary">Edit</button></a></td>
                                    <td><button type="button" onclick="categoryDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteCategoryModal">Delete</button>
                                    <form id="delete_banner_form{{$row->id}}" action="#" method="POST">
                                        @csrf
                                    </form>
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
                            <input type="text" value="{{ old('name') }}" name="name" class="form-control" id="Name" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label for="Designation">Designation</label>
                            <input type="text" value="{{ old('designation') }}" class="form-control" name="designation" id="Designation" placeholder="Designation">
                        </div>
                        <div class="form-group my-2">
                            <label for="Description">Description</label>
                            <textarea name="description" class="form-control" id="Description" cols="30" rows="3" placeholder="Description">{{ old('description') }}</textarea>
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
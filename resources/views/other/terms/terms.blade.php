@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="#" data-toggle="modal" data-target="#createBannerModal"><button type="button" class="btn btn-primary float-end">Create Terms & Conditions</button></a>
            
            <h2 class="mt-4">Terms & Condition</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Privacy & Policy</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Terms & Conditions
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Terms & Conditions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($terms as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->title }}</td>
                                    <td>{{ $row->terms }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu text-center">
                                                <button class="btn btn-secondary" onclick="editBanner({{$row->id}}, `{{$row->title}}`, `{{$row->terms}}`)" data-toggle="modal" data-target="#editBannerModal">Edit</button>
                                                <button type="button" onclick="bannerDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteBannerModal">Delete</button>
                                                <form id="delete_Banner_form{{$row->id}}" action="{{ route('terms.delete', $row->id) }}" method="POST">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
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

<div class="modal fade" id="createBannerModal" tabindex="-1" role="dialog" aria-labelledby="createBannerModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('terms.store') }}" method="POST" enctype="multipart/form-data" id="privacyStore">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title" id="createBannerModalModalLabel">Create Terms & Condition</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Title">Title</label>
                            <input type="text" name="title" class="form-control" id="Title" placeholder="Title">
                        </div>
                        <div class="form-group my-2">
                            <label for="Privacy & Policy">Terms & Condition</label>
                            <textarea type="text" name="terms" class="form-control" id="Privacy_&_Policy" placeholder="Terms & Condition"></textarea>
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

<div class="modal fade" id="editBannerModal" tabindex="-1" role="dialog" aria-labelledby="editBannerModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('terms.update') }}" method="POST" enctype="multipart/form-data" id="editPrivacyForm">
                @csrf
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                <h5 class="modal-title" id="createBannerModalModalLabel">Create Terms & Condition</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Title">Title</label>
                            <input type="text" name="title" class="form-control" id="editTitle" placeholder="Title">
                        </div>
                        <div class="form-group my-2">
                            <label for="Privacy & Policy">Terms & Condition</label>
                            <textarea type="text" name="terms" class="form-control" id="privacy_policy" placeholder="Terms & Condition"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

  <script>

        $('form[id="privacyStore"]').validate({
            rules : {
                title: {
                        required : true,
                    },
                privacy: {
                        required: true,
                    },
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        $('form[id="editPrivacyForm"]').validate({
            rules : {
                title: {
                        required : true,
                    },
                privacy: {
                        required: true,
                    },
            },
            submitHandler: function(form) {
                form.submit();
            }
            
        });

        editBanner = (id, title, policy) => {
            console.log(id);
            $('#editId').val(id);
            $('#editTitle').val(title);
            $('#privacy_policy').val(policy);
        }
        
        let ids = '';

        bannerDelete = id => {
            ids = id;
        }

        deteteBanner = () => {
            
            $('#delete_Banner_form'+ids).submit();
        }

  </script>

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
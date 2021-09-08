@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="#" data-toggle="modal" data-target="#createSocialModal"><button type="button" class="btn btn-primary float-end">Create Social Link</button></a>
            
            <h2 class="mt-4">Social Link</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Social Link</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Social Link
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Url</th>
                                <th>Image / Icon</th>
                                <th>Active</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($social as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->url }}</td>
                                    <td>
                                        @if ($row->image)
                                            <a href="{{ asset($row->image) }}" target="blank"><img src="{{ asset($row->image) }}" width="80px" alt=""></a>
                                        @else
                                            <i class="{{ $row->Icon->name }}"></i>
                                        @endif
                                    </td>
                                    @if($row->status == 1)
                                    <td class="text-success">Active</td>
                                    @else
                                    <td class="text-secondary">Disabled</td>
                                    @endif
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu text-center">
                                                <a href="{{ route('social.edit', $row->id) }}"><button class="btn btn-secondary">Edit</button></a>
                                                <button type="button" onclick="socialDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteSocialModal">Delete</button>
                                                <form id="delete_social_form{{$row->id}}" action="{{ route('social.delete', $row->id) }}" method="POST">
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
    
<div class="modal fade" id="deleteSocialModal" tabindex="-1" role="dialog" aria-labelledby="deleteSocialModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
          {{-- <h5 class="modal-title" id="deleteSocialModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span> --}}
          {{-- </button> --}}
            </div>
            <div class="modal-body">
                Are you sure, do you want to delete?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deteteSocial()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createSocialModal" tabindex="-1" role="dialog" aria-labelledby="createSocialModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('social.store') }}" method="POST" enctype="multipart/form-data" id="createSocial">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createSocialModalModalLabel">Create Social Link</h5>
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
                            <label for="Url">Url</label>
                            <input type="text" class="form-control" name="url" id="Url" placeholder="Url">
                        </div>
                        <div class="form-group my-2">
                            <label for="Icon">Icon (optional, select icon or image)</label>
                            <Select name="icon" class="form-control">
                                <option value="">Select</option>
                                @foreach ($icon as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </Select>
                        </div>
                        <div class="form-group my-2">
                            <label for="Image">Image</label>
                            <input type="file" name="image" class="form-control" id="Image">
                        </div>
                        <div class="form-group my-2">
                            <label for="Status">Status</label>
                            <input type="checkbox" checked name="status" id="">
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

        socialDelete = id => {
            ids = id
        }

        deteteSocial = () => {
            
            $('#delete_social_form'+ids).submit();
        }

        $('form[id="createSocial"]').validate({
            rules : {
                name: {
                        required : true,
                    },
                url: {
                        required: true,
                    },
            },
            submitHandler: function(form) {
                form.submit();
            }
            
        });

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
@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="#" data-toggle="modal" data-target="#createBannerModal"><button type="button" class="btn btn-primary float-end">Create Banner</button></a>
            
            <h2 class="mt-4">Banner</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Banner</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Banner
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Active</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banner as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->name }}</td>
                                    @if($row->status == 1)
                                    <td class="text-success">Active</td>
                                    @else
                                    <td class="text-secondary">Disabled</td>
                                    @endif
                                    <td><a href="{{ route('banner.view', $row->id) }}"><button class="btn btn-primary">View</button></a></td>
                                    <td><button class="btn btn-secondary" onclick="editBanner({{$row->id}}, '{{$row->name}}', '{{$row->type}}', {{$row->status}})" data-toggle="modal" data-target="#editBannerModal">Edit</button></td>
                                    <td><button type="button" onclick="bannerDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteBannerModal">Delete</button>
                                    <form id="delete_Banner_form{{$row->id}}" action="{{ route('banner.delete', $row->id) }}" method="POST">
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

<div class="modal fade" id="createBannerModal" tabindex="-1" role="dialog" aria-labelledby="createBannerModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('banner.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title" id="createBannerModalModalLabel">Create Banner</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Name">Name</label>
                            <input type="text" name="name" class="form-control" id="Name" placeholder="Name">
                        </div>
                        <div class="form-group my-2">
                            <label for="Type">Postion</label>
                            <Select name="position" class="form-control">
                                <option value="">Select</option>
                                <option value="Top">Top</option>
                                <option value="Middle">Middle</option>
                                <option value="Bottom">Bottom</option>
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

<div class="modal fade" id="editBannerModal" tabindex="-1" role="dialog" aria-labelledby="editBannerModalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('banner.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                <h5 class="modal-title" id="createBannerModalModalLabel">Create Banner</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="EditName">Name</label>
                            <input type="text" name="name" class="form-control" id="editName" placeholder="Name">
                        </div>
                        <div class="form-group my-2">
                            <label for="Type">Postion</label>
                            <Select name="position" id="editPosition" class="form-control">
                                <option value="">Select</option>
                                <option value="Top">Top</option>
                                <option value="Middle">Middle</option>
                                <option value="Bottom">Bottom</option>
                            </Select>
                        </div>
                        <div class="form-group my-2">
                            <label for="Image">Image</label>
                            <input type="file" name="image" class="form-control" id="Image">
                        </div>
                        <div class="form-group my-2" id="editStatus">
                            <label for="Status">Status</label>
                            <input type="checkbox" checked name="status">
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
        
        let ids = '';

        bannerDelete = id => {
            ids = id;
        }

        deteteBanner = () => {
            console.log(ids);
            $('#delete_Banner_form'+ids).submit();
        }

        editBanner = (id, name, position, status) => {
            let option = '';
            let editStatus = '';
            $('#editId').val(id);
            $('#editName').val(name);
            
            if(status == 1){
                editStatus = `<label for="Status">Status</label>
                            <input type="checkbox" checked name="status">`;
            }
            else{
                editStatus = `<label for="Status">Status</label>
                            <input type="checkbox" name="status">`;
            }

            if(position == 'Top'){
                option = `<option selected value="Top">Top</option>
                <option value="Middle">Middle</option>
                <option value="Bottom">Bottom</option>`;
            }
            else if(position == 'Middle'){
                option = `<option value="Top">Top</option>
                <option selected value="Middle">Middle</option>
                <option value="Bottom">Bottom</option>`;
            }
            else{
                option = `<option value="Top">Top</option>
                <option value="Middle">Middle</option>
                <option selected value="Bottom">Bottom</option>`;
            }

            $('#editPosition').html(option);
            $('#editStatus').html(editStatus);
        }

  </script>
@endpush
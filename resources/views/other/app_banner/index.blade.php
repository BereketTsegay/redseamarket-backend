@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="#" data-toggle="modal" data-target="#createBannerModal"><button type="button" class="btn btn-primary float-end">Create Banner</button></a>
            
            <h2 class="mt-4">App Banner</h2>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">App Banner</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                   App Banner
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($banner as $row)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $row->title }}</td>
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
                                                <a href="{{ route('appbanner.view', $row->id) }}"><button class="btn btn-primary my-2">View</button></a>
                                                <button class="btn btn-secondary my-2" onclick="editBanner({{$row->id}}, '{{$row->title}}','{{$row->status}}','{{$row->country_id}}')" data-toggle="modal" data-target="#editBannerModal">Edit</button>
                                                {{-- <button type="button" onclick="bannerDelete({{$row->id}})" class="btn btn-danger" data-toggle="modal" data-target="#deleteBannerModal">Delete</button>
                                                <form id="delete_Banner_form{{$row->id}}" action="{{ route('banner.delete', $row->id) }}" method="POST">
                                                    @csrf
                                                </form> --}}
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
                Do you want to delete?
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
            <form action="{{ route('appbanner.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                <h5 class="modal-title" id="createBannerModalModalLabel">Create Banner</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="form-group my-2">
                            <label for="Name">Title</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="title" value="{{old('title')}}">
                        </div>
                        
                        <div class="form-group my-2">
                            <label for="Image">Image</label>
                            <input type="file" name="image" class="form-control" id="Image" value="{{old('image')}}">
                            {{-- <div class="text-danger">Image Width: 1920px, Height: 506px </div> --}}
                            @if($errors->has('image'))
                            <div class="error">{{ $errors->first('image') }}</div>
                        @endif
                        </div>
                        <div class="form-group my-2">
                            <label for="Type">Country</label>
                            <Select name="country" class="form-control">
                                <option value="">Select Country</option>
                                @foreach ($countries as $row1)
                                    <option value="{{ $row1->id }}">{{ $row1->name }}</option>
                                @endforeach
                            </Select>
                            @if($errors->has('country'))
                                <div class="error">{{ $errors->first('country') }}</div>
                            @endif
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
            <form action="{{ route('appbanner.update') }}" method="POST" enctype="multipart/form-data">
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
                            <label for="EditName">Title</label>
                            <input type="text" value="{{ old('title') }}" name="title" class="form-control" id="editName" placeholder="title">
                        </div>
                       
                        <div class="form-group my-2">
                            <label for="Image">Image</label>
                            <input type="file" name="image" class="form-control" id="Image">
                        </div>
                        <div class="form-group my-2 selitem">
                            <label for="Type">Country</label>
                            <Select name="country" id="editPosition" class="form-control">
                                <option value="">Select Country</option>
                                
                            </Select>
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

        editBanner = (id, name, status) => {
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

            let country = [];
            $.ajax({
                url: '/api/customer/get/country',
                method: 'post',
                success:function(data){
                    
                    if(data.status == 'success'){
                        country = data.country;
                    }
                    
                    for(let i = 0; i < country.length; i++){
                        
                        if(country[i].id == id){
                            option += `<option selected value="${country[i].id}">${country[i].name}</option>`;
                        }
                        else{
                            option += `<option value="${country[i].id}">${country[i].name}</option>`;
                        }
                    }
                    $('#editPosition').html(option);

                    $(".selitem").find('#editPosition').val(countryid)


                }
            })
            
                     
            $('#editStatus').html(editStatus);
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

@if ($errors->any())
<script>
    Swal.fire({
        icon: 'error',
        html: '<ul>'+
                @foreach ($errors->all() as $error)
                    `<li>{{ $error }}</li>`
                @endforeach
            +'</ul>',
    });
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
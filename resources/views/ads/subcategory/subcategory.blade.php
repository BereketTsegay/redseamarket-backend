@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">
            
            <a href="{{ route('subcategory.create') }}"><button type="button" class="btn btn-primary float-end">Create Subcategory</button></a>
            
            <h1 class="mt-4">Subcategories</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Subcategory</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Subcategory
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <div class="form-group my-3">
                            <select name="category" id="category" class="form-control w-50 ">
                                <option value="">Select Category</option>
                                @foreach ($category as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                        <tbody id="subcategory_list">
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main> 
@endsection

@push('script')
    
<div class="modal fade" id="subcategoryDeleteModal" tabindex="-1" role="dialog" aria-labelledby="subCategoryDeleteModalLabel" aria-hidden="true">
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
                <button type="button" onclick="deteteSubcategory()" class="btn btn-primary">Delete</button>
            </div>
        </div>
    </div>
</div>



<script>

    let ids = '';

    subcategoryDelete = id => {
        ids = id
    }

    deteteSubcategory = () => {
        
        $('#delete_subcategory_form'+ids).submit();
    }

    $('#category').on('change', function(){
        let id = $(this).val();
        let tr = '';
        
        $.ajax({
            url : '/change/subcategory',
            type : 'get',
            data : {id:id},
            success:function(data){
                if(data.length == 0){
                    tr += 'No data found';
                }

                for(var i = 0; i < data.length; i++){

                    tr +=   `<tr>
                                <th scope="row">${i+1}</th>
                                <td>${data[i].name}</td>
                                ${data[i].status == 1 ? '<td class="text-success">Active</td>' :'<td class="text-secondary">disabled</td>' }
                                <td><a href="/subcategory/view/${data[i].id}"><button class="btn btn-primary">View</button></a></td>
                                <td><a href="/subcategory/edit/${data[i].id}"><button class="btn btn-secondary">Edit</button></a></td>
                                <td><button type="button" onclick="subcategoryDelete(${data[i].id})" class="btn btn-danger" data-toggle="modal" data-target="#subcategoryDeleteModal">Delete</button>
                                <form id="delete_subcategory_form${data[i].id}" action="/subcategory/delete/${data[i].id}" method="POST">
                                    @csrf
                                </form>
                                </td>
                            </tr>`;
                }

                $('#subcategory_list').html(tr);
            }
        });
    });
</script>
@endpush
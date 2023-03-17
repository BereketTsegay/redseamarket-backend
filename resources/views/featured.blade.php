@extends('layout')

@section('content')

    <main>
        <div class="container-fluid px-4">

            <h1 class="mt-4">Featured</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Featured Settings</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container">
                        <div class="row">
                            
                            <div class="col-md-12">
                          
                                <form action="{{route('admin.featured.update')}}" id="form" method="POST">
                                    @csrf
                                <div class="row mt-4">
                                    <input type="hidden" name="id" value="{{$data->id}}">

                                                                            
                                        <div class="form-group col-md-4">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input featuredrd" id="radio1" name="featured" value="0" {{($data->featured==0)? 'checked': ''}} >None
                                                <label class="form-check-label" for="radio1"></label>
                                              </div>
                                             
                                        </div>
                                        <div class="form-group col-md-4">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input featuredrd" id="radio2" name="featured" value="1" {{($data->featured==1)? 'checked': ''}}>Featured
                                                <label class="form-check-label" for="radio2"></label>
                                              </div>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input featuredrd" id="radio3" name="featured" value="2" {{($data->featured==2)? 'checked': ''}}>Both
                                                <label class="form-check-label" for="radio3"></label>
                                              </div>
                                        </div>
                                        

                                </div>
                            </form>
                                   
                            </div>
                        </div>
                      
                        
                    </div>
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

<script>
    $( ".featuredrd" ).click(function() {
  $( "#form" ).submit();
});
</script>
@endpush
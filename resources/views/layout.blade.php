@php
    $pageName="Dashbord";
    $pageDescription="Main admin intry ponit";
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="{{$pageDescription}}" />
        <meta name="author" content="Redsea markets IT department" />
        <title>{{$pageName}}| Redsea Market</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
        @stack('style')
    </head>
    <body class="sb-nav-fixed">
        <input type="hidden" name="" id="csrf_toke" value="{{ csrf_token() }}">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="#">Red Sea Market</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <div class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                {{-- <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div> --}}
            </div>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle notification-icon" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-bell fa-fw"></i>
                        <span id="notification_counts">0</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" id="notifications">

                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}">Profile</a></li>
                        <li><a class="dropdown-item" href="#" onclick="changePassword()">Change Password</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        @php
            
            $adsCount = \App\Models\Ads::where('status', \App\Common\Status::REQUEST)
            ->where('created_at', \Carbon\Carbon::today())
            ->count();

        @endphp

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link {{ request()->is('*dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_AUTHORITY))

                                <a class="nav-link collapsed {{ request()->is('*role*') ? 'active' : '' }} {{ request()->is('*admin/user*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAuthority" aria-expanded="false" aria-controls="collapseUsers">
                                    <div class="sb-nav-link-icon"><i class="fas fa-university"></i></div>
                                    Authority
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse {{ request()->is('*role*') ? 'show' : '' }} {{ request()->is('*admin/user*') ? 'show' : '' }}" id="collapseAuthority" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link {{ request()->is('*role*') ? 'active' : '' }}" href="{{ route('role.index') }}">Roles</a>
                                    </nav>
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link {{ request()->is('*admin/user*') ? 'active' : '' }}" href="{{ route('admin_user.index') }}">Admin Users</a>
                                    </nav>
                                </div>

                            @endif
                            
                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_USER))
                                
                                <a class="nav-link {{ request()->is('*users*') ? 'active' : '' }}" href="{{ route('user.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                    Subscribed Users
                                </a>

                            @endif
                            
                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGEBANNERS))

                            <a class="nav-link {{ request()->is('*banner*') ? 'active' : '' }}" href="{{ route('banner.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-scroll"></i></div>
                                Banners
                            </a>
                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_TESTIMONIAL))

                                <a class="nav-link {{ request()->is('*testimonial*') ? 'active' : '' }}" href="{{ route('testimonial.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-comments"></i></div>
                                    Testimonials
                                </a>

                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_CATEGORY))

                                <a class="nav-link collapsed {{ request()->is('category*') ? 'active' : '' }} {{ request()->is('*custom_field*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCategory" aria-expanded="false" aria-controls="collapseAds">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
                                    Category
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse {{ request()->is('category*') ? 'show' : '' }} {{ request()->is('*custom_field*') ? 'show' : '' }}" id="collapseCategory" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link {{ request()->is('category*') ? 'active' : '' }}" href="{{ route('category.index') }}">Category</a>
                                        <a class="nav-link {{ request()->is('*custom_field*') ? 'active' : '' }}" href="{{ route('custom_field.index') }}">Custom Field</a>
                                    </nav>
                                </div>
                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_SUBCATEGORY))

                                <a class="nav-link {{ request()->is('subcategory*') ? 'active' : '' }}" href="{{ route('subcategory.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
                                    Subcategory
                                </a>

                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_ADS))

                                <a class="nav-link collapsed {{ request()->is('ad_list*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseAds" aria-expanded="false" aria-controls="collapseAds">
                                    <div class="sb-nav-link-icon"><i class="fas fa-ad"></i></div>
                                    Ads
                                    @if ($adsCount != 0)
                                        <div class="badge text-danger">{{$adsCount}}</div>
                                    @endif
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse {{ request()->is('ad*') ? 'show' : '' }}" id="collapseAds" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link {{ request()->is('ad_list*') ? 'active' : '' }}" href="{{ route('ads.index') }}">Ads</a>
                                        <a class="nav-link {{ request()->is('ad_request*') ? 'active' : '' }}" href="{{ route('ad_request.index') }}">Ad Request
                                            @if ($adsCount != 0)
                                                <div class="badge badge-primary">{{$adsCount}}</div>
                                            @endif
                                        </a>
                                    </nav>
                                </div>

                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_PAYMENT))

                                <a class="nav-link {{ request()->is('payment*') ? 'active' : '' }}" href="{{ route('payment.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-dollar-sign"></i></div>
                                    Payment
                                </a>

                            @endif

                            <div class="sb-sidenav-menu-heading">Others</div>

                            <a class="nav-link {{ request()->is('contact*') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-phone"></i></div>
                                Contact Us Enquiry
                            </a>

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_FEATURED_DEALER))

                                <a class="nav-link {{ request()->is('privacy*') ? 'active' : '' }}" href="{{ route('privacy.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                                    Privacy & Policy
                                </a>
                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::TERMS_CONDITIONS))

                                <a class="nav-link {{ request()->is('terms*') ? 'active' : '' }}" href="{{ route('terms.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-balance-scale-right"></i></div>
                                    Terms & Conditions
                                </a>
                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_FEATURED_DEALER))

                                <a class="nav-link {{ request()->is('featured*') ? 'active' : '' }}" href="{{ route('dealer.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-car"></i></div>
                                    Featured Dealer
                                </a>
                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_REJECT_REASON))
                                
                                <a class="nav-link {{ request()->is('reject*') ? 'active' : '' }}" href="{{ route('reject.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
                                    Resons
                                </a>
                            
                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_ICONS))
                                
                                <a class="nav-link {{ request()->is('icons*') ? 'active' : '' }}" href="{{ route('icon.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-icons"></i></div>
                                    Icons
                                </a>

                            @endif

                            @if (Auth::user()->type == \App\Common\Usertype::ADMIN || Auth::user()->UserRole->TaskRole->contains('task_id', \App\Common\Task::MANAGE_SOCIAL_LINK))
                                
                                <a class="nav-link {{ request()->is('social*') ? 'active' : '' }}" href="{{ route('social.index') }}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-thumbs-up"></i></div>
                                    Social Links
                                </a>

                            @endif
                            
                            <a class="nav-link collapsed {{ request()->is('*profile*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSettings" aria-expanded="false" aria-controls="collapseAds">
                                <div class="sb-nav-link-icon"><i class="fas fa-cogs"></i></div>
                                Settings
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse {{ request()->is('*profile*') ? 'show' : '' }}" id="collapseSettings" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link {{ request()->is('*profile*') ? 'active' : '' }}" href="{{ route('admin.profile') }}">Porfile</a>
                                    <a class="nav-link" href="#" onclick="changePassword()">Change Password</a>
                                    <a class="nav-link" href="{{ route('logout') }}">Logout</a>
                                </nav>
                            </div>
                            {{-- <a class="nav-link" href="tables.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                                Tables
                            </a> --}}
                        </div>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">

                @yield('content')

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Redsea Market <span id="currentYear"></span></div>
                        {{-- <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div> --}}
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    {{-- <script src="{{ asset('assets/demo/chart-area-demo.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/demo/chart-bar-demo.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>

    <script src="https://www.gstatic.com/firebasejs/8.6.2/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.6.2/firebase-messaging.js"></script>

    <script>
        let year = new Date().getFullYear();
        document.getElementById('currentYear').innerHTML = year;

        // Your web app's Firebase configuration
        let firebaseConfig1 = {
            apiKey: "AIzaSyBrhfC7ZrhEyH_xNGXcR6HQUUGUVBNlnWw",

            authDomain: "interview-6168a.firebaseapp.com",

            projectId: "interview-6168a",

            storageBucket: "interview-6168a.appspot.com",

            messagingSenderId: "1028679469723",

            appId: "1:1028679469723:web:026d079d23d7505744943e",

            measurementId: "G-Z5PT70YBQ2"


        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig1);

        const messaging1 = firebase.messaging();

        messaging1.onMessage(function(payload) {
            console.log('message get', payload);
            
            const noteTitle = payload.notification.title;
            const noteBody = payload.notification.body;

            const noteOptions = {

                body: payload.notification.body,
                icon: payload.notification.icon,

            };

            // let note = `<h3>${noteTitle}</h3>
            // <p>${noteBody}</p>`;

            // $('#notification_value').html(note);

            new Notification(noteTitle, noteOptions);

        });
            
    </script>

<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.change.password') }}" method="POST" id="changePasswordForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" onclick="dismissModal()" class="btn-close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group my-2">
                        <label for="CurrentPassword">Current Password</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Current Password" autocomplete="off">
                    </div>
                    <div class="form-group my-2">
                        <label for="newPassword">New Password</label>
                        <input type="password" name="password" class="form-control" placeholder="New Password" id="password" autocomplete="off">
                    </div>
                    <div class="form-group my-2">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="dismissModal()" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    changePassword = () => {
        $('#changePasswordModal').modal('show');
    }

    dismissModal = () => {
        $('#changePasswordModal').modal('hide');
    }

    $('form[id="changePasswordForm"]').validate({
            rules : {
                current_password: {
                        required : true,
                    },
                password: {
                        required: true,
                    },
                password_confirmation: {
                    required: true,
                    equalTo: "#password",
                    }
            },
            submitHandler: function(form) {
                form.submit();
            }
            
    });
</script>

<script>

    $(document).ready(function(){

        let notification = `<li class="dropdown-item">`;

        $.ajax({
            url: '/get/notification',
            method: 'get',
            success:function(data){
                $('#notification_counts').html(data.length);

                notification += `<span class="notification_counts">${data.length}</span> Notifications</li>
                        <li class="dropdown-item"><button onclick="readNotification();" class="btn btn-secondary">Clear All</button></li>
                        <li><hr class="dropdown-divider" /></li>`
                for(let i = 0; i < data.length; i++){

                    notification += `<li class="dropdown-item">${data[i].message}</li>`;
                }

                $('#notifications').html(notification);
            }
        });

        readNotification = () => {
            
            let _token = $('#csrf_toke').val();
            $.ajax({
                url: '/read/notification',
                data: {_token: _token},
                method: 'post',
                success:function(){

                    $('#notifications').html(`<li class="dropdown-item"><span class="notification_counts">0</span> Notifications</li>
                        <li class="dropdown-item"><button onclick="readNotification();" class="btn btn-secondary">Clear All</button></li>
                        <li><hr class="dropdown-divider"/></li>`);
                }
            });

        }

    });

    setInterval(function(){

        let notification = `<li class="dropdown-item">`;

        $.ajax({
            url: '/get/notification',
            method: 'get',
            success:function(data){
                $('#notification_counts').html(data.length);

                notification += `<span class="notification_counts">${data.length}</span> Notifications</li>
                        <li class="dropdown-item"><button onclick="readNotification();" class="btn btn-secondary">Clear All</button></li>
                        <li><hr class="dropdown-divider"/></li>`
                for(let i = 0; i < data.length; i++){

                    notification += `<li class="dropdown-item">${data[i].message}</li>`;
                }

                $('#notifications').html(notification);
            }
        });
    }, 20000);

</script>

@stack('script')    
</body>
</html>

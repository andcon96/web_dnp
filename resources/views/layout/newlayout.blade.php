<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        Danapaint
    </title>
    <link rel="icon" type="image/gif/jpg" href="images/SGI.jpg">
    <meta name="description">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @yield('metahead')

    <link rel="shortcut icon" href="{{asset('favicon.ico')}}">

    <link rel="stylesheet" href="{{url('vendors/bootstrap/dist/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{url('vendors/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" href="{{url('vendors/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{url('vendors/themify-icons/css/themify-icons.css')}}">
    <link rel="stylesheet" href="{{url('vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{url('vendors/selectFX/css/cs-skin-elastic.css')}}">
    <link rel="stylesheet" href="{{url('vendors/jqvmap/dist/jqvmap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css">
    <link rel="stylesheet" href="{{url('vendors/bootstrap-select/dist/css/bootstrap-select.min.css')}}"> <!--Searchable Select Dropdown-->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" href="{{url('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{url('assets/css/tablestyle.css')}}" >
    <link rel="stylesheet" href="{{url('assets/css/checkbox.css')}}" >
    <link rel="stylesheet" type="text/css" href="{{url('assets/css/so_mobile.css')}}">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

</head>

<body class="open">
    <!-- Side Navbar -->

    <aside id="left-panel" class="left-panel">
        <nav class="navbar navbar-expand-sm navbar-default">

            <div class="navbar-header">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="{{url('/')}}">Home</a>
                <a class="navbar-brand hidden" href="{{url('/')}}">H</a>
            </div>

            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
    		

                    @if(str_contains( Session::get('menu_access'), 'TS06')||str_contains( Session::get('menu_access'), 'TK01'))
                    <h3 class="menu-title">Transaksi</h3><!-- /.menu-title -->
			<li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" 
			aria-haspopup="true" aria-expanded="false"> 
			<i class="menu-icon fa fa-atlas"></i>Salesman</a>
                        <ul class="sub-menu children dropdown-menu">
                        @if(str_contains( Session::get('menu_access'), 'TK01'))
                            <li><i class="menu-icon fa fa-location-arrow"></i><a 
				href="{{url('/salesactivity')}}">Check In/Check Out
				</a></li>
                        @endif
                        

                        @if(str_contains( Session::get('menu_access'),'TS06'))
        	                <li><i class="menu-icon fa fa-book"></i>
			<a href="{{url('/sosales')}}">SO Salesman</a></li>
                         @endif

				
 	                   </ul>
                    </li>
		    @endif

		    @if(str_contains( Session::get('menu_access'), 'TS01') || str_contains( Session::get('menu_access'), 'TS08') || str_contains( Session::get('menu_access'), 'TS05') || str_contains( Session::get('menu_access'), 'TS07') || str_contains( Session::get('menu_access'), 'TS09') || str_contains( Session::get('menu_access'), 'TK02') || str_contains(Session::get('menu_access'), 'TS02') || str_contains(Session::get('menu_access'), 'TS11'))                    
		    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-book"></i>SAD</a>
                        <ul class="sub-menu children dropdown-menu">
                        
                        @if(str_contains( Session::get('menu_access'),'TS05'))   
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/sosad')}}">Sales Order</a></li>
                        @endif
                        @if(str_contains( Session::get('menu_access'),'TS09'))
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/socons')}}">SO Consignment</a></li>
                        @endif
                        @if(str_contains( Session::get('menu_access'),'TS08'))    
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/soreturbrowse')}}">Sales Order Return</a></li>
                        @endif
                        @if(str_contains( Session::get('menu_access'),'TS07'))    
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/sosalesoh')}}">Sales Order On Hold</a></li>
                        @endif
                        @if(str_contains( Session::get('menu_access'), 'TS01'))
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/mrpeod')}}">MRP</a></li>
                        @endif
                        @if(str_contains( Session::get('menu_access'), 'TS12'))
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/mrppo')}}">PO MRP</a></li>
                        @endif
                        @if(str_contains( Session::get('menu_access'), 'TS02'))
                            <li><i class="menu-icon fa fa-book-open"></i><a href="{{url('/porcp1')}}">PO Receipt</a></li>
                        @endif
                        
                        @if(str_contains( session::get('menu_access'), 'TK02'))
            			<li><i class="menu-icon fa fa-flag-checkered"></i><a 
            				href="{{url('/checkincheckoutbrowse')}}">Sales Activity Browse
            				</a></li>
            			@endif

                        @if(str_contains( session::get('menu_access'), 'TS11'))
                        <li><i class="menu-icon fa fa-flag-checkered"></i><a 
                            href="{{url('/checksoqad')}}">Check SO QAD
                            </a></li>
                        @endif
                        
                        </ul>
                    </li>

		    @endif

		    @if(str_contains( Session::get('menu_access'), 'TS03')||str_contains( Session::get('menu_access'), 'TS04'))
                    <h3 class="menu-title">Shipment</h3>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-globe"></i>Shipment</a>
                        <ul class="sub-menu children dropdown-menu">
                        @if(str_contains( Session::get('menu_access'), 'TS03'))    
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/createdo')}}">Create SPB</a></li>
                        @endif
                        @if(str_contains( Session::get('menu_access'), 'TS04'))    
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/do')}}">Browse SPB</a></li>
                        @endif   
                        @if(str_contains( Session::get('menu_access'), 'TS13'))    
                        <li><i class="menu-icon fa fa-book"></i><a href="{{url('/menuspbcheck')}}">Stock SPB</a></li>
                        @endif     
                              
                        </ul>
                    </li>
		    @endif

            @if(str_contains( Session::get('menu_access'), 'IV01'))
            <h3 class="menu-title">Inventory</h3>
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-database"></i>Inventory</a>
                <ul class="sub-menu children dropdown-menu">   
                <li><i class="menu-icon fa fa-database"></i><a href="{{url('inv')}}">Inventory</a></li>
                </ul>
            </li>
            @endif

                    @if(str_contains( Session::get('menu_access'), 'MT'))
                    <h3 class="menu-title">Master</h3><!-- /.menu-title -->
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-wrench"></i>Setting</a>
                        <ul class="sub-menu children dropdown-menu">
                            @if(str_contains( Session::get('menu_access'), 'MT01'))
                            <li><i class="menu-icon fa fa-user"></i><a href="{{url('/usermt')}}">User Master</a></li>
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT02'))
                            <li><i class="menu-icon fa fa-users"></i><a href="{{url('/rolemaster')}}">Role Master</a></li>
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT03'))
                            <li><i class="menu-icon fa fa-briefcase"></i><a href="{{url('/sitemaster')}}">Site Master</a></li>
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT04'))
                            <li><i class="menu-icon fa fa-cube"></i><a href="{{url('/itemmt')}}">Item Master</a></li>
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT05'))
                            <li><i class="menu-icon fa fa-shopping-bag"></i><a href="{{url('/custmt')}}">Customer Master</a></li>
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT06'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/suppmaint')}}">Supplier Master</a></li>
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT07'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/custrelation')}}">Customer Relation</a></li> 
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT08'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/activitymt')}}">Activity Master</a></li> 
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT09'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/custshipto')}}">Customer Ship To</a></li> 
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT10'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/itemkonversi')}}">Item Convertion</a></li> 
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT11'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/approvalmt')}}">Approval Level</a></li> 
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT12'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/locmenu')}}">Location</a></li> 
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT13'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/menurnbr')}}">Running Number</a></li> 
                            @endif
                            @if(str_contains( Session::get('menu_access'), 'MT14'))
                            <li><i class="menu-icon fa fa-cubes"></i><a href="{{url('/itemchildmenu')}}">Item Parent Child Master</a></li> 
                            @endif
                        </ul>
                    </li>
                    @endif
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside>

    <!-- Side Navbar -->

    <!-- Header & Menu -->

    <div id="right-panel" class="right-panel">

        <!-- Header-->
        <header id="header" class="header">

            <div class="header-menu">

                <div class="col-sm-7">
                    <a id="menuToggle" class="menutoggle pull-left"><i class="fa fa fa-tasks"></i></a>
                    <!--
                    <div class="header-left">
                        <button class="search-trigger"><i class="fa fa-search"></i></button>
                        <div class="form-inline">
                            <form class="search-form">
                                <input class="form-control mr-sm-2" type="text" placeholder="Search ..." aria-label="Search">
                                <button class="search-close" type="submit"><i class="fa fa-close"></i></button>
                            </form>
                        </div>

                        <div class="dropdown for-notification">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                <span class="count bg-danger">5</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notification">
                                <p class="red">You have 3 Notification</p>
                                <a class="dropdown-item media bg-flat-color-1" href="#">
                                <i class="fa fa-check"></i>
                                <p>Server #1 overloaded.</p>
                            </a>
                                <a class="dropdown-item media bg-flat-color-4" href="#">
                                <i class="fa fa-info"></i>
                                <p>Server #2 overloaded.</p>
                            </a>
                                <a class="dropdown-item media bg-flat-color-5" href="#">
                                <i class="fa fa-warning"></i>
                                <p>Server #3 overloaded.</p>
                            </a>
                            </div>
                        </div>

                        <div class="dropdown for-message">
                            <button class="btn btn-secondary dropdown-toggle" type="button"
                                id="message"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ti-email"></i>
                                <span class="count bg-primary">9</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="message">
                                <p class="red">You have 4 Mails</p>
                                <a class="dropdown-item media bg-flat-color-1" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/1.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Jonathan Smith</span>
                                    <span class="time float-right">Just now</span>
                                        <p>Hello, this is an example msg</p>
                                </span>
                            </a>
                                <a class="dropdown-item media bg-flat-color-4" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/2.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Jack Sanders</span>
                                    <span class="time float-right">5 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                </span>
                            </a>
                                <a class="dropdown-item media bg-flat-color-5" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/3.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Cheryl Wheeler</span>
                                    <span class="time float-right">10 minutes ago</span>
                                        <p>Hello, this is an example msg</p>
                                </span>
                            </a>
                                <a class="dropdown-item media bg-flat-color-3" href="#">
                                <span class="photo media-left"><img alt="avatar" src="images/avatar/4.jpg"></span>
                                <span class="message media-body">
                                    <span class="name float-left">Rachel Santos</span>
                                    <span class="time float-right">15 minutes ago</span>
                                        <p>Lorem ipsum dolor sit amet, consectetur</p>
                                </span>
                            </a>
                            </div>
                        </div>
                    </div>
                    -->
                    <div class="header-left">
                        @yield('head-content')
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="user-avatar rounded-circle" src="{{asset('images/icon.jpg')}}" alt="User Avatar">
                        </a>

                        <div class="user-menu dropdown-menu">
                            <a class="nav-link bot" href="{{ url('/changepassword') }}"><i class="fa fa-user"></i>Change Password</a>

                            <a class="nav-link" data-toggle="modal" data-target="#logoutModal" style="cursor: pointer;">
                              <i class="fa fa-power-off"></i>
                              Logout
                            </a>
                        </div>
                    </div>
                    <div class="vertical float-right"></div>
                    <div class="float-right" style="margin: 7px 15px 0 0;">Hello, {{Session::get('name')}} </div>
                </div>

            </div>

        </header><!-- /header -->
        <!-- Header-->

        <div class="breadcrumbs">
            @yield('content-title')
        </div>
        
        <div class="content mt-3">

            @yield('content')

        </div> <!-- .content -->
    </div>


    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
              <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
              <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
              <a class="btn btn-primary" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                {{ __('Logout') }} </a>
              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  @csrf
              </form>
            </div>
          </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> <!--Date Picker-->
    <script src="{{url('vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{url('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{url('vendors/bootstrap-select/js/bootstrap-select.js')}}"></script> <!--Searchable Select Dropdown-->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js"></script>
    
    @yield('scripts')

    <script>
         var time = new Date().getTime();
         $(document.body).bind("mousemove keypress", function(e) {
             time = new Date().getTime();
         });

         function refresh() {
             if(new Date().getTime() - time >= 310000) {
                 window.location.reload(true);
             }
             else {
                 setTimeout(refresh, 10000);
             }
         }

         setTimeout(refresh, 10000);
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            if(window.innerWidth <= 576){
                document.querySelector('body').classList.remove('open');
              }else{
                document.querySelector('body').classList.add('open');   
              }

            
            window.addEventListener("resize", myFunction);

            function myFunction() {
              if(window.innerWidth <= 576){
                document.querySelector('body').classList.remove('open');
              }else{
                document.querySelector('body').classList.add('open');   
              }
            }
        });
    </script>
    
    <script src="{{url('assets/js/main.js')}}"></script>   <!--buat side navbar-->


</body>

</html>

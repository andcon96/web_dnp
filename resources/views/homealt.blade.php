@extends('layout.newlayout')


@section('content-title')
    <div class="col-sm-4 col-md-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Home</h1>
            </div>
        </div>
    </div>
@endsection


@section('content')
    <!-- Flash Menu -->
    @if(session()->has('updated'))
          <div class="alert alert-success  alert-dismissible fade show"  role="alert">
              {{ session()->get('updated') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
    @endif

    @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" id="getError" role="alert">
              {{ session()->get('error') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
    @endif

    @if(count($errors) > 0)
    <ul>    
         <div class = "alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
               @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
               @endforeach
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
            </ul>
         </div>
    </ul>
    @endif

    @if(Session::get('salesman') == 'N')
        <div class="col-xl-12 mb-0">
            <a href="/createdo">
            <div class="col-sm-6 col-md-6 col-lg-3 offset-lg-3 mb-0">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0">
                        <h4 class="mb-0">
                            <span class="count" style="font-size: 18px;">{{$so}}</span>
                        </h4>
                        <p class="text-light" style="font-size: 18px;">Open SO</p>

                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <canvas id="widgetChart1"></canvas>
                        </div>

                    </div>

                </div>
            </div>
            </a>
            <a href="/do">
            <div class="col-sm-6 col-md-6 col-lg-3 mb-0">
                <div class="card text-white bg-flat-color-2">
                    <div class="card-body pb-0">
                        <h4 class="mb-0">
                            <span style="font-size: 18px;">{{$spb}}</span>
                        </h4>
                        <p class="text-light" style="font-size: 18px;">Unconfirm SO</p>

                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <canvas id="widgetChart2"></canvas>
                        </div>

                    </div>
                </div>
            </div>
            </a>
        </div>
    @else
        <div class="col-xl-12 mb-0">
            <a href="/salesactivity">
            <div class="col-sm-3 col-md-6 col-lg-3 offset-lg-3 mb-0">
                <div class="card text-white bg-flat-color-1">
                    <div class="card-body pb-0">
                        <p class="text-light" style="font-size: 18px;">Checkin Checkout</p>

                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <canvas id="widgetChart1"></canvas>
                        </div>

                    </div>

                </div>
            </div>
            </a>
            <a href="/sosales">
            <div class="col-sm-3 col-md-6 col-lg-3 mb-0">
                <div class="card text-white bg-flat-color-2">
                    <div class="card-body pb-0">
                        <p class="text-light" style="font-size: 18px;">Buat Sales Order</p>

                        <div class="chart-wrapper px-0" style="height:70px;" height="70">
                            <canvas id="widgetChart2"></canvas>
                        </div>

                    </div>
                </div>
            </div>
            </a>
        </div>
    @endif

         
    @if(str_contains( Session::get('menu_access'), 'HO01'))
    <div class="row col-12" style="margin-left:1px;">	  
        <!-- Total -->
        <div class="col-md-12">
            <div class="card card-secondary">
                    <div class="card-header" style="background-color:#99FFCC; opacity:0.8;">
                        <h4 class="card-title mt-2" style="color:black;">Top 10 Yearly</h4>
                        
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="totchart" style="display: block; height: 350px; width:955px;" class="chartjs-render-monitor"></canvas>
                        </div>
                    </div>
                    <!-- /.card-body -->
            </div>
        </div>

        <!-- Sales -->
        <div class="col-md-12 col-lg-6">
            <div class="card card-danger">
                <div class="card-header" style="background-color: #800000;">
                  <h4 class="card-title mt-2" style="color:white;">Top 10 Customers</h4>
                  
                </div>
                <div class="card-body">
                  <div class="chart">
                  <canvas background-color:"black" width="100%" height="30%" class="chartjs-render-monitor" id="topten" style="display: block; height: 60%; width: 100%;" ></canvas>    
                  </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <!-- Items -->
        <div class="col-md-12 col-lg-6">
            <div class="card card-success">
                    <div class="card-header" style="background-color: #228B22;"> 
                         <h4 class="card-title mt-2" style="color:white;">Top 10 Items</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                        <canvas  width="100%" height="30%" class="chartjs-render-monitor" id="toptenheal" style="display: block; height: 60%; width: 100%;" ></canvas>
                        </div>
                    </div>
                    <!-- /.card-body -->
            </div>
        </div>

        <!-- Region -->
        <div class="col-md-12 col-lg-6">
            <div class="card card-secondary">
                    <div class="card-header" style="background-color:#000066">
                         <h4 class="card-title mt-2" style="color:white;">Top 10 Regions</h4>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas width="100%" height="60%" class="chartjs-render-monitor " id="engchart" style=" height:60%; width:50%" ></canvas>    
                        </div>
                    </div>
                    <!-- /.card-body -->
            </div>
        </div>
    </div>
    @endif

@endsection

@section('scripts')
    <script src="vendors/chart.js/dist/Chart.bundle.min.js"></script>

    <script>
        var toplabel = new Array();
        var topdata = new Array();

        <?php 
            foreach($topsales as $topsales) {
                echo 'toplabel.push("'.$topsales->cust_desc.'");topdata.push("'.$topsales->g_total.'");';
            }
        ?>

        var toplabel2 = new Array();
        var topdata2 = new Array();

        <?php 
            foreach($topitem as $topitem){
                echo 'toplabel2.push("'.$topitem->itemdesc.'");topdata2.push("'.$topitem->g_total.'");';
            }
        ?>

        
        var topkey = new Array();
        var topval = new Array();

        <?php 
            foreach($topregion as $topregion){
                echo 'topkey.push("'.$topregion->region.'");topval.push('.$topregion->g_total.');';
            }
        ?>

        
        topYear = new Array();
        topYearPrev = new Array();

        <?php 
            foreach($topyear as $topyear){

                if($topyear->year == date('Y')){
                    echo 'topYear.push('.$topyear->g_total.');';    
                }else if(is_null($topyear->year)){
                    echo 'topYear.push(0);';
                    echo 'topYearPrev.push(0);';
                }else if($topyear->year == date('Y') - 1){
                    for($i = 1; $i <= 12; $i++){
                        if($i == $topyear->month){
                            echo 'topYearPrev['.$i.' - 1] = '.$topyear->g_total.';';
                        }
                    }

                }
            
            }
        ?>

    </script>

    <script src="assets/js/dashboardalt.js"></script>
    <script src="assets/js/widgets.js"></script>
@endsection
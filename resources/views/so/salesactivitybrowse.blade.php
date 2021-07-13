@extends('layout.newlayout')

@section('content-title')
<div class="col-4">
  <div class="page-header float-left full-head">
    <div class="page-title">
      <h1>Sales Activity Browse</h1>
    </div>
  </div>
</div>
@endsection

@section('content')

<!-- Flash Menu -->
@if(session()->has('updated'))
<div class="alert alert-success  alert-dismissible fade show" role="alert">
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

<ul>
  @if(count($errors) > 0)
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </ul>
  </div>
  @endif
</ul>

<!--- SEARCHING BROWSER CHECKINCHECKOUT-->
<div class="form-group row col-md-12">
  <label for="s_salesman" class="col-md-2 col-form-label text-md-right">{{ __('Salesman Name') }}</label>
  <div class="col-md-3">
    <select id="s_salesman" class="form-control" name="s_salesman" autofocus autocomplete="off">
	  <option value=""> Select Salesman Name </option>
	  @foreach($salesman as $salesman)	
	  <option value="{{$salesman->username_sales}}"> {{$salesman->username}} -- {{$salesman->name}}</option>
	  @endforeach
	
    </select>
  </div>
  <label for="s_customer" class="col-md-2 col-form-label text-md-right">{{ __('Customer') }}</label>
  <div class="col-md-3">
    <select id="s_customer" class="form-control" name="s_customer" autofocus autocomplete="off">
	  <option value=""> Select Customer </option>
	  @foreach($customers as $customers)	
	  <option value="{{$customers->to_cust}}"> {{$customers->cust_code}} -- {{$customers->cust_desc}}</option>
	  @endforeach	
    </select>
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="datefrom" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Check In Date') }}</label>
  <div class="col-md-4 col-lg-3">
    <input type="text" id="datecheckin" class="form-control" name='datecheckin' placeholder="YYYY-MM-DD" required autofocus autocomplete="off">
  </div>
  <label for="dateto" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Check Out Date') }}</label>
  <div class="col-md-4 col-lg-3">
    <input type="text" id="datecheckout" class="form-control" name='datecheckout' placeholder="YYYY-MM-DD" required autofocus autocomplete="off">
  </div>
</div>
<div class="form-group row col-md-12">
  <label for="s_status" class="col-md-3 col-form-label text-md-right">{{ __('') }}</label>
  <div class="offset-0">
    <input type="button" class="btn bt-action newUser" id="btnsearch" value="Search" />
    <!-- <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-refresh"></i></button> -->
  
  </div>
</div>
<input type="hidden" id="salesmantemp" value=""/>
<input type="hidden" id="customertemp" value=""/>
<input type="hidden" id="checkindatetemp" value=""/>
<input type="hidden" id="checkoutdatetemp" value=""/>


<!--Table Menu-->
<div class="col-md-12"><hr></div>
<div class="table-responsive col-12">
  <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>Salesman Name</th>
        <th>Customer</th>
        <th>Activity</th>
        <th>Check In Date</th>
        <th>Check Out Date</th>
      </tr>
    </thead>
    <tbody>
      @include('so.table-checkincheckout')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>


@endsection


@section('scripts')
<script type="text/javascript">

 $("#datecheckin").datepicker({
    dateFormat: 'yy-mm-dd',
  });

  $("#datecheckout").datepicker({
    dateFormat: 'yy-mm-dd',
  });

 

  $(document).ready(function() {

    $("#s_salesman").select2({
      width: '100%'
    });

    $("#s_customer").select2({
	width: '100%'
    });	   

	function fetch_data(page, sort_type, column_name, salesman, customer, checkindate, checkoutdate) {
	    $.ajax({
	      url: "/salesactivity/searching?page=" + page + "&sorttype=" + sort_type + "&sortby=" + column_name + "&salesman=" + salesman + "&customer=" + customer + "&checkindate=" + checkindate + "&checkoutdate=" + checkoutdate,
	      success: function(data) {
		console.log(data);
		$('tbody').html('');
		$('tbody').html(data);

	      }
	    })
	  }

	  $(document).on('click', '#btnsearch', function() {
	    var salesman = $('#s_salesman').val();
	    var customer = $('#s_customer').val();
	    var checkindate = $('#datecheckin').val();
	    var checkoutdate = $('#datecheckout').val();
	    var column_name = $('#hidden_column_name').val();
	    var sort_type = $('#hidden_sort_type').val();
	    var page = 1;

	    document.getElementById('salesmantemp').value = salesman;
	    document.getElementById('customertemp').value = customer;
            document.getElementById('checkindatetemp').value = checkindate;
	    document.getElementById('checkoutdatetemp').value = checkoutdate;

	    fetch_data(page, sort_type, column_name, salesman, customer, checkindate, checkoutdate);
	  });

	  $(document).on('click', '.pagination a', function(event) {
	    event.preventDefault();
	    var page = $(this).attr('href').split('page=')[1];
	    $('#hidden_page').val(page);
	    var salesman = $('#salesmantemp').val();
	    var customer = $('#customertemp').val();
	    var checkindate = $('#checkindatetemp').val();
	    var checkoutdate = $('#checkoutdatetemp').val();
	    var column_name = $('#hidden_column_name').val();
	    var sort_type = $('#hidden_sort_type').val();

	    fetch_data(page, sort_type, column_name, salesman, customer, checkindate, checkoutdate);
	  });

  });
</script>
@endsection

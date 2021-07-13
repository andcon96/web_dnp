@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Customer Master</h1>
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

	  	<ul>    
	  	@if(count($errors) > 0)
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
	  	@endif
	  	</ul>

	<div class="col-12">
		<div class="col-md-2 col-sm-2 mb-3 ml-2 input-group">
  			<form action="/reloadtabelcustomer" method="POST">
      				@csrf
          			<input type="submit" class="btn bt-action" 
				data-toggle="modal" data-target="#loadingtable" 
				data-backdrop="static" data-keyboard="false"  
          			id="btnrefresh" value="Load Table"
			 	style="float:right" />
      			</form>
  		</div>

	</div>
<div class="col-12"><hr></div>

		  
		  <div class="col-12 form-group row">
              <!--Search Disini-->
              <label for="s_custcode" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Cust Code') }}</label>
              <div class="col-md-4 col-sm-4 mb-2 input-group">
                  <input id="s_custcode" type="text" class="form-control" name="s_custcode" 
                  value="" autofocus autocomplete="off">
              </div>
              <label for="s_region" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Region') }}</label>
              <div class="col-md-4 col-sm-4 mb-2 input-group">
                  <input id="s_region" type="text" class="form-control" name="s_region" 
                  value="" autofocus autocomplete="off">
			  </div>
			  <label for="s_custdesc" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Cust Desc') }}</label>
              <div class="col-md-4 col-sm-4 mb-2 input-group">
                  <input id="s_custdesc" type="text" class="form-control" name="s_custdesc" 
                  value="" autofocus autocomplete="off" min="0">
              </div>
              <label for="s_custsite" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Site') }}</label>
              <div class="col-md-4 col-sm-4 mb-2 input-group">
                  <input id="s_custsite" type="text" class="form-control" name="s_custsite" 
                  value="" autofocus autocomplete="off">
              </div>
              <label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
              <div class="col-md-4 col-sm-4 mb-2 input-group">
                  <input type="button" class="btn bt-action" 
                  id="btnsearch" value="Search"  style="float:right">
			  </div>
			  <label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
			
		  </div>
		  <div class="col-md-12"><hr></div>
		<input type="hidden" id="custcodetemp" name="custcodetemp"/>
		<input type="hidden" id="regiontemp" name="regiontemp"/>
		<input type="hidden" id="custdesctemp" name="custdesctemp"/>
		<input type="hidden" id="sitetemp" name="sitetemp"/>

		<!--Table Menu-->
		<div class="col-12" style="overflow: auto; display: block;white-space: nowrap; margin-left: auto; margin-right: auto;">
			<table class="table  table-bordered" id="dataTable" width="100%" cellspacing="0" >
				<thead>
					<tr>
						<th class="sorting" data-sorting_type="asc" data-column_name="cust_code" style="cursor: pointer;width: 10%;">Cust Code<span id="name_icon"></span></th>
						<th class="sorting" data-sorting_type="asc" data-column_name="cust_desc" style="cursor: pointer;width: 10%;">Cust Name</th> 
						<th class="sorting" data-sorting_type="asc" data-column_name="customer_site" style="cursor: pointer;width: 10%;">Site</th> 
						<th class="sorting" data-sorting_type="asc" data-column_name="customer_region" style="cursor: pointer;width: 10%;">Region</th> 
						<th class="sorting" data-sorting_type="asc" data-column_name="cust_top" style="cursor: pointer;width: 10%;">TOP</th>
						<th class="sorting" data-sorting_type="asc" data-column_name="custcredit_limit" style="cursor: pointer;width: 10%;">Credit Limit</th>   
						<th style="width: 35%;">Address</th>
					</tr>
				</thead>
				<tbody>
					@include('setting.table-customer')              
				</tbody>
	      </table>
	      <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
	      <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="cust_code" />
	      <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
	</div>

  
  <!-- refresh table modal -->
  <div class="modal fade" id="loadingtable" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="spinner-grow text-danger"></div>
      <div class="spinner-grow text-warning" style="animation-delay:0.2s;"></div>
      <div class="spinner-grow text-success" style="animation-delay:0.45s;"></div>
      <div class="spinner-grow text-info"style="animation-delay:0.65s;"></div>
      <div class="spinner-grow text-primary"style="animation-delay:0.85s;"></div>
    </div>
  </div>
</div>
 
@endsection



@section('scripts')	
	<script>
$(document).on('click','#btnrefresh',function(){
    $(document).ready(function () {
      $(document).keydown(function (event) {
        var charCode = event.charCode || event.keyCode || event.which;
        if (charCode == 27 ) {
          
          return false;
        }
      });
    });
  })
		
		function clear_icon()
     	{
			$('#id_icon').html('');
			$('#post_title_icon').html('');
     	}
		
		
		function fetch_data(page, sort_type, sort_by, custcode, custdesc, region, custsite){
			$.ajax({
				url:"/custmt/pagination?page="+page+"&sortby="+sort_by+"&sorttype="+sort_type+"&custcode="+custcode+"&custdesc="+custdesc+"&region="+region+"&custsite="+custsite,
				success:function(data){
					console.log(data);
					$('tbody').html('');
					$('tbody').html(data);
				} 
			})
		}
		
		
		$(document).on('click', '#btnsearch', function(){
			var custcode = $('#s_custcode').val();
			var custdesc = $('#s_custdesc').val();
			var region = $('#s_region').val();
			var custsite = $('#s_custsite').val();

			document.getElementById('custcodetemp').value = custcode;
			document.getElementById('custdesctemp').value = custdesc;
			document.getElementById('regiontemp').value = region;
			document.getElementById('sitetemp').value = custsite;			
	
			var column_name = $('#hidden_column_name').val();
			var sort_type = $('#hidden_sort_type').val();
			var page = 1;

			fetch_data(page, sort_type, column_name, custcode, custdesc, region, custsite);
		});

		
		$(document).on('click', '.sorting', function(){
			var column_name = $(this).data('column_name');
			var order_type = $(this).data('sorting_type');
			var reverse_order = '';
			if(order_type == 'asc')
			{
			$(this).data('sorting_type', 'desc');
			reverse_order = 'desc';
			clear_icon();
			$('#'+column_name+'_icon').html('<span class="glyphicon glyphicon-triangle-bottom"></span>');
			}
			if(order_type == 'desc')
			{
			$(this).data('sorting_type', 'asc');
			reverse_order = 'asc';
			clear_icon();
			$('#'+column_name+'_icon').html('<span class="glyphicon glyphicon-triangle-top"></span>');
			}
			$('#hidden_column_name').val(column_name);
			$('#hidden_sort_type').val(reverse_order);
			var page = $('#hidden_page').val();
			var custcode = $('#s_custcode').val();
			var custdesc = $('#s_custdesc').val();
			var region = $('#s_region').val();
			var custsite = $('#s_custsite').val();
			fetch_data(page, reverse_order, column_name, custcode, custdesc, region, custsite);
     	});

		
		$(document).on('click', '.pagination a', function(event){
			event.preventDefault();
			var page = $(this).attr('href').split('page=')[1];
			$('#hidden_page').val(page);
			var column_name = $('#hidden_column_name').val();
			var sort_type = $('#hidden_sort_type').val();
			var custcode = document.getElementById('custcodetemp').value;
			var custdesc = document.getElementById('custdesctemp').value;
			var region = document.getElementById('regiontemp').value;
			var custsite = document.getElementById('sitetemp').value;
			fetch_data(page, sort_type, column_name, custcode, custdesc, region, custsite);
		});
	</script>
@endsection

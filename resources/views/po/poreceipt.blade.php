@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Purchase Order Receipt</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style type="text/css">
    tbody{
        font-size: 14px;

    }

    h1{
      color: black !important;
    }

    thead{
        background-color: #4e73df;
        text-align: left;
        color:white !important;
    }

    tr:nth-child(even) {background-color: #f2f2f2;}

    tr{
      border-bottom: 1px solid #6D6F70 !important;
    }

    #dataTable thead,
    #dataTable tbody,
    #dataTable td{
        vertical-align: middle;
        color:#000000;
        border: none;
        font-size:22px;
        font-weight: 600;
    }


    .bt-action{
      font-size: 20px;
      width: 150px;
      background-color:#4e73df;
      color:white;
    }
  
    tbody .fas{
      margin-right: 5px;
      margin-left: 5px;
    }


    @media only screen and (max-width: 800px) {
        
    /* Force table to not be like tables anymore */
    #dataTable table, 
    #dataTable thead, 
    #dataTable tbody, 
    #dataTable th, 
    #dataTable td, 
    #dataTable tr { 
        display: block; 
    }

    /* Hide table headers (but not display: none;, for accessibility) */
    #dataTable thead tr { 
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    #dataTable tr { border: 1px solid #ccc; }

    #dataTable td { 
        /* Behave  like a "row" */
        border: none;
        border-bottom: 1px solid #eee; 
        position: relative;
        padding-left: 40%; 
        white-space: normal;
        text-align:left;
    }

    #dataTable td:before { 
        /* Now like a table header */
        position: absolute;
        /* Top/left values mimic padding */
        top: 6px;
        left: 6px;
        width: 45%; 
        padding-right: 10px; 
        white-space: nowrap;
        text-align:left;
        font-weight: bold;
    }

    /*
    Label the data
    */
    #dataTable td:before { 
        content: attr(data-title); 
        vertical-align: top;
        padding: 6px 0px 0px 0px;
    }
}   
</style>

<script>
      $( function() {
        $( "#effdate" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
        $( "#shipdate" ).datepicker({
            dateFormat : 'dd/mm/yy'
        });
    });
</script>


@if(session('error'))
  <div class="alert alert-danger" id="getError">
    {{ session()->get('error') }}
  </div>
@endif

@if(session()->has('updated'))
	      	<div class="alert alert-success  alert-dismissible fade show"  role="alert">
	          	{{ session()->get('updated') }}
	          	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
	              	<span aria-hidden="true">&times;</span>
	          	</button>
	      	</div>
	  	@endif


      <form action="/porcp1" method="post">
        {{csrf_field()}}
        <div class="form-group row">
              <label for="ponbr" class="col-form-label text-md-right" style="margin-left:25px">{{ __('PO Number') }}</label>
              <div class="col-xl-2 col-lg-2 col-md-8 col-sm-12 col-xs-12">
                  <input id="ponbr" type="text" class="form-control" name="ponbr" autocomplete="off" 
                  value="" autofocus>
              </div>
              <label for="receiptdate" class="col-form-label text-md-right" style="margin-left:25px">{{ __('Receipt Date') }}</label>
              <div class="col-xl-2 col-lg-2 col-md-8 col-sm-12 col-xs-12">
                  <input id="receiptdate" type="text" class="form-control" name="receiptdate" 
                  value="{{ Carbon\Carbon::parse($date)->format('d-m-Y')  }}" readonly>
              </div>

              <div class="offset-md-3 offset-lg-0 offset-xl-0 offset-sm-0 offset-xs-0" id='btn'>
                <input type="submit" class="btn btn-info" 
                id="btnsearch" value="Search" />
              </div>
        </div>            
      </form>
      <div class="card shadow mb-4">
	    <div class="card-body">
	      <div class="table-responsive col-lg-12 col-md-12">
	        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
	          <thead>
	            <tr>
				   <th>Line</th>
				   <th>Item Number</th>
			        <th>Description</th>  
				   <th>QTY Order</th> 
                                   <th>QTY Open</th> 
                                 <th>QTY Receipt</th> 
                                 <th>QTY Input</th> 
                       <th>Action</th> 
			         
			    </tr>
		       </thead>
                 <tbody>					
				@foreach ($data as $show)
					<tr>
                         <td>{{ $show->xpod_line }}</td>
                         <td>{{ $show->xpod_part }}</td>
                		<td>{{ $show->xpod_desc }}</td>
					<td>{{ $show->xpod_qty_ord}}</td>
			<td>{{ $show->xpod_qty_open }} </td>
                           <td>{{ $show->xpod_qty_ship}}</td> 
                           <td>{{ $show->xpod_qty_rcvd}}</td>
                         <td>
                             @if ($show->xpod_qty_open != '0')
                				<a href="" class="editModal"  data-nbr="{{$show->xpod_nbr}}"
                					data-line= "{{$show->xpod_line}}" data-qty= "{{$show->xpod_qty_rcvd}}" data-ord= "{{$show->xpod_qty_ord}}" data-ship= "{{$show->xpod_qty_ship}}" data-toggle='modal' data-target="#editModal"><i class="fas fa-edit"></i></button>
			     @endif               		
</td>
               		
					</tr>
				@endforeach			                 
	          </tbody>
	
     </table>
     </div>
     @if ($qdocResult == "")
     <div class="row">
             <div class="col-lg-2 offset-lg-4">
      <div class="card border shadow">
             
          <form action="/porcp1" method="post">
                  {{ csrf_field() }}                                                                                
               <input disable type="hidden" name="conf" value= "Cancel">                                                    
	       <input type="submit" class="col-lg-12" value="Cancel" ></button>
                 
           </form> <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
         </div>
      </div>
    <div class="col-lg-2">
      <div class="card border shadow"> 
             
          <form action="/porcpok" method="post">
                  {{ csrf_field() }}            
               <input disable type="hidden" name="nbr" value= {{ $ponbr }} >                   
               <input disable type="hidden" name="conf" value= "Confirm">                                                    
	       <input type="submit" class="col-lg-12" value="Confirm" ></button>
                 
           </form> <!-- <h6 class="font-weight-bold" id="unpochart" onclick="poClickEvent4()">Unconfirm PO By Supplier</h6> -->
         </div>
      </div>  
          
</div> 
@endif
     </div>
     </div>
     
<div class="modal fade" id="editModal"  tabindex="-1"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<!-- konten modal-->
			<div class="modal-content">
				<div class="modal-header">
			        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Data</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
		      	</div>
	         	<div class="panel-body">
					<!-- heading modal -->
					<form class="form-horizontal" role="form" method="POST" action="/porcpupd">
						
						    {{ csrf_field() }}
	                    <div class="modal-body">
	                    	<div class="form-group row">
                               <input id="e_nbr" type="hidden" class="form-control" name="e_nbr" readonly="true">
<input id="e_ord" type="hidden" class="form-control" name="e_ord" readonly="true">
<input id="e_ship" type="hidden" class="form-control" name="e_ship" readonly="true">
                               <input id="e_line" type="hidden" class="form-control" name="e_line" readonly="true">
				            
		                        <label for="e_qty" class="col-md-3 col-form-label text-md-right">{{ __('Qty Input') }}</label>
				                <div class="col-md-7">
				                    <input id="e_qty" type="text" class="form-control" name="e_qty" >
				                </div>
							</div>
	                    	
							
	                    </div>
	                     
	                    <div class="modal-footer">
					          <button type="button" class="btn btn-info bt-action" data-dismiss="modal">Close</button>
					          <button type="submit" class="btn btn-success bt-action">Save</button>
					    </div>
					</form> 
	            </div>
			</div>
		</div>
</div>     

<script type="text/javascript">	

	$(document).on('click','.editModal',function(){ // Click to only happen on announce links
     
     /*alert('tst');*/
     var uid = $(this).data('nbr');
     var line = $(this).data('line');
     var qty = $(this).data('qty');     
     var ord = $(this).data('ord');  
     var ship = $(this).data('ship'); 
     document.getElementById("e_nbr").value = uid;
     document.getElementById("e_line").value = line;
     document.getElementById("e_qty").value = qty;
     document.getElementById("e_ord").value = ord;
     document.getElementById("e_ship").value = ship;
	
     });
</script>


@endsection

@extends('layout.newlayout')


@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Transaksi / Sales Order Retur</h1>
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

    <form id="new" action="/returqad" method="post">
    	{{ csrf_field() }}

	    <div class="row">
			<div class="form-group row col-md-12">
				<label for="sonbr" class="col-md-2 col-form-label text-md-right">SO Parent</label>
				<div class="col-md-3">
					<input id="sonbr" type="text" class="form-control" name="sonbr" autocomplete="off" maxlength="24" autofocus>
				</div>
					<label for="custcode" class="col-md-2 col-form-label text-md-right"></label>
				<div class="col-md-3">
			    	<button class="btn bt-ref" style="float:left;" id='btnsearch'>
			    		<i class="fa fa-search"></i>
			    	</button>
			    	<button class="btn bt-ref" style="display:none;float:left;" id='btnref'>
			    		<i class="fa fa-refresh"></i>
			    	</button>
				</div>
			</div>
			<div class="form-group row col-md-12">
				<label for="custcode" class="col-md-2 col-form-label text-md-right">Customer</label>
				<div class="col-md-3">
					<input id="custcode" type="text" class="form-control" name="custcode" autocomplete="off" maxlength="24" readonly>
				</div>
				<label for="shipto" class="col-md-2 col-form-label text-md-right">Ship To</label>
				<div class="col-md-3">
					<input id="shipto" type="text" class="form-control" name="shipto" autocomplete="off" maxlength="24" readonly>
				</div>
			</div>
			<div class="form-group row col-md-12">
				<label for="address" class="col-md-2 col-form-label text-md-right">Address</label>
				<div class="col-md-8">
					<input id="address" type="text" class="form-control" name="address" autocomplete="off" maxlength="24" readonly>
				</div>
			</div>
			<div class="form-group row col-md-12 mb-4">
				<label for="notes" class="col-md-2 col-form-label text-md-right">Remarks</label>
				<div class="col-md-8">
					<input id="notes" type="text" class="form-control" name="notes" autocomplete="off" maxlength="24" required readonly>
				</div>
			</div>
	    </div>

	    <div class="row offset-md-1 col-md-10">
	    	<div class="col-md-12 mb-3">
	    		<h4><strong>Detail</strong></h4>
	    	</div>
	    	<table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
			  <thead>
			      <tr id='full'>
			          <th style="width:30%">Item</th>
			          <th style="width:15%">Qty</th>
			          <th style="width:15%">UM</th>
			          <th style="width:15%">Qty Retur</th>
			      </tr>
			  </thead>
			  <tbody id='e_detailapp'>
			  		<td colspan="4" style="color:red;"><center><strong>No Data Available</strong></center></td>
			  </tbody>
			</table>
	    </div>

	    <div class="row col-md-10 offset-md-1 mt-4" id='footer' style="display:none;">
	    	<hr>
        <input type="hidden" name="j_custcode" id="j_custcode">
        <input type="hidden" name="j_shipto" id="j_shipto">
	    	<input type="submit" name="btnsubmit" id="btnsubmit" value="Confirm" class="btn bt-action">
        <button type="button" class="btn bt-action" id="btnloading" style="display:none">
          <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
        </button>
	    </div>
    	
    </form>


@endsection


@section('scripts')
  <script type="text/javascript">
    $(document).ready(function () {
        var counter = 0;


        function selectRefresh() {
          $('.barang').select2({
            tags: true,
            placeholder: "Select an Option",
            width: '100%'
          });
        }

        $("#btnsearch").on("click",function(e){

            var sonbr = document.getElementById('sonbr').value;

           	$.ajax({
           		url:"/alamatretur",
           		data:{
           			sonbr : sonbr,
           		},
           		success:function(data){
           			console.log(data);
                if(!$.trim(data)){
                  alert('SO Number not found');
                }else{
                  var newdata = data.split("||");
                  document.getElementById('custcode').value = newdata[0];
                  document.getElementById('shipto').value = newdata[1];
                  document.getElementById('address').value = newdata[2];
                  document.getElementById('notes').readOnly = false;
                  document.getElementById('btnsearch').style.display = 'none';
                  document.getElementById('btnref').style.display = '';
                  document.getElementById('sonbr').readOnly = true;
                  document.getElementById('footer').style.display = '';
                  document.getElementById('j_shipto').value = newdata[4];
                  document.getElementById('j_custcode').value = newdata[3];
                }
           		}
           	})

            $.ajax({
              url:"/detailretur",
              data:{
                sonbr : sonbr,
              },
              success:function(data){
                  console.log(data);
                  $("#e_detailapp").html('').append(data);
              }
            })

            e.preventDefault();
        });

        $("#btnref").on("click",function(e){
        	document.getElementById('sonbr').value = '';
        	document.getElementById('sonbr').readOnly = false;
        	document.getElementById('custcode').value = '';
        	document.getElementById('shipto').value = '';
        	document.getElementById('address').value = '';
        	document.getElementById('notes').value = '';
        	document.getElementById('notes').readOnly = true;
		      document.getElementById('footer').style.display = 'none';
        	$("#e_detailapp").html('').append('');

          document.getElementById('btnref').style.display = 'none';
          document.getElementById('btnsearch').style.display = '';
          
          e.preventDefault();
        	
        });

        $("#new").submit(function(e){
              document.getElementById('btnsubmit').style.display = 'none';
              document.getElementById('btnloading').style.display = '';
        });

    });
  </script>
@endsection
@extends('layout.newlayout')


@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Transaksi / Sales Order On Hold</h1>
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

    <div class="col-12 form-group row">
      <!--FORM Search Disini-->
      <label for="s_sonbr" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('SO Number') }}</label>
      <div class="col-md-4 col-sm-4 mb-2 input-group">
        
        <input id="s_sonbr" type="text" class="form-control"  name="s_sonbr" value="" autofocus autocomplete="off">
      </div>
      <label for="s_cust" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Customer') }}</label>
      <div class="col-md-4 col-sm-4 mb-2 input-group">
	<select id="s_cust" class="form-control" name="s_cust" autofocus autocomplete="off">
	  <option value=""> Select Data </option>
        @foreach($custsearch as $custsearch)
          <option value="{{$custsearch->cust_code}}">{{$custsearch->cust_code}} -- {{$custsearch->cust_alt_name}}</option>
        @endforeach 
    </select>
      </div>
      <label for="s_datefrom" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Date From') }}</label>
      <div class="col-md-4 col-sm-4 mb-2 input-group">
        <input id="s_datefrom" type="text" class="form-control" name="s_datefrom" value="" placeholder="YYYY-MM-DD" autofocus autocomplete="off" min="0">
      </div>
      <label for="s_dateto" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Date To') }}</label>
      <div class="col-md-4 col-sm-4 mb-2 input-group">
        <input id="s_dateto" type="text" class="form-control" name="s_dateto" value="" placeholder="YYYY-MM-DD" autofocus autocomplete="off" min="0">
      </div>
      <label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
      <div class="col-md-4 col-sm-4 mb-2 input-group">
        <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:right" />
      </div>
    </div>
  	<input type = "hidden" id ="sonumbertemp" name="sonumbertemp" value=""/>
    <input type = "hidden" id ="customertemp" name="customertemp" value=""/>
    <input type = "hidden" id ="datetotemp" name="datetotemp" value=""/>
    <input type = "hidden" id ="datefromtemp" name="datefromtemp" value=""/>

    <div class="col-md-12"><hr></div>
	  <div class="table-responsive col-12">
	      <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
	        <thead>
	          <tr>
	             <th>SO Number</th>
	             <th>Customer</th>  
	             <th>Due Date</th>
	             <th>Amount</th>
               	     <th>Next Approver</th>
	             <th width="7%">Action</th>
	          </tr>
	       </thead>
	        <tbody id='detail'>
	            @include('so.table-sohold')                   
	        </tbody>
	      </table>
	      <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
	      <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
	      <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
	  </div>
	  

	

    <!--Modal View-->
    <div class="modal fade" id="viewModal" role="dialog" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-center" id="exampleModalLabel">Detail SO</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
                <div class="modal-body">

                <form class="form-horizontal" method="POST" action="/approvehold" onkeydown="return event.key != 'Enter';">
                  {{ csrf_field() }}

                    <div class="form-group row col-md-12">
                        <label for="e_sonbr" class="col-md-3 col-form-label text-md-right">SO Number</label>
                        <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
                            <input id="e_sonbr" type="text" class="form-control" name="e_sonbr" autocomplete="off" maxlength="24" autofocus readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="e_custdesc" class="col-md-3 col-form-label text-md-right">Customer</label>
                        <div class="col-md-6">
                            <input id="e_custdesc" type="text" class="form-control" name="e_custdesc" autocomplete="off" maxlength="24" autofocus readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="e_duedate" class="col-md-3 col-form-label text-md-right">Due Date</label>
                        <div class="col-md-6">
                          <input id="e_duedate" type="text" class="form-control" name="e_duedate" autocomplete="off" maxlength="24" autofocus placeholder="yy-mm-dd" required readonly>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="e_amt" class="col-md-3 col-form-label text-md-right">Ammount</label>
                        <div class="col-md-6">
                            <input id="e_amt" type="text" class="form-control" name="e_amt" autocomplete="off" readonly>
                        </div>
                    </div>

               
                    <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

                    <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
                        <thead>
                            <tr id='full'>
                                <th style="width:30%">Item</th>
                                <th style="width:15%">Qty</th>
                                <th style="width:15%">UM</th>
                                <th style="width:15%">Loc</th>
                            </tr>
                        </thead>
                        <tbody id='e_detailapp'>
                        </tbody>
                    </table>


                    <div class="form-group row col-md-12">
                        <label for="e_reason" class="col-md-3 col-form-label text-md-right">Reason</label>
                        <div class="col-md-8">
                            <input id="e_reason" type="text" class="form-control" name="e_reason" autocomplete="off">
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
		    <input type="hidden" name="nextapp" id="nextapp">
         	    <input type="hidden" name="nextorder" id="nextorder">
        	    <input type="hidden" name="session" id="session"/>
                    <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info bt-action" id="btnreject" onclick="return (confirm('Reject SO ?'));" name="action" value="reject" >Reject</button>
                    <button type="submit" class="btn btn-info bt-action" id="btnapprove" onclick="return (confirm('Approve SO ?'));" name="action" value="confirm" >Approve</button>
                </div>

              </form>
        </div>
      </div>  
    </div>
@endsection


@section('scripts')
  <script type="text/javascript">
    $(document).ready(function () {
		
	$('#s_cust').select2({
	   width : '100%'	
	});
        var counter = 0;

        function selectRefresh() {
          $('.barang').select2({
            tags: true,
            placeholder: "Select an Option",
            width: '100%'
          });
        }
        $("#s_datefrom").datepicker({
          dateFormat : 'yy-mm-dd'
        });

        $("#s_dateto").datepicker({
          dateFormat : 'yy-mm-dd'
        });

         function fetch_data(page, sort_type, sort_by, sonbr, cust, datefrom, dateto) {
          $.ajax({
            url: "/sooh/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&sonbr=" + sonbr + "&cust=" + cust + "&datefrom=" + datefrom 
            + "&dateto=" + dateto ,
            success: function(data) {
              console.log(data);
              $('#detail').html('');
              $('#detail').html(data);
            }
          })
        }

        $(document).on('click', '#btnsearch', function() {
          var sonbr = $('#s_sonbr').val(); 
          var cust = $('#s_cust').val(); 
          var datefrom = $('#s_datefrom').val();
          var dateto = $('#s_dateto').val(); 

          var column_name = $('#hidden_column_name').val();
          var sort_type = $('#hidden_sort_type').val();
          var page = 1;

          document.getElementById('sonumbertemp').value = sonbr;
          document.getElementById('customertemp').value = cust;
          document.getElementById('datefromtemp').value = datefrom;
          document.getElementById('datetotemp').value = dateto;

          fetch_data(page, sort_type, column_name, sonbr, cust, datefrom, dateto);
        });

        $(document).on('click', '.pagination a', function(event) {
          event.preventDefault();
          var page = $(this).attr('href').split('page=')[1];
          $('#hidden_page').val(page);
          var column_name = $('#hidden_column_name').val();
          var sort_type = $('#hidden_sort_type').val();

          var sonbr = $('#sonumbertemp').val(); 
          var cust = $('#customertemp').val(); 
          var datefrom = $('#datefromtemp').val();
          var dateto = $('#datetotemp').val(); 

          fetch_data(page, sort_type, column_name, sonbr, cust, datefrom, dateto);

        });

        $(document).on('click', '.editmodal', function() {
	    var session = $(this).data('session'); 
            var sonbr = $(this).data('sonbr');
            var cust = $(this).data('desc');
            var duedate = $(this).data('duedate');
            var ammount = $(this).data('ammount');
            var reason = $(this).data('reason');
            var nextapp = $(this).data('nextapp');
            var nextorder = $(this).data('nextorder');

	    if(session != nextapp){
                       document.getElementById('btnreject').style.display = 'none';
                       document.getElementById('btnapprove').style.display = 'none';
		       document.getElementById('e_reason').readOnly = true;
            }else{
                      document.getElementById('btnreject').style.display = '';
                      document.getElementById('btnapprove').style.display = '';
                      document.getElementById('e_reason').readOnly = false;
       	    }

	   
            document.getElementById('e_sonbr').value = sonbr;
            document.getElementById('e_custdesc').value = cust;
            document.getElementById('e_duedate').value = duedate;
            document.getElementById('e_amt').value = ammount;
            document.getElementById('nextapp').value = nextapp;
            document.getElementById('nextorder').value = nextorder;
	    document.getElementById('session').value = session;

            $.ajax({
                url:"/detailsales",
                data:{
                  sonbr : sonbr,
                },
                success:function(data){
                    console.log(data);
                    $('#e_detailapp').html('');
                    $('#e_detailapp').html(data);

                }
            })

        });


        $("#new").submit(function(e){
              document.getElementById('btnsubmit').style.display = 'none';
              document.getElementById('btnloading').style.display = '';
        });

        $("#btnapprove").on('click',function(e){
            $("#e_reason").removeAttr('required');
        });

        $("#btnreject").on('click',function(e){
            $("#e_reason").attr('required',"");
        });

    });
  </script>
@endsection

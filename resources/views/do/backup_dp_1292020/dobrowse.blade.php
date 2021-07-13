@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Browse SPB</h1>
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

     <div class="form-group row col-md-12">
  <label for="s_spbnumber" class="col-md-2 col-form-label text-md-right">{{ __('SPB Number.') }}</label>
  <div class="col-md-3">
    <input id="s_spbnumber" type="text" class="form-control" name="s_spbnumber" value="" autofocus autocomplete="off">
  </div>
  <label for="s_customer" class="col-md-2 col-form-label text-md-right">{{ __('Customer') }}</label>
  <div class="col-md-3">
    <input id="s_customer" type="text" class="form-control" name="s_customer" value="" autofocus autocomplete="off">
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="datefrom" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Delivery Date From') }}</label>
  <div class="col-md-4 col-lg-3">
    <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="YYYY-MM-DD" required autofocus autocomplete="off">
  </div>
  <label for="dateto" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Delivery Date To') }}</label>
  <div class="col-md-4 col-lg-3">
    <input type="text" id="dateto" class="form-control" name='dateto' placeholder="YYYY-MM-DD" required autofocus autocomplete="off">
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="s_status" class="col-md-2 col-form-label text-md-right">{{ __('Status') }}</label>
  <div class="col-md-3">
    <select id="s_status" class="form-control" name="s_status" autofocus autocomplete="off">
      <option value=""> --Select Status-- </option>
      <option value="1">Open</option>
      <option value="2">Confirm</option>
      <option value="3">Delete</option>
    </select>
    
  </div>

  <label for="s_status" class="col-md-3 col-form-label text-md-right">{{ __('') }}</label>
  <div class="offset-0">
    <input type="button" class="btn bt-action newUser" id="btnsearch" value="Search" />
    <button class="btn bt-action seconddata newUser" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-refresh"></i></button>
  
  </div>
</div>

<div class="col-12"><hr></div>


      <div class="table-responsive col-12">
          <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
            <thead>
              <tr>
                 <th>SPB Number</span></th>
                 <th>Customer</span></th>  
                 <th>Delivery Date</th>
                 <th>Status</th>
                 <th width="18%">Action</th>
              </tr>
           </thead>
            <tbody>
                @include('do.table-dobrowse')                   
            </tbody>
          </table>
          <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
          <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="so_nbr" />
          <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
      </div>




      <!--Modal View-->
      <div class="modal fade" id="viewModal" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-center" id="exampleModalLabel">Detail Surat Permintaan Barang</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="form-group row col-md-12">
                    <label for="e_custcode" class="col-md-2 col-form-label text-md-right">Customer</label>
                    <div class="col-md-4 {{ $errors->has('uname') ? 'has-error' : '' }}">
                        <input id="e_custcode" type="text" class="form-control" name="e_custcode" value="{{ old('e_custcode') }}" autocomplete="off" maxlength="24" autofocus readonly>
                    </div>
                    <label for="e_address" class="col-md-2 col-form-label text-md-right">Address</label>
                    <div class="col-md-4">
                        <input id="e_address" type="text" class="form-control" name="e_address" value="{{ old('e_address') }}" autocomplete="off" maxlength="24" autofocus readonly>
                    </div>
                </div>
                <div class="form-group row col-md-12">
                    <label for="e_nbr" class="col-md-2 col-form-label text-md-right">SPB Number</label>
                    <div class="col-md-4">
                        <input id="e_nbr" type="text" class="form-control font-weight-bold" name="e_nbr" value="{{ old('e_nbr') }}" autocomplete="off" maxlength="24" autofocus readonly>
                    </div>
                    <label for="e_duedate" class="col-md-2 col-form-label text-md-right">SPB Date</label>
                    <div class="col-md-4">
                      <input id="e_duedate" type="text" class="form-control" name="e_duedate" value="{{ old('e_dodate') }}" autocomplete="off" maxlength="24" autofocus placeholder="yy-mm-dd" required readonly>
                    </div>
                </div>
                <div class="form-group row col-md-12">
                    <label for="e_shipto" class="col-md-2 col-form-label text-md-right">Ship To</label>
                    <div class="col-md-4">
                        <input id="e_shipto" type="text" class="form-control" name="e_shipto" value="{{ old('e_shipto') }}" autocomplete="off" readonly>
                    </div>
                    <label for="e_note" class="col-md-2 col-form-label text-md-right">Notes</label>
                    <div class="col-md-4">
                        <input id="e_note" type="text" class="form-control" name="e_note" value="{{ old('e_note') }}" autocomplete="off" readonly>
                    </div>
                </div>

                <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

                <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
                    <thead>
                        <tr id='full'>
                            <th style="width:15%">SO Nbr</th>
                            <th style="width:10%">Line</th>
                            <th style="width:30%">Item</th>
                            <th style="width:15%">Qty</th>
                            <th style="width:15%">UM</th>
                        </tr>
                    </thead>
                    <tbody id='e_detailapp'>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>  
      </div>


<!--Modal Delete-->
  <div class="modal fade" id="deleteModal" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-center" id="exampleModalLabel">Delete SPB</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

         <form class="form-horizontal" method="post" action="dodelete">
              {{ csrf_field() }}

              <div class="modal-body">
                  <input type="hidden" name="donbr" id="donbr">
                  Anda yakin ingin menghapus no SPB <b> <span id="d_donbr"></span> </b> ?
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success bt-action" id="d_btnconf">Confirm</button>
                <button type="button" class="btn bt-action" id="d_btnloading" style="display:none">
                  <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                </button>
              </div>
         </form>
      </div>
    </div>
  </div>

  <!-- modal accept -->
<div class="modal fade" id="confirmodal" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">Accept Shipment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="form-horizontal" method="post" action="soconfirm">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <span for="e_suratjalan" value="" class="col-md-3 col-form-label text-md-right">Surat Jalan</span>
                        <div class="col-md-4 {{ $errors->has('uname') ? 'has-error' : '' }}">
                            <input id="e_suratjalan" type="text" class="form-control" name="e_suratjalan" value="{{ old('e_suratjalan') }}" autocomplete="off" maxlength="6" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="e_custname" class="col-md-3 col-form-label text-md-right">Cust Name</label>
                        <div class="col-md-4">
                            <input id="e_custname" type="text" class="form-control" name="e_custname" value="{{ old('e_custname') }}" autocomplete="off" maxlength="24" readonly autofocus required>
                        </div>
                    </div>
                    <div class="table-responsive form-group row">
                        <div class="col-md-12">
                            <table class="table" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="10%">SO</th>
                                        <th width="10%">Item code</th>
                                        <th width="10%">Item Desc</th>
                                        <th width="10%">Shipto</th>
                                        <th width="10%">Quantity</th>
                                        <th width="10%">Price</th>
                                        <th width="10%">Due date</th>
                                        <th width="10%">Order date</th>
                                    </tr>
                                </thead>
                                <tbody id="testtable">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Confirm</button>
                    <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
                        <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection


@section('scripts')
  <script type="text/javascript">
    $(function() {
    $('#datefrom').datepicker({
      dateFormat: 'yy-mm-dd'
    });
    $('#dateto').datepicker({
      dateFormat: 'yy-mm-dd'
    });
    });

    $("#dodate").datepicker({
      dateFormat : 'yy-mm-dd'
    });

    $(document).ready(function () {
        var counter = 0;

        $("#custcode").select2({
          width : '100%'
        });

        function selectRefresh() {
          
          $('.sodrop').select2({
            tags: true,
            placeholder: "Select an Option",
            width: '100%'
          });


        }


        function fetch_data(page, sort_type, sort_by, spbnumber, customer, status, dlvdatefrom, dlvdateto)
        {
            $.ajax({
             url:"/dobrowse/pagination?page="+page+"&sorttype="+sort_type+"&sortby="+sort_by+"&spbnumber="+spbnumber+"&customer="+customer+"&status="+status+"&dlvdatefrom="+dlvdatefrom+"&dlvdateto="+dlvdateto,
             success:function(data)
             {
              console.log(data);
              $('tbody').html('');
              $('tbody').html(data);
             }
            })
        }
	
	$('#btnrefresh').on('click',function(){
      
      
	  var spbnumber = '';
          var customer = '';
	  var status = '';
	  var dlvdatefrom = '';
	  var dlvdateto = '';
          

            document.getElementById("s_customer").value = '' ;
            document.getElementById("s_spbnumber").value = '';
            document.getElementById("datefrom").value = '';
            document.getElementById("dateto").value = '';
	    document.getElementById("s_status").value = '';
            var page = $("#hidden_page").val();
            var sort_type= $("#hidden_sort_type").val();
            var column_name = $("#hidden_column_name").val();

	fetch_data(page, sort_type, column_name, spbnumber, customer, status, dlvdatefrom, dlvdateto);
      
      });


	$(document).on('click', '#btnsearch', function() {
		var spbnumber = $('#s_spbnumber').val();
		var customer = $('#s_customer').val();
		var status = $('#s_status').val();
		var dlvdatefrom = $('#datefrom').val();
		var dlvdateto = $('#dateto').val();
		var column_name = $('#hidden_column_name').val();
		var sort_type = $('#hidden_sort_type').val();
		var page = $('#hidden_page').val();

	        fetch_data(page, sort_type, column_name, spbnumber, customer, status, dlvdatefrom, dlvdateto);
        });

        $(document).on('click', '.pagination a', function(event){
          event.preventDefault();
          var page = $(this).attr('href').split('page=')[1];
          $('#hidden_page').val(page);
	  var spbnumber = $('#s_spbnumber').val();
          var customer = $('#s_customer').val();
	  var status = $('#s_status').val();
	  var dlvdatefrom = $('#datefrom').val();
	  var dlvdateto = $('#dateto').val();
          var column_name = $('#hidden_column_name').val();
          var sort_type = $('#hidden_sort_type').val();

          fetch_data(page, sort_type, column_name, spbnumber, customer, status, dlvdatefrom, dlvdateto);
        });

        $("table.order-list").on("change", ".sodrop", function (e) {
            var nbr = document.getElementById('sodrop').value;
            var temp = $(this).closest("tr").find('.barang');

            $.ajax({
              url:"/searchitem",
              data:{
                nbr : nbr,
              },
              success:function(data){
                  console.log(data);

                  temp.html('').append(data);
              }
            })
        })


        $("table.order-list").on("change", ".barang", function (e) {
            var nbr = document.getElementById('sodrop').value;
            var item = document.getElementById('barang').value;

            $.ajax({
              url:"/searchqty",
              data:{
                nbr : nbr,
                item : item,
              },
              success:function(data){
                  console.log(data);
                  alert(nbr);
                  document.getElementById('qtyso').value = data;
              }
            })
        })

        $(".newUser").on("click",function(){
            $('#shipto').focus();
            //$("#custcode").select2('open');
        });

        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
        });

        $("#custcode").on("change",function(e){

            var cust = document.getElementById('custcode').value;
            
            $.ajax({
              url:"/alamatsearch",
              data:{
                cust : cust,
              },
              success:function(data){
                  console.log(data);
                  document.getElementById('address').value = data;
              }
            })

             $.ajax({
              url:"/lastdo",
              success:function(data){
                  console.log(data);
                  document.getElementById('lastdonbr').value = "DO" + data.padStart(6, "0");
              }
            })
            
        });

       $(document).on('click', '.viewmodal', function() {
          var cust      = $(this).data('cust');
          var nbr       = $(this).data('nbr');
          var desc      = $(this).data('custdesc');
          var alamat    = $(this).data('alamat');
          var duedate   = $(this).data('dodate');
          var shipto    = $(this).data('shipto');
          var note      = $(this).data('note');

          document.getElementById('e_custcode').value = cust + ' - ' + desc;
          document.getElementById('e_address').value = alamat;  
          document.getElementById('e_duedate').value = duedate;
          document.getElementById('e_shipto').value = shipto;
          document.getElementById('e_nbr').value = nbr;
          document.getElementById('e_note').value = note;
          //alert('tyas');
          $.ajax({
              url:"/detaildo",
              data:{
                nbr : nbr,
              },
              success:function(data){
                  console.log(data);
                  $('#e_detailapp').html('');
                  $('#e_detailapp').html(data);
              }
          })
        });

        $("#btnconf").on("click",function(e){
              document.getElementById('btnclosem').style.display = 'none';
              document.getElementById('btnconf').style.display = 'none';
              document.getElementById('btnloading').style.display = '';
        });

    });

    $(document).on('click','.deletemodal',function(){
       
       var donbr = $(this).data('donbr');

       document.getElementById('d_donbr').innerHTML = donbr;   
       document.getElementById('donbr').value = donbr;   

       // flag tunggu semua menu
    });

    $(document).on('click', '.soshipmentgetinfo', function() {

      var suratjalan = $(this).data('do_nbr');
      var custcode = $(this).data('do_cust');
      var custname = $(this).data('do_custname');
      document.getElementById('e_suratjalan').value = suratjalan;
      document.getElementById('e_custname').value = custcode.concat(" --- ", custname);

      $.ajax({
          type: "get",
          url: "{{URL::to("soshipmentgetinfo")}}",
          data: {
              search: suratjalan,
          },
          success: function(data) {
              $('#testtable').html(data);

          }
      })
    });

    
  </script>
@endsection

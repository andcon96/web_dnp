@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Create SPB</h1>
            </div>
        </div>
    </div>
@endsection
@section('content')

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
  $( function() {
      $( "#datefrom" ).datepicker({
          dateFormat : 'dd/mm/yy'
      });
      $( "#dateto" ).datepicker({
          dateFormat : 'dd/mm/yy'
      });
  });
</script>



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



    <div class="form-group row">
      
	     <label for="socode" class="col-md-2 col-lg-2 col-form-label">{{ __('SO Number') }}</label>
          <div class="col-md-4 col-lg-3">
              <input id="socode" type="text" class="form-control" name="socode" 
              value="" autofocus>
          </div>
      <label for="pocode" class="col-md-2 col-lg-2 col-form-label">{{ __('PO Number') }}</label>
          <div class="col-md-4 col-lg-3">
              <input id="pocode" type="text" class="form-control" name="pocode" 
              value="" autofocus>
          </div>    
          
    </div>
    <div class="form-group row">
      <label for="custcode" class="col-md-2 col-lg-2 col-form-label">{{ __('Customer Code') }}</label>
          <div class="col-md-4 col-lg-3">
              <input id="custcode" type="text" class="form-control" name="custcode" 
              value="" autofocus>
          </div>
	<label for="shipto" class="col-md-2 col-lg-2 col-form-label">{{ __('Ship To Code') }}</label>
          <div class="col-md-4 col-lg-3">
              <input id="shipto" type="text" class="form-control" name="shipto" 
              value="" autofocus>
          </div>
          
    </div>

    <div class="form-group row">
      <label for="datefrom" class="col-md-2 col-lg-2 col-form-label">{{ __('Sales Order Date From') }}</label>
          <div class="col-md-4 col-lg-3">
              <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                      required autofocus autocomplete="off">
          </div>
	<label for="dateto" class="col-md-2 col-lg-2 col-form-label">{{ __('Date To') }}</label>
          <div class="col-md-4 col-lg-3">
              <input type="text" id="dateto" class="form-control"  name='dateto' placeholder="DD/MM/YYYY"
                      required autofocus autocomplete="off">
          </div>
	<label for="dateto" class="col-md-4 col-lg-2 col-form-label">{{ __('') }}</label>
          <div class="col-md-2 offset-md-2 offset-lg-0  d-flex">
            <input type="button" class="btn bt-action seconddata" id="btnsearch" value="Search" />
            <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-refresh"></i></button>
          </div>

    </div>

   <div class="col-12"><hr></div>
   <input type="hidden" id="potemp" value=""/>
<input type="hidden" id="sotemp" value=""/>
<input type="hidden" id="custcodetemp" value=""/>
<input type="hidden" id="shiptodesctemp" value=""/>
<input type = "hidden" id ="datefromtemp" name="datetotemp" value=""/>
<input type = "hidden" id ="datetotemp" name="datefromtemp" value=""/>
    <!--Table-->
    <form action="/createdoTemp" method="post">
      {{ csrf_field() }}
      <div class="table-responsive tag-container" style="overflow-x: auto; display: block;white-space: nowrap;">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
          <thead>
            <tr>
               <th style="width:5%; text-align:center;"></th>
               <th>SO Number</th>
               <th>PO Number</th>
	             <th>Customer</th>
	             <th>Ship To</th>  
               <th>SO Date</th>
            </tr>
         </thead>
         <tbody>   
            
            @include('do.docreate-view')
          </tbody>
        </table>
      </div>
      <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
      <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
      <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
      @if(!str_contains( Session::get('menu_access'),'TS10'))
      <button type="submit" class="btn bt-action" name='action' value="confirm" id='btnconf'>CREATE</button>
      @endif
    </form>

@endsection


@section('scripts')
  <script type="text/javascript">
    
      function fetch_data(page, sort_type, sort_by,pocode,socode, code, shipto, datefrom, dateto)
      {
          $.ajax({
           url:"/docreate/pagination?page="+page+"&sorttype="+sort_type+"&sortby="+sort_by+"&pocode="+pocode+"&socode="+socode+"&code="+code+"&shipto="+shipto+"&datefrom="+datefrom+"&dateto="+dateto,
           success:function(data)
           {
            console.log(data);
            $('tbody').html('');
            $('tbody').html(data);
           }
          })
      }

      $(document).on('click', '.pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var code      = $("#custcodetemp").val();
        var shipto    = $("#shiptodesctemp").val();
        var datefrom  = $("#datefromtemp").val();
        var dateto    = $("#datetotemp").val();
	      var socode    = $("#sotemp").val();
        var pocode    = $("#potemp").val();

        fetch_data(page, sort_type, column_name, pocode, socode, code, shipto, datefrom, dateto);
      });

      $(document).on('click','#btnsearch',function(){
        var pocode    = $("#pocode").val();     
	      var socode    = $("#socode").val();  
        var code      = $("#custcode").val();
        var shipto    = $("#shipto").val();
        var datefrom  = $("#datefrom").val();
        var dateto    = $("#dateto").val();
        var page = 1;
        var sort_type= $("#hidden_sort_type").val();
        var column_name = $("#hidden_column_name").val();
        document.getElementById('potemp').value = pocode;
	      document.getElementById('sotemp').value = socode;
	      document.getElementById('custcodetemp').value = code;
        document.getElementById('shiptodesctemp').value = shipto;
        document.getElementById('datefromtemp').value = datefrom;
        document.getElementById('datetotemp').value = dateto;


        fetch_data(page, sort_type, column_name,pocode, socode, code, shipto, datefrom, dateto);
        
      });

      $('#btnrefresh').on('click',function(){
      
      var code      = '';
      var shipto    = '';
      var datefrom  = '';
      var dateto    = '';
      var socode    = '';
      var pocode    = '';

            document.getElementById("custcode").value = '';
            document.getElementById("socode").value = '';
            document.getElementById("shipto").value = '';
            document.getElementById("datefrom").value = '';
            document.getElementById("dateto").value = '';
            var hiddenpage = $("#hidden_page").val();
            var sorttype= $("#hidden_sort_type").val();
            var sortby = $("#hidden_column_name").val();
	          document.getElementById('sotemp').value = '';
	          document.getElementById('custcodetemp').value = '';
            document.getElementById('shiptodesctemp').value = '';
            document.getElementById('datefromtemp').value = '';
            document.getElementById('datetotemp').value = '';
	          document.getElementById('potemp').value = '';
	
        fetch_data(hiddenpage,sorttype,sortby,pocode,socode,code,shipto,datefrom,dateto);
      
      });

  </script>
@endsection

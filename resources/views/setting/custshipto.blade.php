@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Customer ship to</h1>
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

 <div class="col-md-2 col-sm-4 mb-3 offset-md-2 offset-lg-0">
        <form action="/custshiptoload" method="POST">
        @csrf
            <input type="submit" class="btn bt-action" data-toggle="modal" data-target="#loadingtable" id="btnrefresh" value="Load Table"  style="float:right" />
        </form>
  </div>
<div class="col-12"><hr></div>
  <div class="col-12 form-group row">
      <!--Search Disini-->
      <label for="s_custcode" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Customer Code') }}</label>
      <div class="col-md-4 col-sm-4 mb-2 input-group">
          <input id="s_custcode" type="text" class="form-control" name="s_custcode" 
          value="" autofocus autocomplete="off">
      </div>
      <label for="s_shipto" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Ship To') }}</label>
      <div class="col-md-4 col-sm-4 mb-2 input-group">
          <input id="s_shipto" type="text" class="form-control" name="s_shipto" 
          value="" autofocus autocomplete="off" min="0">
      </div>
  </div>
  <div class="col-12 form-group row">
      <div class="col-md-8">
      </div>
      <div class="col-md-2 col-sm-4 offset-md-2 offset-lg-0">
          <input type="button" class="btn bt-action" 
          id="btnsearch" value="Search"/>
      </div>
  </div>

  <input type="hidden" id="custcodetemp" value=""/>
  <input type="hidden" id="shiptotemp" value=""/>
  <div class="col-md-12"><hr></div>
  <div class="table-responsive col-12" style="overflow: auto; display: block;white-space: nowrap; margin-left: auto; margin-right: auto;">
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
             <th class="sorting" data-sorting_type="asc" data-column_name= "cust_code" style="cursor: pointer" width="20%">Customer Code<span id="name_icon"></span></th>
             <th class="sorting" data-sorting_type="asc" data-column_name= "shipto" style="cursor: pointer" width="20%">Ship To</th>
	     <th class="sorting" data-sorting_type="asc" data-column_name= "alamat" style="cursor: pointer" width="60%">Address</th>  
          </tr>
       </thead>
        <tbody>
             @include('setting.table-custshipto')              
        </tbody>
      </table>
      <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
      <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="custcodeship" />
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
    
    function clear_icon() {
        $('#id_icon').html('');
        $('#post_title_icon').html('');
    }

    function fetch_data(page, sort_type, sort_by, custcode, shipto) {
        $.ajax({
            url: "/custshipto/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&custcode=" + custcode + "&shipto=" + shipto, 
            success: function(data) {
                console.log(data);
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }


    //fungsi search
    $(document).on('click', '#btnsearch', function() {
        var custcode = $('#s_custcode').val(); 
        var shipto = $('#s_shipto').val();
	
	document.getElementById('custcodetemp').value = custcode;
	document.getElementById('shiptotemp').value = shipto;
	  
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = 1;
	//alert(page);
	document.getElementById("")
        fetch_data(page, sort_type, column_name, custcode, shipto);
    });

    //fungsi sorting
    $(document).on('click', '.sorting', function() {
        var column_name = $(this).data('column_name');
        var order_type = $(this).data('sorting_type');
        var reverse_order = '';
        if (order_type == 'asc') {
            $(this).data('sorting_type', 'desc');
            reverse_order = 'desc';
            clear_icon();
            $('#' + column_name + '_icon').html('<span class="glyphicon glyphicon-triangle-bottom"></span>');
        }
        if (order_type == 'desc') {
            $(this).data('sorting_type', 'asc');
            reverse_order = 'asc';
            clear_icon();
            $('#' + column_name + '_icon').html('<span class="glyphicon glyphicon-triangle-top"></span>');
        }
        $('#hidden_column_name').val(column_name);
        $('#hidden_sort_type').val(reverse_order);
        var page = $('#hidden_page').val();
        var custcode = $('#s_custcode').val(); 
        var shipto = $('#s_shipto').val(); 
        fetch_data(page, reverse_order, column_name, custcode, shipto);
    });

    //fungsi paginate
    $(document).on('click', '.pagination a', function(event) {
        
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();

        var custcode = document.getElementById('custcodetemp').value; 
        var shipto = document.getElementById('shiptotemp').value;

	fetch_data(page, sort_type, column_name, custcode, shipto);

	event.preventDefault();        
	
    });
</script>
@endsection

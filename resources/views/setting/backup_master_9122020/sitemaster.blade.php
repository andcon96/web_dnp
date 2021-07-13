@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Site Master</h1>
            </div>
        </div>
    </div>
@endsection
@section('content')
<style type="text/css">
  @media screen and (max-width: 992px) {

    .mini-table {
      border: 0;
    }

    .mini-table thead {
      display: none;
    }

    .mini-table tr {
      margin-bottom: 10px;
      display: block;
      border-bottom: 2px solid #ddd;
    }

    .mini-table td {
      display: block;
      text-align: right;
      font-size: 13px;
      border-bottom: 1px dotted #ccc;
    }

    .mini-table td:last-child {
      border-bottom: 0;
    }

    .mini-table td:before {
      content: attr(data-label);
      float: left;
      text-transform: uppercase;
      font-weight: bold;
    }
  }
</style>


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

<div class="col-md-2 col-sm-2 mb-3 ml-5 input-group">
  <form action="/reloadtabelsite" method="POST">
      @csrf
          <input type="submit" class="btn bt-action" data-toggle="modal" data-target="#loadingtable" data-backdrop="static" data-keyboard="false"  
          id="btnrefresh" value="Load Table"  style="float:right" />
      </form>
  </div>

<div class="col-12"><hr></div>

<!-- tablesearch -->
<div class="col-12 form-group row">
  <!--FORM Search Disini -->
  <label for="s_username" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Site Code') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_sitecode" type="text" class="form-control" name="s_sitecode" value="" autofocus autocomplete="off">
  </div>
  <label for="s_warehouse" class="col-md-1 col-sm-2 col-form-label text-md-right">{{ __('Warehouse') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <select id="s_warehouse" class="form-control" name="s_warehouse">
      <option value="">Pilih Warehouse</option>
      <option value="Y">Ada</option>
      <option value="N">Tidak</option>
    </select>
    
  </div>
  <label for="s_name" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Site Desc') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_sitedesc" type="text" class="form-control" name="s_sitedesc" value="" autofocus autocomplete="off" min="0">
  </div>

  <label for="" class="col-md-1 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
  <div class="col-md-2 col-sm-2 mb-2 input-group">
    <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:right" />
  </div>
  
  
</div>
<div class="col-md-12"><hr></div>
<!--Table Menu-->
<div class="table-responsive offset-lg-1 col-lg-10 col-md-12">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th width=23% class ="sorting" data-sorting_type="asc" data-column_name="site_code" style="cursor: pointer" >Site Code</th>
        <th width=23% class ="sorting" data-sorting_type="asc" data-column_name="site_desc" style="cursor: pointer">Site Description</th>
        <th width=23% class ="sorting" data-sorting_type="asc" data-column_name="site_flag" style="cursor: pointer">Warehouse</th>
        <th width=21% class ="sorting" data-sorting_type="asc" data-column_name="pusat_cabang" style="cursor: pointer">Pusat/Cabang</th>
        <th width="10%">Action</th>
      </tr>
    </thead>
    <tbody>
    @include('setting.table-sitemaster')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="site_code" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>

<!-- edit site -->
<div class="modal fade" id="editsitee" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Site</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="POST" id='new' action="editsite" onkeydown="return event.key != 'Enter';">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group row">
            <label for="site_code" class="col-md-5 col-form-label text-md-right">Site Code</label>
            <div class="col-md-7">
              <input id="e_sitecode" type="text" class="form-control" name="e_sitecode" value="{{ old('e_sitecode') }}" autocomplete="off" maxlength="50" readonly autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="site_desc" class="col-md-5 col-form-label text-md-right">Site Description</label>
            <div class="col-md-7">
              <input id="e_sitedesc" type="text" class="form-control" name="e_sitedesc" value="{{ old('e_sitedesc') }}" autocomplete="off" maxlength="50" readonly autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_Site" class="col-md-5 col-form-label text-md-right">Warehouse</label>
            <div class="col-md-7">              
              <select id="e_siteflag" class="form-control" name="e_siteflag">
                <option value="">Pilih</option>
                <option value="Y">Ada</option>
                <option value="N">Tidak</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_pusatcabang" class="col-md-5 col-form-label text-md-right">Pusat/Cabang</label>
            <div class="col-md-7">        
              <select id="e_pusatcabang" class="form-control" name="e_pusatcabang">
                <option value=0>Cabang</option>
                <option value=1>Pusat</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Save</button>
            <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
              <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
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
<script type="text/javascript">

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

  $(".newUser").on("click", function() {

    $.ajax({
      url: "/sitesearch",
      success: function(data) {
        $('#detailapp').html('');
        $('#detailapp').html(data);
      }
    });
  });

  function clear_icon() {
    $('#id_icon').html('');
    $('#post_title_icon').html('');
  }
  
  $("table.order-list").on("click", ".ibtnDel", function(event) {
    $(this).closest("tr").remove();
    counter -= 1
  });


  $("#edit").submit(function() {
    document.getElementById('e_btnclose').style.display = 'none';
    document.getElementById('e_btnconf').style.display = 'none';
    document.getElementById('e_btnloading').style.display = '';
  });



  $(document).on('click', '.editsite', function() {

    var sitecode2 = $(this).data('sitecode');
    var sitedesc2 = $(this).data('sitedesc');
    var flagcheck = $(this).data('siteflag');
    var pusatcabang = $(this).data('pusatcabang');
    var testdata = $(this).data('pusatcabang2');
    var testdata2 = $(this).data('pusatcabang3');
    document.getElementById('e_sitecode').value = sitecode2;
    document.getElementById('e_sitedesc').value = sitedesc2;
    document.getElementById('e_siteflag').value = flagcheck;
    document.getElementById('e_pusatcabang').value = pusatcabang;
    
    if(sitecode2.trim() === testdata.trim() && testdata2==="notnull"){
      document.getElementById("e_pusatcabang").removeAttribute('disabled');
    }
    else if(sitecode2.trim() !== testdata.trim() && testdata2==="notnull"){
      document.getElementById("e_pusatcabang").setAttribute('disabled','disabled');
    }
    else if(testdata2 === "allnull"){
      document.getElementById("e_pusatcabang").removeAttribute('disabled');
    }
  });

  function fetch_data(page, sort_type, sort_by, sitecode, sitedesc, warehouse) {
    $.ajax({
      url: "/sitemaster/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&sitecode=" + sitecode + "&sitedesc=" + sitedesc + "&warehouse=" + warehouse,
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      }
    })
  }


  
  $(document).on('click', '#btnsearch', function() {
    var sitecode = $('#s_sitecode').val(); 
    var sitedesc = $('#s_sitedesc').val(); 
    var warehouse = $('#s_warehouse').val(); 
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = 1;

    fetch_data(page, sort_type, column_name, sitecode, sitedesc,warehouse);
  });

  
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
    var sitecode = $('#s_sitecode').val(); 
    var sitedesc = $('#s_sitedesc').val(); 
    var warehouse = $('#s_warehouse').val(); 
    fetch_data(page, reverse_order, column_name, sitecode, sitedesc,warehouse);
  });

  
  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();

    var sitecode = $('#s_sitecode').val(); 
    var sitedesc = $('#s_sitedesc').val(); 
    var warehouse = $('#s_warehouse').val(); 
    fetch_data(page, sort_type, column_name, sitecode, sitedesc, warehouse);

  });

 
</script>

@endsection

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
  <form action="/loadloc" method="POST">
      @csrf
          <input type="submit" class="btn bt-action" data-toggle="modal" data-target="#loadingtable" data-backdrop="static" data-keyboard="false"  
          id="btnrefresh" value="Load Table"  style="float:right" />
      </form>
  </div>

<div class="col-12"><hr></div>

<!-- tablesearch -->
<div class="col-12 form-group row">
  <!--FORM Search Disini -->
  <label for="s_siteloc" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Site Code') }}</label>
  <div class="col-md-3 col-sm-4 mb-2 input-group">
    <input id="s_siteloc" type="text" class="form-control" name="s_siteloc" value="" autofocus autocomplete="off">
  </div>
  <label for="s_locloc" class="col-md-3 col-sm-2 col-form-label text-md-right">{{ __('Location') }}</label>
  <div class="col-md-3 col-sm-4 mb-2 input-group">
    <input id="s_locloc" type="text" class="form-control" name="s_locloc" value="" autofocus autocomplete="off">
  </div>

  <label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
  <div class="col-md-2 col-sm-2 mb-2 input-group">
    <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:right" />
  </div>
  
  
</div>
<div class="col-md-12"><hr></div>
<input type="hidden" id="sitecodetemp" value=""/>
<input type="hidden" id="sitedesctemp" value=""/>


<!--Table Menu-->
<div class="table-responsive offset-lg-1 col-lg-10 col-md-12">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th width=23% class ="sorting" data-sorting_type="asc" data-column_name="id" style="cursor: pointer" >No</th>
        <th width=23% class ="sorting" data-sorting_type="asc" data-column_name="loc_site" style="cursor: pointer" >Site</th>
        <th width=23% class ="sorting" data-sorting_type="asc" data-column_name="loc_loc" style="cursor: pointer">Location</th>
      </tr>
    </thead>
    <tbody>
    @include('setting.table-locmaster')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="loc_site" />
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

  function fetch_data(page, sort_type, sort_by, locsite, locloc) {
    $.ajax({
      url: "/locmaster/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&locsite=" + locsite + "&locloc=" + locloc,
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      }
    })
  }


  
  $(document).on('click', '#btnsearch', function() {
    var sitecode = $('#s_siteloc').val(); 
    var sitedesc = $('#s_locloc').val(); 
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = 1;


    document.getElementById("sitecodetemp").value = sitecode;
    document.getElementById("sitedesctemp").value = sitedesc;

    fetch_data(page, sort_type, column_name, sitecode, sitedesc);
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
    var sitecode = $('#s_siteloc').val(); 
    var sitedesc = $('#s_locloc').val(); 
    fetch_data(page, reverse_order, column_name, sitecode, sitedesc);
  });

  
  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();

    var locsite = $('#sitecodetemp').val(); 
    var locloc = $('#sitedesctemp').val(); 

    fetch_data(page, sort_type, column_name, locsite, locloc);

  });

 
</script>

@endsection

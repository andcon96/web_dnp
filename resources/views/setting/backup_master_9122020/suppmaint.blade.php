@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Supplier Master</h1>
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

<div class="col-md-2 col-sm-2 mb-3 input-group">
  <form action="/reloadtabelsupplier" method="POST">
      @csrf
          <input type="submit" class="btn bt-action" data-toggle="modal" data-target="#loadingtable" data-backdrop="static" data-keyboard="false"  
          id="btnrefresh" value="Load Table"  style="float:right" />
      </form>
  </div>
<div class="col-12"><hr></div>
<!-- tablesearch -->
<div class="col-12 form-group row">
  <!--FORM Search Disini -->
  <label for="s_suppcode" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Supplier Code') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_suppcode" type="text" class="form-control" name="s_suppcode" value="" autofocus autocomplete="off">
  </div>
  
  <label for="s_suppdesc" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Supplier Desc') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_suppdesc" type="text" class="form-control" name="s_suppdesc" value="" autofocus autocomplete="off" min="0">
  </div>

  <label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:right" />
  </div>
  <label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
  
</div>

<div class="container col-md-12">

<div class="col-md-12"><hr></div>
  <!--Customer Type Table Content-->
  <div class="table-responsive col-sm-12">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th width="20%" class="sorting" data-sorting_type="asc" data-column_name="supp_code" style="cursor: pointer">Supplier Code</th>
          <th width="40%" class="sorting" data-sorting_type="asc" data-column_name="supp_desc" style="cursor: pointer">Supplier Description</th>
          <th width="20%">Telepon Supplier</th>
        </tr>
      </thead>
      <tbody>
        @include('setting.table-suppmaint')
      </tbody>
    </table>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="supp_code" />
    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
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

  function fetch_data(page, sort_type, sort_by, suppcode, suppdesc) {
    $.ajax({
      url: "/suppmaint/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&suppcode=" + suppcode + "&suppdesc=" + suppdesc,
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      }
    })
  }

  $(document).on('click', '#btnsearch', function() {
    var suppcode = $('#s_suppcode').val(); 
    var suppdesc = $('#s_suppdesc').val(); 
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = 1;

    fetch_data(page, sort_type, column_name, suppcode, suppdesc);
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
    var suppcode = $('#s_suppcode').val(); 
    var suppdesc = $('#s_suppdesc').val(); 
    fetch_data(page, reverse_order, column_name, suppcode, suppdesc);
  });

  
  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var suppcode = $('#s_suppcode').val(); 
    var suppdesc = $('#s_suppdesc').val(); 
    fetch_data(page, sort_type, column_name, suppcode, suppdesc);

  });
</script>
@endsection

@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Running Number Master</h1>
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

<!--Table Menu-->

<div class="table-responsive col-12">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th class="sorting" data-sorting_type="asc" data-column_name="site_code" style="cursor: pointer" width="10%">Site Code<span id="name_icon"></span></th>
        <th class="sorting" data-sorting_type="asc" data-column_name="site_desc" style="cursor: pointer">Site Desc<span id="username_icon"></span></th>
        <th class="sorting" data-sorting_type="asc" data-column_name="r_nbr_so" style="cursor:pointer" width="15%">Last SO Nbr</th>
        <th class="sorting" data-sorting_type="asc" data-column_name="r_nbr_so" style="cursor:pointer" width="15%">Last SO Cons</th>
        <th class="sorting" data-sorting_type="asc" data-column_name="r_nbr_so" style="cursor:pointer" width="15%">Last Retur</th>
        <th class="sorting" data-sorting_type="asc" data-column_name="r_nbr_spb" style="cursor:pointer" width="15%">Last SPB Nbr</th>
        <th width="20%">Action</th>
      </tr>
    </thead>
    <tbody>
      @include('setting.table-runnbr')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>

<!--Modal Edit-->
<div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Running Number</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" id="formedit" method="post" action="editrnbr">
        {{ csrf_field() }}

        <div class="modal-body">
          <div class="form-group row">
            <label for="e_sitecode" class="col-md-3 col-form-label text-md-right">Site Code</label>
            <div class="col-md-4 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="e_sitecode" type="text" class="form-control" name="e_sitecode" value="{{ old('e_sitecode') }}" autocomplete="off" maxlength="6" readonly autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_sitedesc" class="col-md-3 col-form-label text-md-right">Site Desc</label>
            <div class="col-md-4">
              <input id="e_sitedesc" type="text" class="form-control" name="e_sitedesc" value="{{ old('e_sitedesc') }}" autocomplete="off" maxlength="24" autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_rnbrso" class="col-md-3 col-form-label text-md-right">Running Nbr SO</label>
            <div class="col-md-4">
              <input id="e_rnbrso" type="text" class="form-control" name="e_rnbrso" value="{{ old('e_rnbrso') }}" autocomplete="off" maxlength="24" autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_rnbrcons" class="col-md-3 col-form-label text-md-right">Running Nbr SO Cons</label>
            <div class="col-md-4">
              <input id="e_rnbrcons" type="text" class="form-control" name="e_rnbrcons" value="{{ old('e_rnbrcons') }}" autocomplete="off" maxlength="24" autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_rnbrretur" class="col-md-3 col-form-label text-md-right">Running Nbr Retur</label>
            <div class="col-md-4">
              <input id="e_rnbrretur" type="text" class="form-control" name="e_rnbrretur" value="{{ old('e_rnbrretur') }}" autocomplete="off" maxlength="24" autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_rnbrspb" class="col-md-3 col-form-label text-md-right">Running Nbr SPB</label>
            <div class="col-md-4">
              <input id="e_rnbrspb" type="text" class="form-control" name="e_rnbrspb" value="{{ old('e_rnbrspb') }}" autocomplete="off" maxlength="24" autofocus required>
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

@endsection

@section('scripts')
<script>
  $(document).on('click', '.edituser', function() {
    var sitecode = $(this).data('sitecode');
    var sitedesc = $(this).data('sitedesc');
    var rnbrso = $(this).data('rnbrso');
    var rnbrspb = $(this).data('rnbrspb');
    var rnbrretur = $(this).data('rnbrretur');
    var rnbrcons = $(this).data('rnbrcons');

    document.getElementById('e_sitecode').value = sitecode;
    document.getElementById('e_sitedesc').value = sitedesc;
    document.getElementById('e_rnbrso').value = rnbrso;
    document.getElementById('e_rnbrspb').value = rnbrspb;
    document.getElementById('e_rnbrretur').value = rnbrretur;
    document.getElementById('e_rnbrcons').value = rnbrcons;

  });

  // flag tunggu semua menu


  $(document).on('click', '.deleteuser', function() {

    var name = $(this).data('name');
    var username = $(this).data('username');

    document.getElementById('d_name').innerHTML = name;
    document.getElementById('d_username').innerHTML = username;
    document.getElementById('tmp_username').value = username;

    // flag tunggu semua menu
  });

  function clear_icon() {
    $('#id_icon').html('');
    $('#post_title_icon').html('');
  }

  function fetch_data(page, sort_type, sort_by, username, name, cabang) {
    $.ajax({
      url: "/usermt/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&username=" + username + "&name=" + name + "&cabang=" + cabang,
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      }
    })
  }


  /*
  $(document).on('click', '#btnsearch', function() {
    var username = $('#s_username').val(); 
    var name = $('#s_name').val(); 
    var cabang = $('#s_cabang').val(); 

    document.getElementById('usernametemp').value = username;
    document.getElementById('nametemp').value = name;
    document.getElementById('sitetemp').value = cabang;

    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = 1;

    fetch_data(page, sort_type, column_name, username, name,cabang);
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
    var username = $('#s_username').val();
    var name = $('#s_name').val();
    var cabang = $('#s_cabang').val();
    fetch_data(page, reverse_order, column_name, username, name,cabang);
  });

  
  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();

    var username = document.getElementById('usernametemp').value;
    var name = document.getElementById('nametemp').value;
    var cabang = document.getElementById('sitetemp').value;
    fetch_data(page, sort_type, column_name, username, name, cabang);

    });

    $('#Role').select2({
    width:'100%'
  });*/

</script>
@endsection

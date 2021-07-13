@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>User / Change Password</h1>
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

<div class="panel-body">
    <form class="form-horizontal" method="POST" action="/userchange/changepass">
        {{ csrf_field() }}

        
        <div>
            <input id="id" type="hidden" class="form-control" name="id" value='{{ $users->id }}' required>
        </div>

        <div class="form-group{{ $errors->has('uname') ? ' has-error' : '' }} row">
            <label for="uname" class="col-md-4 control-label">Username</label>
            <div class="col-md-4">
                <input id="uname" type="text" class="form-control" name="uname" value='{{ $users->username }}' required disabled>
            </div>
        </div>
    
        
        <div class="form-group{{ $errors->has('oldpass') ? ' has-error' : '' }} row">
            <label for="oldpass" class="col-md-4 control-label">Old Password</label>

            <div class="col-md-4">
                <input id="oldpass" type="password" class="form-control" name="oldpass" required>

                @if ($errors->has('oldpass'))
                    <span class="help-block">
                        <strong>{{ $errors->first('oldpass') }}</strong>
                    </span>
                @endif
            </div>
        </div>


        
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} row">
            <label for="password" class="col-md-4 control-label">New Password</label>

            <div class="col-md-4">
                <input id="password" type="password" class="form-control" name="password" required>

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        


       
        <div class="form-group{{ $errors->has('confpass') ? ' has-error' : '' }} row">
            <label for="confpass" class="col-md-4 control-label">Confirm New Password</label>

            <div class="col-md-4">
                <input id="confpass" type="password" class="form-control" name="confpass" required>

                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <br>
        <div class="form-group row">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit" class="btn bt-action newUser">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
  $(document).on('click', '.edituser', function() {

    var name = $(this).data('name');
    var username = $(this).data('username');
    var erole = $(this).data('role');
    var esite = $(this).data('site');

    document.getElementById('e_name').value = name;
    document.getElementById('e_role').value = erole;
    document.getElementById('e_site').value = esite;
    document.getElementById('e_username').value = username;
    
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


  
  $(document).on('click', '#btnsearch', function() {
    var username = $('#s_username').val(); 
    var name = $('#s_name').val(); 
    var cabang = $('#s_cabang').val(); 
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = $('#hidden_page').val();

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

    var username = $('#s_username').val();
    var name = $('#s_name').val();
    var cabang = $('#s_cabang').val();
    fetch_data(page, sort_type, column_name, username, name, cabang);

  });

  $('#select2_example').select2({
    
  });
</script>
@endsection
@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / User Master</h1>
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
<div class="col-12">
  <button class="btn bt-action newUser" data-toggle="modal" data-target="#createModal">
    Create User</button>
    <hr>
</div>


<div class="col-12 form-group row">
  <!--FORM Search Disini-->
  <label for="s_username" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Username') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    
    <input id="s_username" type="text" class="form-control"  name="s_username" value="" autofocus autocomplete="off">
  </div>
  <label for="s_name" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Name') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_name" type="text" class="form-control" name="s_name" value="" autofocus autocomplete="off" min="0">
  </div>
  <label for="s_cabang" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Site') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_cabang" type="text" class="form-control" name="s_cabang" value="" autofocus autocomplete="off" min="0">
  </div>
  <label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:right" />
  </div>
</div>

<input type="hidden" id="usernametemp" name="usernametemp"/>
<input type="hidden" id="nametemp" name="nametemp"/>
<input type="hidden" id="sitetemp" name="sitetemp"/>
<div class=col-12><hr></div>
<div class="table-responsive col-12">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th class="sorting" data-sorting_type="asc" data-column_name="name" style="cursor: pointer" width="20%">Name<span id="name_icon"></span></th>
        <th class="sorting" data-sorting_type="asc" data-column_name="username" style="cursor: pointer">Username<span id="username_icon"></span></th>
        <th class="sorting" data-sorting_type="asc" data-column_name="role_desc" style="cursor:pointer" width="20%">Role</th>
        <th class="sorting" data-sorting_type="asc" data-column_name="site_desc" style="cursor:pointer" width="20%">Site</th>
        <th width="20%">Action</th>
      </tr>
    </thead>
    <tbody>
      @include('setting.table-usermaint')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>

<!--Modal Create-->
<div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="post" action="createuser" autocomplete="off">
          {{ csrf_field() }}

          <div class="form-group row col-md-12">
            <label for="username" class="col-md-5 col-form-label text-md-right">Username</label>
            <div class="col-md-7 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" autocomplete="off" maxlength="6" required autofocus>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="name" class="col-md-5 col-form-label text-md-right">Name</label>
            <div class="col-md-7">
              <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autocomplete="off" maxlength="24" autofocus required>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="Role" class="col-md-5 col-form-label text-md-right">Role</label>
            <div class="col-md-7">
              <select id="Role" class="form-control" name="Role">
                @foreach($datarole as $dr)
                <option value="{{$dr->role_code}}">{{$dr->role_code}} -- {{$dr->role_desc}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="Site" class="col-md-5 col-form-label text-md-right">Site</label>
            <div class="col-md-7">
              <Select id="Site" class="form-control" name="Site">
                @foreach($datasite as $ds)
                <option value="{{$ds->site_code}}">{{$ds->site_code}} -- {{$ds->site_desc}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="password" class="col-md-5 col-form-label text-md-right">Password</label>
            <div class="col-md-7">
              <input id="password" type="password" class="form-control" name="password" autocomplete="new-password" required>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="password-confirm" class="col-md-5 col-form-label text-md-right">Confirm Password</label>
            <div class="col-md-7">
              <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
            </div>
          </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
        <button type="button" class="btn bt-action" id="btnloading" style="display:none">
          <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
        </button>
      </div>
      </form>

    </div>
  </div>
</div>

<!--Modal Edit-->
<div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" id="formedit" method="post" action="edituser">
        {{ csrf_field() }}

        <div class="modal-body">
          <div class="form-group row">
            <label for="e_username" class="col-md-3 col-form-label text-md-right">Username</label>
            <div class="col-md-4 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="e_username" type="text" class="form-control" name="e_username" value="{{ old('e_username') }}" autocomplete="off" maxlength="6" readonly autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_name" class="col-md-3 col-form-label text-md-right">Name</label>
            <div class="col-md-4">
              <input id="e_name" type="text" class="form-control" name="e_name" value="{{ old('e_name') }}" autocomplete="off" maxlength="24" autofocus required>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_Role" class="col-md-3 col-form-label text-md-right">Role</label>
            <div class="col-md-4">
              <Select id="e_role" class="form-control" name="e_role">
                @foreach($datarole as $dr)
                <option value="{{$dr->role_code}}">{{$dr->role_code}} -- {{$dr->role_desc}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_Site" class="col-md-3 col-form-label text-md-right">Site</label>
            <div class="col-md-4">
              <Select id="e_site" class="form-control" name="e_site">
                @foreach ($datasite as $ds)
                <option value="{{$ds->site_code}}">{{$ds->site_code}} -- {{$ds->site_desc}}</option>
                @endforeach
              </select>
            </div>
          </div>
	<div class="form-group row">
            <label for="e_newpass" class="col-md-3 col-form-label text-md-right">New password</label>
            <div class="col-md-4 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="e_newpass" type="password" class="form-control" name="e_newpass" value="" autocomplete="new-password" autofocus>
            </div>
          </div>
          <div class="form-group row">
            <label for="e_name" class="col-md-3 col-form-label text-md-right">New password confirm</label>
            <div class="col-md-4">
              <input id="e_newpassconf" type="password" class="form-control" name="e_newpassconf" value=""  autofocus>
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

<!--Modal Delete-->
<div class="modal fade" id="deleteModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete User</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="post" action="deleteuser">
        {{ csrf_field() }}

        <div class="modal-body">
          <input type="hidden" name="tmp_username" id="tmp_username">
          Anda yakin ingin menghapus user <b> <span id="d_username"></span> -- <span id="d_name"></span> </b> ?
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
@endsection

@section('scripts')
<script>

    $(document).on('submit','#formedit',function(e){
    
    var newpass = $('#e_newpass').val();
    var newpassconf = $('#e_newpassconf').val(); 
    if(newpass === newpassconf){
        $('#formedit').submit(); 	
       }
    else{
	 alert('new password not match');
	 e.preventDefault(); 
	}
     }); 
 
  $(document).on('click', '.edituser', function() {

    var name = $(this).data('name');
    var username = $(this).data('username');
    var erole = $(this).data('role');
    var esite = $(this).data('site');

    document.getElementById('e_name').value = name;
    document.getElementById('e_role').value = erole;
    document.getElementById('e_site').value = esite;
    document.getElementById('e_username').value = username;
    $('#e_role').select2({
    width:'100%',
    erole
  });
  $('#e_site').select2({
    width:'100%',
    esite
  });

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
  });
  $('#Site').select2({
    width:'100%'
  });
</script>
@endsection

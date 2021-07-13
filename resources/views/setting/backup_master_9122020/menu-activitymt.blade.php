@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Activity Master</h1>
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

  <div class="col-lg-9 col-md-8 col-6">
        <button class="btn bt-action newUser" data-toggle="modal" data-target="#createModal">
            Create Activity
        </button>
    </div>
    <div class="col-md-12"><hr></div>
    <!-- Bagian Searching -->
    <div class="col-12 form-group row justify-content-center">
        <label for="s_activitycode" class="col-md-2 col-sm-2 col-form-label text-md-right">Activity Code</label>
        <div class="col-md-4 col-sm-4 mb-2 input-group">
            <input id="s_activitycode" type="text" class="form-control" name="s_activitycode"
            value="" autofocus autocomplete="off"/>
        </div>
        
        <div class="col-md-2 col-sm-4 offset-md-2 offset-lg-0">
          <input type="button" class="btn bt-action" 
          id="btnsearch" value="Search"  style="float:right"/>
        </div>
    </div>
<div class="col-md-12"><hr></div>
    <div class="table-responsive col-12">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th class="sorting" data-sorting_type="asc" data-column_name="activity_code" style="cursor: pointer;">Activity Code<span id="activity_code_icon"></span></th>
                    <th>Description</th>
                    <th style="width: 15%;">Action</th>  
                </tr>
            </thead>
            <tbody>
                <!-- untuk isi table -->
                @include('setting.table-activity')
            </tbody>
        </table>
        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="activity_code"/>
        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLabel">Create Activity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" method="post" action="/activitymt/create">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="t_activityid" class="col-md-4 col-form-label text-md-right">Activity Code</label>
                            <div class="col-md-6">
                                <input id="t_activityid" type="text" class="form-control" name="activity_code"
                                autocomplete="off" autofocus maxlength="6" required pattern="[A-Z0-9]{0,6}" title="Masukan hanya huruf Kapital A-Z dan angka 0-9. Maks.6"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="t_activitydesc" class="col-md-4 col-form-label text-md-right">Description</label>
                            <div class="col-md-6">
                                <input id="t_activitydesc" type="text" class="form-control" name="activity_desc" autocomplete="off" autofocus maxlength="40" required/>
                            </div>
                        </div>
                    </div>
                
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success bt-action" id="btncreate">Create</button> 
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title text-center" id="exampleModalLabel">Edit Activity</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form class="form-horizontal" method="post" action="/activitymt/edit">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group row">
                            <label for="te_activityid" class="col-md-4 col-form-label text-md-right">Activity Code</label>
                            <div class="col-md-6">
                                <input id="te_activityid" type="text" class="form-control" name="activity_code"
                                autocomplete="off" autofocus maxlength="12" readonly/>
                            </div>
                    </div>
                    <div class="form-group row">
                            <label for="te_activitydesc" class="col-md-4 col-form-label text-md-right">Description</label>
                            <div class="col-md-6">
                                <input id="te_activitydesc" type="text" class="form-control" name="activity_desc"
                                autocomplete="off" autofocus maxlength="24"/>
                            </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success bt-action" id="btnedit">Save</button>
                </div>
            </form>
        </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteModal" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">Delete Activity</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form class="form-horizontal" method="post" action="/activitymt/delete">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <!-- <div class="form-group row col-md-12">
                                <label for="td_idsubgroup" class="col-md-3 col-form-label text-md-right">Sub Group Code</label>
                                <div class="col-md-4">
                                    <input id="td_idsubgroup" type="text" class="form-control" name="td_idsubgroup"
                                    autocomplete="off" autofocus maxlength="12"  pattern="[A-Z0-9]{0,12}" 
                                    title="Masukan hanya huruf Kapital A-Z dan angka 0-9. Maks.12" required readonly/>
                                </div>
                        </div>
                        <div class="form-group row col-md-12">
                                <label for="td_groupidtype" class="col-md-3 col-form-label text-md-right">Group Name</label>
                                <div class="col-md-4">
                                    <input id="td_groupidtype" type="text" class="form-control" name="td_groupidtype"
                                    autocomplete="off" autofocus maxlength="24" readonly/>
                                </div>
                        </div>
                        <div class="form-group row col-md-12">
                                <label for="td_description" class="col-md-3 col-form-label text-md-right">Description</label>
                                <div class="col-md-4">
                                    <input id="td_description" type="text" class="form-control" name="td_description"
                                    autocomplete="off" autofocus maxlength="24" readonly/>
                                </div>
                        </div> -->
                        <input type="hidden" id="d_activityid" name="d_activityid">
                        Anda yakin ingin menghapus Activity <b><span id="td_activityid"></span> -- <span id="td_activitydesc"></span></b> ?
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success bt-action" id="btndelete">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
       $(document).on('click', '.editactivity', function(e){
           var activityid = $(this).data('activityid');
           var desc = $(this).data('desc');

           document.getElementById('te_activityid').value = activityid;
           document.getElementById('te_activitydesc').value = desc;
       });

       $(document).on('click', '.deleteactivity', function(e){
            var activityid = $(this).data('activityid');
            var desc = $(this).data('desc');

            document.getElementById('d_activityid').value = activityid;
            document.getElementById('td_activityid').innerHTML = activityid;
            document.getElementById('td_activitydesc').innerHTML = desc;
       });

       function clear_icon()
       {
            $('#id_icon').html('');
            $('#post_title_icon').html('');
       }

       function fetch_data(page, sort_type, sort_by, activitycode){
            $.ajax({
                url:"activitymt/pagination?page="+page+"&sorttype="+sort_type+"&sortby="+sort_by+"&activitycode="+activitycode,
                success:function(data){
                    console.log(data);
                    $('tbody').html('');
                    $('tbody').html(data);
                }
            })
        }

        $(document).on('click', '#btnsearch', function(){
            var activitycode = $('#s_activitycode').val();
            var column_name = $('#hidden_column_name').val();
			var sort_type = $('#hidden_sort_type').val();
			var page = 1;

            fetch_data(page, sort_type, column_name, activitycode);
        });

       $(document).on('click', '.sorting', function(){
			var column_name = $(this).data('column_name');
			var order_type = $(this).data('sorting_type');
			var reverse_order = '';
			if(order_type == 'asc')
			{
			$(this).data('sorting_type', 'desc');
			reverse_order = 'desc';
			clear_icon();
			$('#'+column_name+'_icon').html('<span class="glyphicon glyphicon-triangle-bottom"></span>');
			}
			if(order_type == 'desc')
			{
			$(this).data('sorting_type', 'asc');
			reverse_order = 'asc';
			clear_icon();
			$('#'+column_name+'_icon').html('<span class="glyphicon glyphicon-triangle-top"></span>');
			}
			$('#hidden_column_name').val(column_name);
			$('#hidden_sort_type').val(reverse_order);
            var page = $('#hidden_page').val();
            var activitycode = $('#s_activitycode').val();
			fetch_data(page, reverse_order, column_name, activitycode);
     	});
       
       
       $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            $('#hidden_page').val(page);
            var column_name = $('#hidden_column_name').val();
            var sort_type = $('#hidden_sort_type').val();
            var activitycode = $('#s_activitycode').val();
            fetch_data(page, sort_type, column_name, activitycode);
       });
    </script>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.min.js"></script>

    <script>
        $('#t_groupidtype').select2({
            width: '100%'
        });
        $('#te_groupidtype').select2({
            width: '100%'
        });
    </script>
@endsection

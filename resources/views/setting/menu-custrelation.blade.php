@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Customer Relation</h1>
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

    <!--
    <div class="col-lg-9 col-md-8 col-6">
        <button class="btn bt-action newUser" data-toggle="modal" data-target="#createModal">
            Create Cust Relation
        </button>
    </div>
    -->
    <div class="col-md-2 col-sm-2 mb-3 ml-5 input-group">
      <form action="/loadParentC" method="POST">
          @csrf
              <input type="submit" class="btn bt-action" data-toggle="modal" data-target="#loadingtable" data-backdrop="static" data-keyboard="false"  
              id="btnrefresh" value="Load Table"  style="float:right" />
      </form>
    </div>
    <div class="col-md-12"><hr></div>

    <div class="col-12 form-group row justify-content-center">
        <label for="s_parent" class="col-md-2 col-sm-2 col-form-label text-md-right">Parent code</label>
        <div class="col-md-3 col-sm-4 mb-2 input-group">
            <input id="s_parent" type="text" class="form-control" name="s_parent"
            value="" autofocus autocomplete="off"/>
        </div>

        <label for="s_child" class="col-md-2 col-sm-2 col-form-label text-md-right">Child code</label>
        <div class="col-md-3 col-sm-4 mb-2 input-group">
            <input id="s_child" type="text" class="form-control" name="s_child"
            value="" autofocus autocomplete="off"/>
        </div>
        <div class="col-md-2 col-sm-4 offset-md-2 offset-lg-0">
          <input type="button" class="btn bt-action" 
          id="btnsearch" value="Search"  style="float:right"/>
        </div>
    </div>
    <div class="col-md-12"><hr></div>
    <input type="hidden" id="tempparent" value=""/>
    <input type="hidden" id="tempchild" value=""/>

    <div class="table-responsive col-12">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th class="sorting" data-sorting_type="asc" data-column_name="cust_code_parent" style="cursor:pointer;">Customer Parent<span id="cust_code_parent_icon"></span></th>
                    <th class="sorting" data-sorting_type="asc" data-column_name="cust_code_child" style="cursor:pointer;">Customer Child<span id="cust_code_child_icon"></span></th>
                    <!--
                    <th style="width:15%">Action</th> 
                    -->
                </tr>
            </thead>
            <tbody>
                    <!-- untuk isi table -->
                    @include('setting.table-custrelation')
            </tbody>
        </table>
        <input type="hidden" name="hidden_page" id="hidden_page" value="1"/>
        <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="cust_code_parent"/>
        <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="exampleModalLabel">Create Customer Relation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-horizontal" method="POST" action="/custrelation/create">
                    {{ csrf_field() }}
                    <div class="modal-body">   
                        <div class="form-group row">
                            <label for="t_custparent" class="col-md-5 col-form-label text-md-right">Customer Parent</label>
                            <div class="col-md-5">
                                <select id="t_custparent" class="form-control" name="t_custparent" autocomplete="off" autofocus required>
                                    <option value="" >--Select Cust. Parent--</option>
                                    @foreach($datacust as $show)
                                    <option value="{{$show->cust_code}}" >{{$show->cust_code}} -- {{$show->cust_desc}}</option>
                                    @endforeach
                                    <!-- disini foreach ? -->
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="t_custchild" class="col-md-5 col-form-label text-md-right">Customer Child</label>
                            <div class="col-md-5">
                                <select id="t_custchild" class="form-control" name="t_custchild" autocomplete="off" autofocus required/>
                                    <option value="">--Select Cust. Child--</option>
                                    @foreach($datacust as $show)
                                    <option value="{{$show->cust_code}}" >{{$show->cust_code}} -- {{$show->cust_desc}}</option>
                                    @endforeach
                                    <!-- disini foreach ? -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success bt-action" id="btncreate">Create Relation</button> 

                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title text-center" id="exampleModalLabel">Edit Cust. Relation Group</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form class="form-horizontal" method="post" action="/custrelation/edit">
                {{ csrf_field() }}
                <div class="modal-body">
                    <div class="form-group row">
                        <input type="hidden" id="idrel" name="idrel">
                        <label for="te_custparent" class="col-md-5 col-form-label text-md-right">Cust. Parent Code</label>
                        <div class="col-md-5">
                            <select id="te_custparent"  class="form-control" name="te_custparent" autocomplete="off" autofocus required>
                                    @foreach($datacust as $show)
                                    <option value="{{$show->cust_code}}" >{{$show->cust_code}} -- {{$show->cust_desc}}</option>
                                    @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="hidden_parent" name="hidden_parent">
                    </div>
                    <div class="form-group row">
                        <label for="te_custchild" class="col-md-5 col-form-label text-md-right">Cust. Child Code</label>
                        <div class="col-md-5">
                            <select id="te_custchild" type="text" class="form-control" name="te_custchild" autocomplete="off" autofocus>
                                    @foreach($datacust as $show)
                                    <option value="{{$show->cust_code}}" >{{$show->cust_code}} -- {{$show->cust_desc}}</option>
                                    @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="hidden_child" name="hidden_child">
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

    <!-- Modul Delete -->
    <div class="modal fade" id="deleteModal" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-tittle text-center" id="exampleModalLabel">Delete Cust. Relation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <form class="form-horizontal" method="post" action="/custrelation/delete">
                {{ csrf_field() }}
                <div class="modal-body">
                    <!-- <div class="form-group row col-md-12">
                        <label for="td_groupid" class="col-md-3 col-form-label text-md-right">Group Code</label>
                        <div class="col-md-4">
                            <input id="td_groupid" type="text" class="form-control" name="td_groupid"
                            autocomplete="off" autofocus maxlength="12" readonly/>
                        </div>
                    </div>
                    <div class="form-group row col-md-12">
                        <label for="td_groupname" class="col-md-3 col-form-label text-md-right">Group Name</label>
                        <div class="col-md-4">
                            <input id="td_groupname" type="text" class="form-control" name="td_groupname" 
                            autocomplete="off" maxlength="24" readonly autofocus>
                        </div>
                    </div> -->
                    <input type="hidden" id="d_custparent" name="d_custparent">
                    Apakah anda yakin ingin mnenghapus Cust. Relation <b><span id="td_custparent"></span> dengan child <span id="td_custchild"></span></b> ?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success bt-action" id="btdelete">Delete</button>
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
@endsection

@section('scripts')
    <script>
       
       $(document).on('click', '.editrelation', function(e){
           var custparent = $(this).data('custparent');
           var custchild = $(this).data('custchild');
           var id = $(this).data('id');

           document.getElementById('te_custparent').value = custparent;
           document.getElementById('te_custchild').value = custchild;
           document.getElementById('idrel').value = id;
           
           document.getElementById('hidden_parent').value = custparent;
           document.getElementById('hidden_child').value = custchild;
           

           $("#te_custchild").select2({
                width : '100%',
                custchild
            });
            $("#te_custparent").select2({
                width : '100%',
                custparent
            });
       });

       $(document).on('click', '.deleterelation', function(e){
            var custparent = $(this).data('custparent');
            var custchild = $(this).data('custchild');
            var parentdesc = $(this).data('parentdesc');
            var childdesc = $(this).data('childdesc');

            document.getElementById('d_custparent').value = custparent;
            document.getElementById('td_custparent').innerHTML = custparent.concat(" -- ", parentdesc);
            document.getElementById('td_custchild').innerHTML = custchild.concat(" -- ", childdesc);
       });
       
       
       function clear_icon()
       {
            $('#id_icon').html('');
            $('#post_title_icon').html('');
       }

        function fetch_data(page, sort_type, sort_by, parent, child){
            $.ajax({
                url:"custrelation/pagination?page="+page+"&sorttype="+sort_type+"&sortby="+sort_by+"&parent="+parent+"&child="+child,
                success:function(data){
                    console.log(data);
                    $('tbody').html('');
                    $('tbody').html(data);
                }
            })
        }

         

        $(document).on('click', '#btnsearch', function(){
            var parent = $('#s_parent').val();
            var child = $('#s_child').val();
            var column_name = $('#hidden_column_name').val();
	    var sort_type = $('#hidden_sort_type').val();
	    var page = 1;

	    document.getElementById("tempparent").value =parent;
	    document.getElementById("tempchild").value =child;
	    
            fetch_data(page, sort_type, column_name, parent, child);
            
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
            var parent = $('#s_parent').val();
            var child = $('#s_child').val();
			// var groupname = $('#s_groupname').val();
            fetch_data(page, reverse_order, column_name, parent, child);
            
     	});
       
       
       $(document).on('click', '.pagination a', function(event){
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            $('#hidden_page').val(page);
            var column_name = $('#hidden_column_name').val();
            var sort_type = $('#hidden_sort_type').val();
            var parent = $('#tempparent').val();
            var child = $('#tempchild').val();
            
            fetch_data(page, sort_type, column_name, parent, child);
       });

       $("#t_custchild").select2({
          width : '100%'
        });
        $("#t_custparent").select2({
          width : '100%'
        });

    </script>
@endsection



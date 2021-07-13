@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Approval Level</h1>
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

  <!--Search Disini -->
  <div class="col-12 form-group row">
    <label for="s_username" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Site Code') }}</label>
    <div class="col-sm-4 col-md-4 col-lg-3 mb-2 input-group">
      <input id="s_sitecode" type="text" class="form-control" name="s_sitecode" value="" autofocus autocomplete="off">
    </div>
    <label for="s_name" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Site Desc') }}</label>
    <div class="col-sm-4 col-md-4 col-lg-4 mb-2 input-group">
      <input id="s_sitedesc" type="text" class="form-control" name="s_sitedesc" value="" autofocus autocomplete="off" min="0">
    </div>

    <label for="btnsearch" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
    <div class="col-sm-4 col-md-4 col-lg-4 mb-2 input-group">
      <input type="button" class="btn bt-action" style="width:18em;" id="btnsearch" value="Search" style="float:right" />
    </div>
  </div>
<div class="col-md-12"><hr></div>
  <!--Table Menu-->
  <div class="table-responsive col-12" style="overflow: auto; display: block;white-space: nowrap; margin-left: auto; margin-right: auto;">
    
      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
             <th style="width:20%;">Site Code</th>
             <th style="width:50%;">Site Desc</th>
             <th style="width:20%;">Approver</th>
             <th style="width:10%;">Action</th>   
          </tr>
       </thead>
        <tbody id="test">
          @include('setting.table-menuapproval')                    
        </tbody>
      </table>
  </div>


  <!--Edit Modal-->
  <div class="modal fade" id='editModal' tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  	<div class="modal-dialog modal-lg " role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-center" id="exampleModalLabel">Create/Edit Approval Level</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        	<form class="form-horizontal" method="POST" id='new' action="/createapproval" onkeydown="return event.key != 'Enter';">
              {{ csrf_field() }}
              <div class="modal-body">
                  <div class="form-group row mx-auto">
                      <label for="site" class="col-md-4 text-sm-left col-sm-12 col-form-label text-md-right">Site</label>
                      <div class="col-md-5 col-sm-12">
                        <input id="site" type="text" class="form-control" name="site"
                        autocomplete="off" autofocus readonly/>
                      </div>
                  </div>
                  <div class="dbody">
                    <table id='suppTable' class='table table-striped table-bordered dataTable no-footer order-list mini-table'>
                        <thead>
                            <tr>
                                <th style="width:30%">Approver</th>
                                <th style="width:15%">Order</th>
                                <th style="width:10%">Delete</th>
                            </tr>
                        </thead>
                        <tbody id='detailapp'>
                          
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <input type="button" class="btn btn-lg btn-block" 
                                    id="addrow" value="Add Row" style="background-color:#1234A5; color:white; font-size:16px" />
                                </td>
                            </tr>
                        </tfoot>
                    </table>
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
  </div>
	
@endsection


@section('scripts')
  <script type="text/javascript">

    $(document).on('click', '.editApprovalLevel', function(e){
        var siteid = $(this).data('siteid');
        var sitedesc = $(this).data('sitename');

        //document.getElementById('site').value = siteid.concat(' - ',sitedesc);

        document.getElementById('site').value = siteid + ' - ' + sitedesc;

        jQuery.ajax({
            type : "get",
            url : "{{URL::to("approvalsearch")}}",
            data:{
                search : siteid, 
            },
            success:function(data){
                console.log(data);
                $('#detailapp').html(data);
            }
        });

    });

    function fetch_data(sitecode, sitedesc) {
      $.ajax({
        url: "/approval/searching?sitecode=" + sitecode + "&sitedesc=" + sitedesc,
        success: function(data) {
          console.log(data);
          $('#test').html('');
          $('#test').html(data);
        }
      })
    }

    $(document).on('click', '#btnsearch', function() {
        var sitecode = $('#s_sitecode').val(); 
        var sitedesc = $('#s_sitedesc').val(); 

        fetch_data(sitecode, sitedesc);
    });

		$(document).ready(function () {


        var counter = 0;

       
        $("#addrow").on("click", function () {


            var newRow = $("<tr>");
            var cols = "";

            cols += '<td data-label="Approver">';
            cols += '<select id="userid[]" class="form-control userid" name="userid[]" required autofocus>';
            cols += '<option value = ""> -- Select Data -- </option>'
                    @foreach($user as $user)
                        cols += '<option value={{$user->username}}>{{$user->username}} -- {{$user->name}}</option>';
                    @endforeach
            cols += '</select>';
            cols += '</td>';

            cols += '<td data-title="order[]" data-label="Order"><input type="number" class="form-control form-control-sm order" autocomplete="off" name="order[]" style="height:37px" min="1" step="1" required/></td>';
            
            cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-danger"  value="Delete"></td>';
            cols += '</tr>'

            newRow.append(cols);
            $("table.order-list").append(newRow);
            counter++;

            $('.userid').select2({
              width: '100%',
              dropdownParent: $("#editModal")
	          });
           
        });


        $("table.order-list").on("click", ".ibtnDel", function (event) {
            $(this).closest("tr").remove();       
            counter -= 1
        });

         $("#new").submit(function(e){
              document.getElementById('btnclose').style.display = 'none';
              document.getElementById('btnconf').style.display = 'none';
              document.getElementById('btnloading').style.display = '';
          });
         
    });

  

    
	</script>
@endsection

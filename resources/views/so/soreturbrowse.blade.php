@extends('layout.newlayout')

@section('content-title')
<div class="col-4">
  <div class="page-header float-left full-head">
    <div class="page-title">
      <h1>Transaksi / SO Return</h1>
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

@php($datebsk = date("Y-m-d",strtotime("tomorrow")))
@php($datenow = date("Y-m-d",strtotime("today")))

<div class="col-12">
  @if(!str_contains( Session::get('menu_access'),'TS10'))
  <button class="btn bt-action newUser" data-toggle="modal" data-target="#createModal">
    Create SO Return</button>
  @endif
</div>
<div class="col-md-12"><hr></div>
<!--SEARCHING RETUR BROWSER-->
<div class="form-group row col-md-12">
  <label for="s_returnbr" class="col-md-2 col-form-label text-md-right">{{ __('SO Return Number') }}</label>
  <div class="col-md-3">
    <input id="s_returnbr" type="text" class="form-control" name="s_returnbr" value="" autofocus autocomplete="off">
  </div>
  
  <label for="s_site" class="col-md-2 col-form-label text-md-right">{{ __('Site') }}</label>
  <div class="col-md-3">
    <input id="s_site" type="text" min="0" class="form-control" name="s_site" autofocus autocomplete="off">
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="s_customer" class="col-md-2 col-form-label text-md-right">{{ __('Customer Code') }}</label>
  <div class="col-md-3">
    <select id="s_customer" class="form-control" name="s_customer" autofocus autocomplete="off">
	  <option value=""> Select Data </option>
        @foreach($custsearch as $custsearch)
          <option value="{{$custsearch->cust_code}}">{{$custsearch->cust_code}} -- {{$custsearch->cust_alt_name}}</option>
        @endforeach 
    </select>
  </div>

  <label for="s_shipto" class="col-md-2 col-form-label text-md-right">{{ __('Ship To Code') }}</label>
  <div class="col-md-3 col-lg-3">
    <select id="s_shipto" class="form-control" name="s_shipto" autofocus autocomplete="off">
	  <option value=""> Select Data </option>
        @foreach($shipsearch as $shipsearch)
          <option value="{{$shipsearch->shipto}}">{{$shipsearch->shipto}} -- {{$shipsearch->custname}}</option>
        @endforeach 
        @foreach($cship as $cship)
          <option value="{{$cship->cust_code}}">{{$cship->cust_code}} -- {{$cship->cust_alt_name}}</option>
        @endforeach 
    </select>
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="s_status" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
  <div class="offset-0">
    <input type="button" class="btn bt-action newUser" style="margin-left: 15px;" id="btnsearch" value="Search" />
    <!-- <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-refresh"></i></button> -->
  </div>
</div>

<input type = "hidden" id ="soreturnumbertemp" name="soreturnumbertemp" value=""/>
<input type = "hidden" id ="sitetemp" name="sitetemp" value=""/>
<input type = "hidden" id ="customertemp" name="customertemp" value=""/>
<input type = "hidden" id ="shiptotemp" name="shiptotemp" value=""/>

<!--Table Menu-->
<div class="col-md-12"><hr></div>
<div class="table-responsive col-12">
  <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th style="width:15%">SO Return Number</th>
        <th>Site</th>
        <!--Validasi Kalo pusat doank-->
        <th>Customer</th>
        <th>Ship To</th>
        <th>SO Date</th>
        <th>Remarks</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @include('so.table-retur')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>


<!--Modal View-->
<div class="modal fade" id="viewModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">View SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row col-md-12">
          <label for="e_sonbr" class="col-md-3 col-form-label text-md-right">SO Retur</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="e_sonbr" type="text" class="form-control" name="e_sonbr" value="{{ old('e_sonbr') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_customer" class="col-md-3 col-form-label text-md-right">Search Name</label>
          <div class="col-md-6">
            <input id="e_customer" type="text" class="form-control" name="e_customer" value="{{ old('e_customer') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_brelname" class="col-md-3 col-form-label text-md-right">Customer</label>
          <div class="col-md-6">
            <input id="e_brelname" type="text" class="form-control" name="e_brelname" value="{{ old('e_customer') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_shipto" class="col-md-3 col-form-label text-md-right">Ship To</label>
          <div class="col-md-6">
            <input id="e_shipto" type="text" class="form-control" name="e_shipto" value="{{ old('e_shipto') }}" autocomplete="off" maxlength="24" autofocus required readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
            <label for="view_pricedate" class="col-md-3 col-form-label text-md-right">Price Date</label>
            <div class="col-md-6">
              <input id="view_pricedate" type="text" class="form-control" name="view_pricedate" value="{{ old('pricedate') }}" autocomplete="off" placeholder="yy-mm-dd" maxlength="24" autofocus readonly>
            </div>
        </div>

        <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

        <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
          <thead>
            <tr id='full'>
              <th style="width:5%">Line</th>
              <th style="width:30%">Item</th>
              <th style="width:15%">Qty</th>
              <th style="width:10%">Loc</th>
            </tr>
          </thead>
          <tbody id='e_detailapp'>
          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!--Modal Create-->
<div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create SO Return</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="/createsoreturweb" id="new" onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}

          <div class="form-group row col-md-12">
            <label for="custcode" class="col-md-2 col-form-label text-md-right">Search Name</label>
            <div class="col-md-3 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <select id="custcode" name="custcode" class="form-control" required>
                <option value=""> Select Data</option>
                @foreach($customer as $cust)
                <option value="{{$cust->cust_code}}">{{$cust->cust_code}} -- {{$cust->cust_alt_name}}</option>
                @endforeach
              </select>
            </div>
            <label for="shipto" class="col-md-2 col-form-label text-md-right">Ship To</label>
            <div class="col-md-3">
              <select id="shipto" name="shipto" class="form-control">

              </select>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="brelname" class="col-md-2 col-form-label text-md-right">Customer</label>
            <div class="col-md-8">
              <input id="brelname" type="text" class="form-control" name="brelname" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="addresscust" class="col-md-2 col-form-label text-md-right">Address Cust</label>
            <div class="col-md-8">
              <input id="addresscust" type="text" class="form-control" name="addresscust" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="address" class="col-md-2 col-form-label text-md-right">Address</label>
            <div class="col-md-8">
              <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="pricedate" class="col-md-2 col-form-label text-md-right">Price Date</label>
            <div class="col-md-4">
              <input id="pricedate" type="text" class="form-control" name="pricedate" value="{{$datenow}}" autocomplete="off" maxlength="24" placeholder="yy-mm-dd" autofocus required>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="remarks" class="col-md-2 col-form-label text-md-right">Remarks</label>
            <div class="col-md-8">
              <input id="remarks" type="text" class="form-control" name="remarks" autocomplete="off" maxlength="24" autofocus>
            </div>
          </div>

          <div class="form-group offset-md-1">
            <h4><strong>Detail</strong></h4>
          </div>

          <div class="col-md-10 offset-md-1">
            <table id='suppTable' class='table table-striped table-bordered dataTable no-footer order-list mini-table'>
              <thead>
                <tr id='full'>
                  <th style="width:30%">Item</th>
                  <th style="width:15%">Qty</th>
                  <th style="width:15%">UM</th>
                  <th style="width:15%">Loc</th>
                  <th style="width:10%">Delete</th>
                </tr>
              </thead>
              <tbody id='detailapp'>

              </tbody>
              <tfoot>
                <tr>
                  <td colspan="6">
                    <input type="button" class="btn btn-lg btn-block btn-focus" id="addrow" value="Add Item" style="background-color:#1234A5; color:white; font-size:16px" />
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-action btn-focus" id="btnclosem" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success bt-action btn-focus" id="btnconf">Save</button>
        <button type="button" class="btn bt-action" id="btnloading" style="display:none">
          <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
        </button>
      </div>

      </form>

    </div>
  </div>
</div>

<!--Modal Confirm-->
<div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Confirm SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="/createsoretur" id='confirm' onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}
          <div class="form-group row col-md-12">
            <label for="ed_sonbr" class="col-md-3 col-form-label text-md-right">SO Number</label>
            <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="ed_sonbr" type="text" class="form-control" name="ed_sonbr" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="ed_custcode" class="col-md-3 col-form-label text-md-right">Customer</label>
            <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="ed_custcode" type="text" class="form-control" name="ed_custcode" autocomplete="off" maxlength="24" autofocus readonly>
              <input id="eds_custcode" type="hidden" class="form-control" name="eds_custcode" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="ed_shipto" class="col-md-3 col-form-label text-md-right">Ship To</label>
            <div class="col-md-6">
              <input id="ed_shipto" type="text" class="form-control" name="ed_shipto" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="con_pricedate" class="col-md-3 col-form-label text-md-right">Price Date</label>
            <div class="col-md-6">
              <input id="con_pricedate" type="text" class="form-control" name="con_pricedate" value="{{ old('pricedate') }}" autocomplete="off" placeholder="yy-mm-dd" maxlength="24" autofocus required>
            </div>
          </div>

          <div class="form-group row col-md-12">
            <label for="ed_remarks" class="col-md-3 col-form-label text-md-right">Remarks</label>
            <div class="col-md-6">
              <input id="ed_remarks" type="text" class="form-control" name="ed_remarks" autocomplete="off" maxlength="24" autofocus>
            </div>
          </div>

          <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

          <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
            <thead>
              <tr id='full'>
                <th style="width:35%">Item</th>
                <th style="width:15%">Qty Retur</th>
                <th style="width:15%">Loc</th>
                <th style="width:15%">Qty Actual</th>
                <th style="width:15%">Delete</th>
              </tr>
            </thead>
            <tbody id='ed_detailapp'>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="6">
                  <input type="button" class="btn btn-lg btn-block btn-focus" id="ed_addrow" value="Add Item" style="background-color:#1234A5; color:white; font-size:16px" />
                </td>
              </tr>
            </tfoot>
          </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-action" id="c_btnclose" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success bt-action btn-focus" id="c_btnconf">Save</button>
        <button type="button" class="btn bt-action" id="c_btnloading" style="display:none">
          <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
        </button>
      </div>

      </form>
    </div>
  </div>
</div>

<!--Modal Edit Web-->
<div class="modal fade" id="editmodalweb" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="/editsoreturweb" id='edited' onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}
          <div class="form-group row col-md-12">
            <label for="edw_sonbr" class="col-md-3 col-form-label text-md-right">SO Number</label>
            <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="edw_sonbr" type="text" class="form-control" name="edw_sonbr" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="edw_custcode" class="col-md-3 col-form-label text-md-right">Customer</label>
            <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="edw_custcode" type="text" class="form-control" name="edw_custcode" autocomplete="off" maxlength="24" autofocus readonly>
              <input id="edsw_custcode" type="hidden" class="form-control" name="edsw_custcode" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="edw_shipto" class="col-md-3 col-form-label text-md-right">Ship To</label>
            <div class="col-md-6">
              <input id="edw_shipto" type="text" class="form-control" name="edw_shipto" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>

          <div class="form-group row col-md-12">
            <label for="ed_pricedate" class="col-md-3 col-form-label text-md-right">Price Date</label>
            <div class="col-md-6">
              <input id="ed_pricedate" type="text" class="form-control" name="ed_pricedate" autocomplete="off" placeholder="yy-mm-dd" maxlength="24" autofocus required>
            </div>
          </div>

          <div class="form-group row col-md-12">
            <label for="edw_remarks" class="col-md-3 col-form-label text-md-right">Remarks</label>
            <div class="col-md-6">
              <input id="edw_remarks" type="text" class="form-control" name="edw_remarks" autocomplete="off" maxlength="24" autofocus>
            </div>
          </div>

          <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

          <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
            <thead>
              <tr id='full'>
                <th style="width:35%">Item</th>
                <th style="width:15%">Qty Retur</th>
                <th style="width:15%">Loc</th>
                <th style="width:15%">Delete</th>
              </tr>
            </thead>
            <tbody id='edw_detailapp'>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="6">
                  <input type="button" class="btn btn-lg btn-block btn-focus" id="edw_addrow" value="Add Item" style="background-color:#1234A5; color:white; font-size:16px" />
                </td>
              </tr>
            </tfoot>
          </table>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success bt-action btn-focus" id="e_btnconf">Save</button>
        <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
          <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
        </button>
      </div>

      </form>
    </div>
  </div>
</div>

<!--Modal Delete-->
<div class="modal fade" id="delModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete SO Retur</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="/deleteretur" id='delete' onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}
          Delete SO Number : <b><span id='del_sonbr'></span></b>
          <input type="hidden" name="text_sonbr" id="text_sonbr">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-action" id="d_btnclose" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success bt-action btn-focus" id="d_btnconf">Save</button>
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
<script type="text/javascript">
  $(document).ready(function() {
    var counter = 0;
    $("#pricedate").datepicker({
      dateFormat: 'yy-mm-dd',
    });

    $("#ed_pricedate").datepicker({
      dateFormat: 'yy-mm-dd',
    });

    $("#con_pricedate").datepicker({
      dateFormat: 'yy-mm-dd',
    });

    $("#view_pricedate").datepicker({
      dateFormat: 'yy-mm-dd',
    });


    $("#duedate").datepicker({
      dateFormat: 'yy-mm-dd',
      minDate: '+0d',
      onClose: function() {
        $("#addrow").focus();
      }
    });

    $("#custcode").select2({
      width: '100%'
    });
    $("#shipto").select2({
      width: '100%'
    });
    $("#s_customer").select2({
	    width: '100%'
    });
    $("#s_shipto").select2({
	    width: '100%'
    });

    function selectRefresh() {
      $('.barang').selectpicker().focus();
      $('.selectpicker').selectpicker();
    }

    $("#addrow").on("click", function() {

      var newRow = $("<tr>");
      var cols = "";


      cols += '<td data-label="Barang">';
      cols += '<select id="barang" class="form-control barang selectpicker" data-live-search="true" name="barang[]" required autofocus>';
      cols += '<option value = "">  Select Data </option>'
      @foreach($item as $item)
      cols += '<option value="{{$item->itemcode}}"> {{$item->itemcode}} -- {{$item->itemdesc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="jumlah[]" data-label="Jumlah"><input type="number" class="form-control" autocomplete="off" name="jumlah[]" style="height:37px" required min="1"/></td>';

      cols += '<td data-title="um[]" data-label="Satuan"><input type="text" class="form-control um" autocomplete="off" name="um[]" style="height:37px" min="1" step="1" required readonly/></td>';

      cols += '<td data-label="Location">';
      cols += '<select id="loc" class="form-control loc selectpicker" data-live-search="true" name="loc[]" required autofocus>';
      cols += '<option value = "">  Select Data </option>'
      @foreach($location as $location)
      cols += '<option value="{{$location->loc_loc}}"> {{$location->loc_loc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-danger btn-focus"  value="Delete"></td>';
      cols += '</tr>'
      newRow.append(cols);
      $("#detailapp").append(newRow);
      counter++;

      selectRefresh();
    });

    $("table.order-list").on("click", ".ibtnDel", function(event) {
      $(this).closest("tr").remove();
      counter -= 1
    });

    $(document).on('click', '.newUser', function(event) {
      $("#detailapp").html('');
    });

    $(document).on('submit', '#new,#edited,#delete,#confirm', function(e) {
      
      
      document.getElementById('btnconf').style.display = 'none';
      document.getElementById('btnclosem').style.display = 'none';
      document.getElementById('btnloading').style.display = '';

      document.getElementById('e_btnconf').style.display = 'none';
      document.getElementById('e_btnclose').style.display = 'none';
      document.getElementById('e_btnloading').style.display = '';

      document.getElementById('c_btnconf').style.display = 'none';
      document.getElementById('c_btnclose').style.display = 'none';
      document.getElementById('c_btnloading').style.display = '';

      document.getElementById('d_btnconf').style.display = 'none';
      document.getElementById('d_btnclose').style.display = 'none';
      document.getElementById('d_btnloading').style.display = '';
    });

    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      $('#hidden_page').val(page);
      var column_name = $('#hidden_column_name').val();
      var sort_type = $('#hidden_sort_type').val();
      var returnumber = $('#soreturnumbertemp').val();
      var site = $('#sitetemp').val();
      var customer = $('#customertemp').val();
      var shipto = $('#shiptotemp').val();  

      fetch_data(page, sort_type, column_name, returnumber, site, customer, shipto);
    });

    $(document).on('change', '#custcode', function() {

      var cust = document.getElementById('custcode').value;
      var custdesc = $("#custcode option:selected").text();;
      var i = 0;
      var toAppend = '';

      $.ajax({
        url: "/shiptosearch",
        data: {
          cust: cust,
        },
        success: function(data) {
          console.log(data);

          if ($.trim(data)) {
            // ada isi
            var list = data.split("||");
            for (var i = 0; i < list.length - 1; i++) {
              toAppend += '<option value=' + list[i] + '>' + list[i] + '</option>';
            }
            $('#shipto').html('').append(toAppend);
          } else {
            $('#shipto').html('').append('<option value="' + cust + '">' + custdesc + '</option>');
          }

          var shipto = document.getElementById('shipto').value;

          $.ajax({
            url: "/alamatsearch",
            data: {
              cust: cust,
              shipto: shipto,
            },
            success: function(data) {
              console.log(data);
              document.getElementById('address').value = data.trim();
            }
          })

          $.ajax({
            url: "/alamatcust",
            data: {
              cust: cust,
            },
            success: function(data) {
              console.log(data);
              document.getElementById('addresscust').value = data.trim();
            }
          })

          $.ajax({
            url: "/brelnamesearch",
            data: {
              cust: cust,
            },
            success: function(data) {
              console.log(data);
              document.getElementById('brelname').value = data.trim();
            }
          })


        }
      })
    });

    $(document).on('change', '#shipto', function() {

      var cust = document.getElementById('custcode').value;
      var shipto = document.getElementById('shipto').value;

      $.ajax({
        url: "/alamatsearch",
        data: {
          cust: cust,
          shipto: shipto,
        },
        success: function(data) {
          console.log(data);
          document.getElementById('address').value = data.trim();
        }
      })
    });

    $(document).on('change', 'select.barang', function() {
      var um = $(this).closest('tr').find('.um');
      var loc = $(this).closest('tr').find('.loc');
      var item = $(this).val();

      $.ajax({
        url: "/getumitem",
        data: {
          item: item,
        },
        success: function(data) {
          var newdata = data.split("||");
          console.log(um);
          um.val($.trim(newdata[0]));
        }
      })
    });
  });

  function selectRefresh() {
      $('.barang').selectpicker().focus();
      $('.selectpicker').selectpicker();
    }
  
  function fetch_data(page, sort_type, sort_by, returnumber, site, customer, shipto) {
    $.ajax({
      url: "/soretur/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&returnumber=" + returnumber + "&site=" + site + "&customer=" + customer + "&shipto=" + shipto,
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      }
    })
  }

  $(document).on('click', '#btnsearch', function() {
    var returnumber = $('#s_returnbr').val();
    var site = $('#s_site').val();
    var customer = $('#s_customer').val();
    var shipto = $('#s_shipto').val();
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = 1;


    document.getElementById('soreturnumbertemp').value = returnumber;
    document.getElementById('sitetemp').value = site;
    document.getElementById('customertemp').value = customer;
    document.getElementById('shiptotemp').value = shipto;

	
    fetch_data(page, sort_type, column_name, returnumber, site, customer, shipto);
  });

  $(document).on('click', '.viewmodal', function() {
    var sonbr = $(this).data('sonbr');
    var cust = $(this).data('cust');
    var shipto = $(this).data('shipto');
    var namacust = $(this).data('namacust');
    var namaship = $(this).data('namaship');
    var pricedate = $(this).data('pricedate');
    var brelname = $(this).data('brelname');

    document.getElementById('e_customer').value = cust + ' - ' + namacust;
    document.getElementById('e_shipto').value = shipto + ' - ' + namaship;
    document.getElementById('e_sonbr').value = sonbr;
    document.getElementById('view_pricedate').value = pricedate;
    document.getElementById('e_brelname').value = cust + ' - ' + brelname;


    $.ajax({
      url: "/detailreturbrowse",
      data: {
        sonbr: sonbr,
      },
      success: function(data) {
        console.log(data);
        $('#e_detailapp').html('');
        $('#e_detailapp').html(data);
      }
    })
  });

  $(document).on("click", '.editmodal', function() {
    var sonbr = $(this).data('sonbr');
    var cust = $(this).data('cust');
    var desc = $(this).data('desc');
    var shipto = $(this).data('shipto');
    var remarks = $(this).data('remarks');
    var pricedate = $(this).data('pricedate');

    document.getElementById('ed_sonbr').value = sonbr;
    document.getElementById('ed_custcode').value = cust + ' - ' + desc;
    document.getElementById('eds_custcode').value = cust;
    document.getElementById('ed_shipto').value = shipto;
    document.getElementById('ed_shipto').value = shipto;
    document.getElementById('ed_remarks').value = remarks;
    document.getElementById('con_pricedate').value = pricedate;

    $.ajax({
      url: "/editdetailretur",
      data: {
        cust: cust,
        sonbr: sonbr,
      },
      success: function(data) {
        console.log(data);
        $('#ed_detailapp').html('').append(data);
        selectRefresh();
      }
    })
  });

  $(document).on("click", '.editmodalweb', function() {
    var sonbr = $(this).data('sonbr');
    var cust = $(this).data('cust');
    var desc = $(this).data('desc');
    var shipto = $(this).data('shipto');
    var remarks = $(this).data('remarks');
    var pricedate = $(this).data('pricedate');

    document.getElementById('edw_sonbr').value = sonbr;
    document.getElementById('edw_custcode').value = cust + ' - ' + desc;
    document.getElementById('edsw_custcode').value = cust;
    document.getElementById('edw_shipto').value = shipto;
    document.getElementById('edw_shipto').value = shipto;
    document.getElementById('edw_remarks').value = remarks;
    document.getElementById('ed_pricedate').value = pricedate;

    $.ajax({
      url: "/editsoreturwebdetail",
      data: {
        cust: cust,
        sonbr: sonbr,
      },
      success: function(data) {
        console.log(data);
        $('#edw_detailapp').html('').append(data);
        selectRefresh();
      }
    })
  });

  $(document).on("click", '.deletemodal', function() {
    var sonbr = $(this).data('sonbr');

    document.getElementById('del_sonbr').innerHTML = sonbr;
    document.getElementById('text_sonbr').value = sonbr;
  });

  $(document).on('change', '.qaddel', function() {
      var checkbox = $(this), // Selected or current checkbox
        value = checkbox.val(); // Value of checkbox

      if (checkbox.is(':checked')) {
        $(this).closest("tr").find('.defdel').val('R');
        $(this).closest("tr").find('.qtyso').prop('readonly', true);
      } else {
        $(this).closest("tr").find('.defdel').val('M');
        $(this).closest("tr").find('.qtyso').prop('readonly', false);
      }
  });

  $(document).on('click', '#ed_addrow', function() {

      var newRow = $("<tr>");
      var cols = "";


      cols += '<td data-label="Barang">';
      cols += '<select id="barang" class="form-control barang selectpicker" data-live-search="true" name="itemcode[]" required autofocus>';
      cols += '<option value = "">  Select Data </option>'
      @foreach($itemedit as $item)
      cols += '<option value="{{$item->itemcode}}"> {{$item->itemcode}} -- {{$item->itemdesc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="qtyship[]" data-label="Jumlah"><input type="number" class="form-control" autocomplete="off" name="qtyretur[]" style="height:37px" min="1" required/></td>';

      cols += '<td data-label="Location">';
      cols += '<select id="loc" class="form-control loc selectpicker" data-live-search="true" name="loc[]" required autofocus>';
      cols += '<option value = "">  Select Data </option>';
      
      @foreach($loced as $location)
      cols += '<option value="{{$location->loc_loc}}"> {{$location->loc_loc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="qtyso[]" data-label="Jumlah"><input type="number" class="form-control" autocomplete="off" name="qtyso[]" style="height:37px" required min="1" required/></td>';


      cols += '<td data-title="Action" style="vertical-align:middle;text-align:center;"><input type="button" class="ibtnDel btn btn-danger btn-focus"  value="Delete"></td>';

      cols += '<input type="hidden" name="delLine[]" value="A">';

      cols += '</tr>'
      newRow.append(cols);
      $("#ed_detailapp").append(newRow);

      selectRefresh();
  });

  $(document).on('click', '#edw_addrow', function() {

      var newRow = $("<tr>");
      var cols = "";


      cols += '<td data-label="Barang">';
      cols += '<select id="barang" class="form-control barang selectpicker" data-live-search="true" name="itemcode[]" required autofocus>';
      cols += '<option value = "">  Select Data </option>'
      @foreach($itemedit as $item)
      cols += '<option value="{{$item->itemcode}}"> {{$item->itemcode}} -- {{$item->itemdesc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="qtyso[]" data-label="Jumlah"><input type="number" class="form-control" autocomplete="off" name="qtyso[]" style="height:37px" required min="1" required/></td>';

      cols += '<td data-label="Location">';
      cols += '<select id="loc" class="form-control loc selectpicker" data-live-search="true" name="loc[]" required autofocus>';
      cols += '<option value = "">  Select Data </option>';
      @foreach($loced as $location)
      cols += '<option value="{{$location->loc_loc}}"> {{$location->loc_loc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="Action" style="vertical-align:middle;text-align:center;"><input type="button" class="ibtnDel btn btn-danger btn-focus"  value="Delete"></td>';

      cols += '<input type="hidden" name="delLine[]" value="A">';

      cols += '</tr>'
      newRow.append(cols);
      $("#edw_detailapp").append(newRow);

      selectRefresh();
  });
</script>
@endsection

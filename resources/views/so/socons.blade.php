@extends('layout.newlayout')

@section('content-title')
<div class="col-12">
  <div class="page-header float-left full-head">
    <div class="page-title">
      <h1>Transaksi / Sales Order Consignment</h1>
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
<div class="col-12">
  @if(!str_contains( Session::get('menu_access'),'TS10'))
  <button class="btn bt-action newUser" data-toggle="modal" data-target="#createModal">
    Create SO Consignment</button>
  @endif
</div>

<!--- SEARCHING BROWSER SALES SAD-->
<div class="form-group row col-md-12">
  <label for="s_sonumber" class="col-md-2 col-form-label text-md-right">{{ __('SO Number.') }}</label>
  <div class="col-md-3">
    <input id="s_sonumber" type="text" class="form-control" name="s_sonumber" value="" autofocus autocomplete="off">
  </div>
  <label for="s_customer" class="col-md-2 col-form-label text-md-right">{{ __('Customer') }}</label>
  <div class="col-md-3">
    <select id="s_customer" class="form-control" name="s_customer" autofocus autocomplete="off">
	  <option value=""> Select Data </option>
        @foreach($custsearch as $custsearch)
          <option value="{{$custsearch->cust_code}}">{{$custsearch->cust_code}} -- {{$custsearch->cust_alt_name}}</option>
        @endforeach 
    </select>
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="s_totalstart" class="col-md-2 col-form-label text-md-right">{{ __('Total Start') }}</label>
  <div class="col-md-3">
    <input id="s_totalstart" type="number" min="0" class="form-control" name="s_totalstart" autofocus autocomplete="off">
  </div>

  <label for="s_totalto" class="col-md-2 col-form-label text-md-right">{{ __('Total To') }}</label>
  <div class="col-md-3">
    <input id="s_totalto" type="number" min="0" class="form-control" name="s_totalto" autofocus autocomplete="off">
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="datefrom" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Due Date From') }}</label>
  <div class="col-md-4 col-lg-3">
    <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="YYYY-MM-DD" required autofocus autocomplete="off">
  </div>
  <label for="dateto" class="col-md-2 col-lg-2 col-form-label text-md-right">{{ __('Due Date To') }}</label>
  <div class="col-md-4 col-lg-3">
    <input type="text" id="dateto" class="form-control" name='dateto' placeholder="YYYY-MM-DD" required autofocus autocomplete="off">
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="s_status" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
  <div class="offset-0">
    <input type="button" class="btn bt-action newUser" style="margin-left: 15px;" id="btnsearch" value="Search" />
    <!-- <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-refresh"></i></button> -->
  
  </div>
</div>
<input type = "hidden" id ="sonumbertemp" name="sonumbertemp" value=""/>
<input type = "hidden" id ="customertemp" name="customertemp" value=""/>
<input type = "hidden" id ="totalstarttemp" name="totalstarttemp" value=""/>
<input type = "hidden" id ="totaltotemp" name="totaltotemp" value=""/>
<input type = "hidden" id ="datetotemp" name="datetotemp" value=""/>
<input type = "hidden" id ="datefromtemp" name="datefromtemp" value=""/>


<!--Table Menu-->

<div class="col-md-12"><hr></div>
<div class="table-responsive col-12">
  <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th>SO Number</span></th>
        <th>Customer</span></th>
        <th>SO Date</th>
        <th>Due Date</th>
        <th>Status</th>
        <th>Total</th>
        <th width="15%">Action</th>
      </tr>
    </thead>
    <tbody>
      @include('so.table-socons')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>

<!--Modal View-->
<div class="modal fade" id="viewModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">View SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row col-md-12">
          <label for="e_sonbr" class="col-md-3 col-form-label text-md-right">SO Number</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="e_sonbr" type="text" class="form-control" name="e_sonbr" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_custcode" class="col-md-3 col-form-label text-md-right">Search Name</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="e_custcode" type="text" class="form-control" name="e_custcode" value="{{ old('e_custcode') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_brelname" class="col-md-3 col-form-label text-md-right">Customer</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="e_brelname" type="text" class="form-control" name="e_brelname" value="" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_addresscust" class="col-md-3 col-form-label text-md-right">Address Cust</label>
          <div class="col-md-6">
            <input id="e_addresscust" type="text" class="form-control" name="e_addresscust" value="{{ old('e_address') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_address" class="col-md-3 col-form-label text-md-right">Address Ship</label>
          <div class="col-md-6">
            <input id="e_address" type="text" class="form-control" name="e_address" value="{{ old('e_address') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_duedate" class="col-md-3 col-form-label text-md-right">Due Date</label>
          <div class="col-md-6">
            <input id="e_duedate" type="text" class="form-control" name="e_duedate" value="{{ old('e_duedate') }}" autocomplete="off" maxlength="24" autofocus placeholder="yy-mm-dd" required readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_shipto" class="col-md-3 col-form-label text-md-right">Ship To</label>
          <div class="col-md-6">
            <input id="e_shipto" type="text" class="form-control" name="e_shipto" value="{{ old('e_shipto') }}" autocomplete="off" readonly>
          </div>
        </div>

        <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

        <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
          <thead>
            <tr id='full'>
              <th style="width:30%">Item</th>
              <th style="width:15%">Qty</th>
              <th style="width:15%">UM</th>
              <th style="width:15%">List Price</th>
              <th style="width:15%">Disc</th>
              <th style="width:15%">Total</th>
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
        <h5 class="modal-title text-center" id="exampleModalLabel">Create SO Consignment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="/createsoconsweb" id="new" onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}

          <div class="form-group row col-md-12">
            <label for="custcode" class="col-md-2 col-form-label text-md-right">Search Name</label>
            <div class="col-md-3 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <select id="custcode" name="custcode" class="form-control" required>
                <option value="">-- Select Data --</option>
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
              <input id="brelname" type="text" class="form-control" name="brelname" value="{{ old('address') }}" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="addresscust" class="col-md-2 col-form-label text-md-right">Address Cust</label>
            <div class="col-md-8">
              <input id="addresscust" type="text" class="form-control" name="addresscust" value="{{ old('address') }}" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="address" class="col-md-2 col-form-label text-md-right">Address Ship</label>
            <div class="col-md-8">
              <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="duedate" class="col-md-2 col-form-label text-md-right">Due Date</label>
            <div class="col-md-3">
              <input id="duedate" type="text" class="form-control" name="duedate" autocomplete="off" maxlength="24" autofocus value="{{$datebsk}}" placeholder="YYYY-MM-DD" required>
            </div>
          </div>

          <div class="form-group offset-md-1">
            <h4><strong>Detail</strong></h4>
          </div>

          <div class="col-md-10 offset-md-1">
            <table id='suppTable' class='table table-striped table-bordered dataTable no-footer order-list mini-table' style="table-layout: fixed;">
              <thead>
                <tr id='full'>
                  <th style="width:55%">Item</th>
                  <th style="width:15%">Qty</th>
                  <th style="width:15%">UM</th>
                  <th style="width:15%">Delete</th>
                </tr>
              </thead>
              <tbody id='detailapp'>

              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5">
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

<!--Modal Edit-->
<div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit SO Consignment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="/editsoconsweb" id="edited" onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}

          <div class="form-group row col-md-12">
            <label for="ed_sonbr" class="col-md-2 col-form-label text-md-right">SO Number</label>
            <div class="col-md-3 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="ed_sonbr" type="text" class="form-control" name="ed_sonbr" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
            <label for="ed_duedate" class="col-md-2 col-form-label text-md-right">Due Date</label>
            <div class="col-md-3">
              <input id="ed_duedate" type="text" class="form-control" name="ed_duedate" autocomplete="off" maxlength="24" autofocus value="{{$datebsk}}" placeholder="YYYY-MM-DD" required>
            </div>
          </div>

          <div class="form-group row col-md-12">
            <label for="ed_custcode" class="col-md-2 col-form-label text-md-right">Customer</label>
            <div class="col-md-3 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="ed_custcode" type="text" class="form-control" name="ed_custcode" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
            <label for="ed_shipto" class="col-md-2 col-form-label text-md-right">Ship To</label>
            <div class="col-md-3">
              <input id="ed_shipto" type="text" class="form-control" name="ed_shipto" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="ed_addresscust" class="col-md-2 col-form-label text-md-right">Address Cust</label>
            <div class="col-md-8">
              <input id="ed_addresscust" type="text" class="form-control" name="ed_addresscust" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="ed_address" class="col-md-2 col-form-label text-md-right">Address Ship</label>
            <div class="col-md-8">
              <input id="ed_address" type="text" class="form-control" name="ed_address" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>

          <div class="form-group offset-md-1">
            <h4><strong>Detail</strong></h4>
          </div>

          <div class="col-md-10 offset-md-1">
            <table id='suppTable' class='table table-striped table-bordered dataTable no-footer order-list mini-table' style="table-layout: fixed;">
              <thead>
                <tr id='full'>
                  <th style="width:55%">Item</th>
                  <th style="width:15%">Qty</th>
                  <th style="width:15%">UM</th>
                  <th style="width:15%">Delete</th>
                </tr>
              </thead>
              <tbody id='ed_detailapp'>

              </tbody>
              <tfoot>
                <tr>
                  <td colspan="5">
                    <input type="button" class="btn btn-lg btn-block btn-focus" id="ed_addrow" value="Add Item" style="background-color:#1234A5; color:white; font-size:16px" />
                  </td>
                </tr>
              </tfoot>
            </table>
          </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-action btn-focus" id="ed_btnclosem" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success bt-action btn-focus" id="ed_btnconf">Save</button>
        <button type="button" class="btn bt-action" id="ed_btnloading" style="display:none">
          <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
        </button>
      </div>

      </form>

    </div>
  </div>
</div>

<!--Modal Conf-->
<div class="modal fade" id="confModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Confirm SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row col-md-12">
          <label for="edw_sonbr" class="col-md-3 col-form-label text-md-right">SO Number</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="edw_sonbr" type="text" class="form-control" name="edw_sonbr" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="edw_custcode" class="col-md-3 col-form-label text-md-right">Search Name</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="edw_custcode" type="text" class="form-control" name="edw_custcode" value="{{ old('e_custcode') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="edw_brelname" class="col-md-3 col-form-label text-md-right">Customer</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="edw_brelname" type="text" class="form-control" name="edw_brelname" value="{{ old('e_custcode') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="edw_addresscust" class="col-md-3 col-form-label text-md-right">Address Cust</label>
          <div class="col-md-6">
            <input id="edw_addresscust" type="text" class="form-control" name="edw_addresscust" value="{{ old('e_address') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="edw_address" class="col-md-3 col-form-label text-md-right">Address Ship</label>
          <div class="col-md-6">
            <input id="edw_address" type="text" class="form-control" name="edw_address" value="{{ old('e_address') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="edw_duedate" class="col-md-3 col-form-label text-md-right">Due Date</label>
          <div class="col-md-6">
            <input id="edw_duedate" type="text" class="form-control" name="edw_duedate" value="{{ old('e_duedate') }}" autocomplete="off" maxlength="24" autofocus placeholder="yy-mm-dd" required readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="edw_shipto" class="col-md-3 col-form-label text-md-right">Ship To</label>
          <div class="col-md-6">
            <input id="edw_shipto" type="text" class="form-control" name="edw_shipto" value="{{ old('e_shipto') }}" autocomplete="off" readonly>
          </div>
        </div>

        <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

        <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table'>
          <thead>
            <tr id='full'>
              <th style="width:30%">Item</th>
              <th style="width:15%">Qty</th>
              <th style="width:15%">UM</th>
              <th style="width:15%">Net Price</th>
              <th style="width:15%">Disc</th>
              <th style="width:15%">Total</th>
            </tr>
          </thead>
          <tbody id='edw_detailapp'>
          </tbody>
        </table>
      </div>

      <div class="modal-footer">
        <form class="form-horizontal" method="POST" action="/confirmsocons" id="conf" onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}
          <input type="hidden" name="h_edw_sonbr" id="h_edw_sonbr">
          <button type="button" class="btn btn-info bt-action" id="edw_btnclose" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success bt-action btn-focus" id="edw_btnconf">Confirm</button>
          <button type="button" class="btn bt-action" id="edw_btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </form>
      </div>
    </div>
  </div>
</div>


<!--Modal Delete-->
<div class="modal fade" id="deleteModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="/deletesocons" onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}
          <div class="d-flex">
            Delete SO Number : &nbsp; <b>
              <p id='d_sonbr' style="font-weight: 500;color:black"></p>
            </b> &nbsp; ?
            <input type="hidden" id="de_sonbr" name="de_sonbr">
          </div>
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
  $("#duedate").datepicker({
    dateFormat: 'yy-mm-dd',
    minDate: '+0d',
    onClose: function() {
      $("#addrow").focus();
    }
  });

  $("#ed_duedate").datepicker({
    dateFormat: 'yy-mm-dd',
    minDate: '+0d',
  });

  $(document).ready(function() {
    var counter = 0;

    $("#custcode").select2({
      width: '100%'
    });

    $("#s_customer").select2({
	   width: '100%'
    });

    $("#shipto").select2({
      width: '100%'
    });

    function selectRefresh() {
      $('.selectpicker').selectpicker().focus();
    }

    $("#addrow").on("click", function() {

      var newRow = $("<tr>");
      var cols = "";


      cols += '<td data-label="Barang">';
      cols += '<select id="barang" class="form-control barang selectpicker" data-live-search="true" name="barang[]" required autofocus>';
      cols += '<option value = ""> -- Select Data -- </option>'
      @foreach($item as $item)
      cols += '<option value="{{$item->itemcode}}"> {{$item->itemcode}} -- {{$item->itemdesc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="jumlah[]" data-label="Jumlah"><input type="number" class="form-control" step="0.01" autocomplete="off" name="jumlah[]" style="height:37px" required min="0"/></td>';

      cols += '<td data-label="UM">';
      cols += '<select id="um" class="form-control um" name="um[]" required autofocus>';
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-danger btn-focus"  value="Delete"></td>';

      cols += '</tr>'
      newRow.append(cols);
      $("#detailapp").append(newRow);
      counter++;

      selectRefresh();
    });

    $(".newUser").on("click", function() {
      $('#shipto').focus();
      //$("#custcode").select2('open');
    });

    $("table.order-list").on("click", ".ibtnDel", function(event) {
      $(this).closest("tr").remove();
      counter -= 1
    });

    $(document).on('click', '.viewmodal', function(e) {
      var sonbr = $(this).data('sonbr');
      var cust = $(this).data('cust');
      var desc = $(this).data('custdesc');
      var alamat = $(this).data('alamat');
      var duedate = $(this).data('duedate');
      var shipto = $(this).data('shipto');
      var brelname = $(this).data('brelname');

      document.getElementById('e_sonbr').value = sonbr;
      document.getElementById('e_custcode').value = cust + ' - ' + desc;
      document.getElementById('e_addresscust').value = alamat;
      document.getElementById('e_duedate').value = duedate;
      document.getElementById('e_shipto').value = shipto;
      document.getElementById('e_brelname').value = cust + ' - ' + brelname;


      $.ajax({
        url: "/detailsalescons",
        data: {
          sonbr: sonbr,
        },
        success: function(data) {
          console.log(data);
          $('#e_detailapp').html('');
          $('#e_detailapp').html(data);
        }
      })

      $.ajax({
        url: "/alamatsearch",
        data: {
          cust: cust,
          shipto: shipto,
        },
        success: function(data) {
          console.log(data);
          document.getElementById('e_address').value = data.trim();
        }
      })
    });

    $(document).on('click', '.confmodal', function(e) {
      var sonbr = $(this).data('sonbr');
      var cust = $(this).data('cust');
      var desc = $(this).data('custdesc');
      var alamat = $(this).data('alamat');
      var duedate = $(this).data('duedate');
      var shipto = $(this).data('shipto');
      var brelname = $(this).data('brelname');

      document.getElementById('edw_sonbr').value = sonbr;
      document.getElementById('edw_custcode').value = cust + ' - ' + desc;
      document.getElementById('edw_addresscust').value = alamat;
      document.getElementById('edw_duedate').value = duedate;
      document.getElementById('edw_shipto').value = shipto;
      document.getElementById('h_edw_sonbr').value = sonbr;
      document.getElementById('edw_brelname').value = cust + ' - ' + brelname;


      $.ajax({
        url: "/detailsalescons",
        data: {
          sonbr: sonbr,
        },
        success: function(data) {
          console.log(data);
          $('#edw_detailapp').html('');
          $('#edw_detailapp').html(data);
        }
      })

      $.ajax({
        url: "/alamatsearch",
        data: {
          cust: cust,
          shipto: shipto,
        },
        success: function(data) {
          console.log(data);
          document.getElementById('edw_address').value = data.trim();
        }
      })
    });


    $(document).on('click', '.editmodal', function(e) {
      var sonbr = $(this).data('sonbr');
      var cust = $(this).data('cust');
      var desc = $(this).data('custdesc');
      var alamat = $(this).data('alamat');
      var duedate = $(this).data('duedate');
      var shipto = $(this).data('shipto');

      document.getElementById('ed_sonbr').value = sonbr;
      document.getElementById('ed_custcode').value = cust + ' - ' + desc;
      document.getElementById('ed_addresscust').value = alamat;
      document.getElementById('ed_duedate').value = duedate;
      document.getElementById('ed_shipto').value = shipto;


      $.ajax({
        url: "/detaileditcons",
        data: {
          sonbr: sonbr,
        },
        success: function(data) {
          console.log(data);
          $('#ed_detailapp').html('');
          $('#ed_detailapp').html(data);
        }
      })

      $.ajax({
        url: "/alamatsearch",
        data: {
          cust: cust,
          shipto: shipto,
        },
        success: function(data) {
          console.log(data);
          document.getElementById('ed_address').value = data.trim();
        }
      })
    });

    $(document).on('click', '.deletemodal', function(e){ 
      var sonbr = $(this).data('sonbr');

      document.getElementById('d_sonbr').innerHTML = sonbr;
      document.getElementById('de_sonbr').value = sonbr;
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

    $(document).on('submit', '#new,#edited,#conf', function(e) {
      document.getElementById('btnconf').style.display = 'none';
      document.getElementById('btnclosem').style.display = 'none';
      document.getElementById('btnloading').style.display = '';     
      document.getElementById('ed_btnconf').style.display = 'none';
      document.getElementById('ed_btnclosem').style.display = 'none';
      document.getElementById('ed_btnloading').style.display = '';   
      document.getElementById('edw_btnconf').style.display = 'none';
      document.getElementById('edw_btnclose').style.display = 'none';
      document.getElementById('edw_btnloading').style.display = '';   
    });
    
    $(document).on('change', '#custcode', function(e) {

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
            var item = $(this).val();

            $.ajax({
                url:"/getlistum",
                data:{
                  item : item,
                },
                success:function(data){
                    console.log(data);
                    um.html('').append(data);
                }
            })
    });

	  function fetch_data(page, sort_type, sort_by, sonumber, customer, totalstart, totalto, duedatefrom, duedateto) {
	    $.ajax({
	      url: "/socons/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&sonumber=" + sonumber + "&customer=" + customer + "&totalstart=" + totalstart + "&totalto=" + totalto + "&duedatefrom=" + duedatefrom + "&duedateto=" + duedateto,
	      success: function(data) {
    		console.log(data);
    		$('tbody').html('');
    		$('tbody').html(data);

	      }
	    })
	  }

	  $(document).on('click', '.newUser', function(event) {
      		$("#detailapp").html('');
    });

	  $(document).on('click', '#btnsearch', function() {
	    var sonumber = $('#s_sonumber').val();
	    var customer = $('#s_customer').val();
	    var totalstart = $('#s_totalstart').val();
	    var totalto = $('#s_totalto').val();
	    var duedatefrom = $('#datefrom').val();
	    var duedateto = $('#dateto').val();
	    var column_name = $('#hidden_column_name').val();
	    var sort_type = $('#hidden_sort_type').val();
	    var page = 1;


      document.getElementById('sonumbertemp').value = sonumber;
      document.getElementById('customertemp').value = customer;
      document.getElementById('totalstarttemp').value = totalstart;
      document.getElementById('totaltotemp').value = totalto;
      document.getElementById('datefromtemp').value = duedatefrom;
      document.getElementById('datetotemp').value = duedateto;


	    fetch_data(page, sort_type, column_name, sonumber, customer, totalstart, totalto, duedatefrom, duedateto);
	  });

	  $(document).on('click', '.pagination a', function(event) {
	    event.preventDefault();
	    var page = $(this).attr('href').split('page=')[1];
	    $('#hidden_page').val(page);
	    var column_name = $('#hidden_column_name').val();
	    var sort_type = $('#hidden_sort_type').val();
	    var sonumber = $('#sonumbertemp').val();
	    var customer = $('#customertemp').val();
	    var totalstart = $('#totalstarttemp').val();
	    var totalto = $('#totaltotemp').val();
	    var duedatefrom = $('#datefromtemp').val();
	    var duedateto = $('#datetotemp').val();

	    fetch_data(page, sort_type, column_name, sonumber, customer, totalstart, totalto, duedatefrom, duedateto);
	  });

    $(document).on('click', '#ed_addrow', function() {

      var newRow = $("<tr>");
      var cols = "";


      cols += '<td data-label="Barang">';
      cols += '<select id="barang" class="form-control barang selectpicker" data-live-search="true" name="itemcode[]" required autofocus>';
      cols += '<option value = ""> -- Select Data -- </option>'
      @foreach($itemedit as $item)
      cols += '<option value="{{$item->itemcode}}"> {{$item->itemcode}} -- {{$item->itemdesc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="qtyso[]" data-label="Jumlah"><input type="number" class="form-control" autocomplete="off" name="qtyso[]" style="height:37px" required min="0" step="0.01" required/></td>';

      cols += '<td data-label="UM">';
      cols += '<select id="um" class="form-control um" name="um[]" required autofocus>';
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="Action" style="vertical-align:middle;text-align:center;"><input type="button" class="ibtnDel btn btn-danger btn-focus"  value="Delete"></td>';

      cols += '<input type="hidden" name="delLine[]" value="A">';

      cols += '</tr>'
      newRow.append(cols);
      $("#ed_detailapp").append(newRow);

      selectRefresh();
  });

  });
</script>
@endsection

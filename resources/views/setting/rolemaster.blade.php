@extends('layout.newlayout')
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Role Master</h1>
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
<div class="col-12">
  <button class="btn bt-action newUser" data-toggle="modal" data-target="#createrole">
    Create Role</button>
    <hr>
</div>

<div class="col-11 form-group row">

  <!--FORM Search Disini -->
  <label for="s_rolecode" class="col-md-3 col-sm-2 col-form-label text-md-right">{{ __('Role Code') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_rolecode" type="text" class="form-control" name="s_rolecode" value="" autofocus autocomplete="off">
  </div>
</div>
<div class="col-11 form-group row">

  <label for="s_roledesc" class="col-md-3 col-sm-2 col-form-label text-md-right">{{ __('Role Desc') }}</label>
  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input id="s_roledesc" type="text" class="form-control" name="s_roledesc" value="" autofocus autocomplete="off" min="0">
  </div>

  <div class="col-md-4 col-sm-4 mb-2 input-group">
    <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:right" />
  </div>
</div>

<div class="col-md-12">
  <hr>
</div>

<input type="hidden" id="rolecodetemp" value=""/>
<input type="hidden" id="roledesctemp" value=""/>


<!--Table Menu-->
<div class="table-responsive offset-lg-1 col-lg-10 col-md-12">
  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
        <th width=30% class="sorting" data-sorting_type="asc" data-column_name="role_code" style="cursor: pointer">Role Code</th>
        <th width=30% class="sorting" data-sorting_type="asc" data-column_name="role_desc" style="cursor: pointer">Role Description</th>
        <th width=30% class="sorting" data-sorting_type="asc" data-column_name="salesman" style="cursor: pointer">Salesman</th>
        <th width="10%">Action</th>

      </tr>
    </thead>
    <tbody>
      @include('setting.table-rolemaster')
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="role_code" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>


<!--Create Modal-->
<div class="modal fade createrole" id='createrole' tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg " role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Create Role</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form class="form-horizontal" method="POST" id='new' action="/createrole" onkeydown="return event.key != 'Enter';">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group row col-md-12">
            <label for="role_code" class="col-md-3 col-form-label text-md-right">Role Code</label>
            <div class="col-md-4 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <input id="role_code" type="text" class="form-control" name="role_code" value="{{ old('role_code') }}" autocomplete="off" maxlength="8" required autofocus>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="role_desc" class="col-md-3 col-form-label text-md-right">Role Description</label>
            <div class="col-md-4">
              <input id="role_desc" type="text" class="form-control" name="role_desc" value="{{ old('role_desc') }}" autocomplete="off" maxlength="50" autofocus required>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="salesman" class="col-md-3 col-form-label text-md-right">Salesman</label>
            <div class="col-md-4">
            <label class="switch" for="slsmn" required>
                <input type="checkbox" id="slsmn" name="slsmn" value="Y" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group">
            
          <h3>
              <center><strong>Menu Access</strong></center>
            </h3>
            <br/>
            <h4>
              <center><strong>Transaksi</strong></center>
            </h4>
            <hr>
            <h5>
              <center><strong>End Of Day Process</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
              <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('EOD Process') }}</label>
              <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('EOD Process') }}</label>
              <div class="col-6">
                <label class="switch" for="cbeodp">
                  <input type="checkbox" id="cbeodp" name="cbeodp" value="TS01" />
                  <div class="slider round"></div>
                </label>
              </div>
            </div>
            <div class="form-group row">
              <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('PO EOD') }}</label>
              <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('PO EOD') }}</label>
              <div class="col-6">
                <label class="switch" for="cbpoeod">
                  <input type="checkbox" id="cbpoeod" name="cbpoeod" value="TS12" />
                  <div class="slider round"></div>
                </label>
              </div>
            </div>
            <hr>
            <h5>
              <center><strong>PO Receipt</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('PO Receipt') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('PO Receipt') }}</label>
            <div class="col-6">
              <label class="switch" for="cbpor">
                <input type="checkbox" id="cbpor" name="cbpor" value="TS02" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <hr>
            <h5>
              <center><strong>Salesman</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Salesman Activity') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Salesman Activity') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsa">
                <input type="checkbox" id="cbsa" name="cbsa" value="TK01" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
            <div class="form-group row">
              <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO SALES') }}</label>
              <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO SALES') }}</label>
              <div class="col-6">
                <label class="switch" for="cbsosales">
                  <input type="checkbox" id="cbsosales" name="cbsosales" value="TS06" />
                  <div class="slider round"></div>
                </label>
              </div>
            </div>
          <hr>
            <h5>
              <center><strong>Shipment</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SPB Maintenance') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SPB Maintenance ') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsosc">
                <input type="checkbox" id="cbsosc" name="cbsosc" value="TS03" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SPB Create') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SPB Create') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsj">
                <input type="checkbox" id="cbsj" name="cbsj" value="TS04" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Stock SPB') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Stock SPB') }}</label>
            <div class="col-6">
              <label class="switch" for="cbstockspb">
                <input type="checkbox" id="cbstockspb" name="cbstockspb" value="TS13" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SPB View Only') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SPB View Only') }}</label>
            <div class="col-6">
              <label class="switch" for="cbspbview">
                <input type="checkbox" id="cbspbview" name="cbspbview" value="TSV" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <hr>
            <h5>
              <center><strong>SO</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO SAD') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO SAD') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsosad">
                <input type="checkbox" id="cbsosad" name="cbsosad" value="TS05" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO Onhold ') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO Onhold ') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsooa">
                <input type="checkbox" id="cbsooa" name="cbsooa" value="TS07" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO Retur Browse') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO Retur Browse') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsorbrow">
                <input type="checkbox" id="cbsorbrow" name="cbsorbrow" value="TS08" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO Consign') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO Consign') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsoc">
                <input type="checkbox" id="cbsoc" name="cbsoc" value="TS09" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO View Only') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO View Only') }}</label>
            <div class="col-6">
              <label class="switch" for="cbviewso">
                <input type="checkbox" id="cbviewso" name="cbviewso" value="TS10" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>

 	        <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Sales Activity Browse') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Sales Activity Browse') }}</label>
            <div class="col-6">
              <label class="switch" for="cbsab">
                <input type="checkbox" id="cbsab" name="cbsab" value="TK02" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>

          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Checking SO QAD') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Checking SO QAD') }}</label>
            <div class="col-6">
              <label class="switch" for="cbcheckso">
                <input type="checkbox" id="cbcheckso" name="cbcheckso" value="TS11" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
            <br/>

            <h4>
              <center><strong>Master</strong></center>
            </h4>
            <hr>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('User Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('User Master') }}</label>
            <div class="col-6">
              <label class="switch" for="cbUser">
                <input type="checkbox" id="cbUser" name="cbUser" value="MT01" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Role Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Role Master') }}</label>
            <div class="col-6">
              <label class="switch" for="cbRole">
                <input type="checkbox" id="cbRole" name="cbRole" value="MT02" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Site Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Site Master') }}</label>
            <div class="col-6">
              <label class="switch" for="cbSite">
                <input type="checkbox" id="cbSite" name="cbSite" value="MT03" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Item Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Item Master') }}</label>
            <div class="col-6">
              <label class="switch" for="cbItem">
                <input type="checkbox" id="cbItem" name="cbItem" value="MT04" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Supplier Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Supplier Master') }}</label>
            <div class="col-6">
              <label class="switch" for="cbSupplier">
                <input type="checkbox" id="cbSupplier" name="cbSupplier" value="MT05" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Customer Master') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Customer Master') }}</label>
          <div class="col-6">
            <label class="switch" for="cbCustomer">
              <input type="checkbox" id="cbCustomer" name="cbCustomer" value="MT06" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Customer Relation') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Cust Relation') }}</label>
          <div class="col-6">
            <label class="switch" for="cbCustomerRelation">
              <input type="checkbox" id="cbCustomerRelation" name="cbCustomerRelation" value="MT07" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Activity Master') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Activity Master') }}</label>
          <div class="col-6">
            <label class="switch" for="cbActivity">
              <input type="checkbox" id="cbActivity" name="cbActivity" value="MT08" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Customer ST') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Customer ST') }}</label>
          <div class="col-6">
            <label class="switch" for="cbCustomerST">
              <input type="checkbox" id="cbCustomerST" name="cbCustomerST" value="MT09" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Item Konversi') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Item Konversi') }}</label>
          <div class="col-6">
            <label class="switch" for="cbItemKonv">
              <input type="checkbox" id="cbItemKonv" name="cbItemKonv" value="MT10" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Approval Level MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Approval Level MT') }}</label>
          <div class="col-6">
            <label class="switch" for="cbapprovallmt">
              <input type="checkbox" id="cbapprovallmt" name="cbapprovallmt" value="MT11" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Location MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Location MT') }}</label>
          <div class="col-6">
            <label class="switch" for="cblocationmt">
              <input type="checkbox" id="cblocationmt" name="cblocationmt" value="MT12" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Running Number MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Running Number MT') }}</label>
          <div class="col-6">
            <label class="switch" for="cbrunningnumber">
              <input type="checkbox" id="cbrunningnumber" name="cbrunningnumber" value="MT13" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Item Parent Child MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Item Parent Child MT') }}</label>
          <div class="col-6">
            <label class="switch" for="cbpchildmt">
              <input type="checkbox" id="cbpchildmt" name="cbpchildmt" value="MT14" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Inventory') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Inventory') }}</label>
          <div class="col-6">
            <label class="switch" for="cbinventory">
              <input type="checkbox" id="cbinventory" name="cbinventory" value="IV01" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Dashboard') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Dashboard') }}</label>
          <div class="col-6">
            <label class="switch" for="cbdashboard">
              <input type="checkbox" id="cbdashboard" name="cbdashboard" value="HO01" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>


        <div class="modal-footer">
          <button type="button" class="btn btn-info bt-action" id="btnclose" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success bt-action" id="btnconf">Save</button>
          <button type="button" class="btn bt-action" id="btnloading" style="display:none">
            <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
          </button>
        </div>
    </div>
    </form>
  </div>
</div>


<!--Modal Edit-->
<div class="modal fade" id="editModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Edit Role</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="POST" id='newedit' action="editrole" onkeydown="return event.key != 'Enter';">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="form-group row col-md-12">
            <label for="role_code" class="col-md-5 col-form-label text-md-right">Role Code</label>
            <div class="col-md-7">
              <input id="e_rolecode" type="text" class="form-control" name="e_rolecode" value="{{ old('e_rolecode') }}" autocomplete="off" maxlength="50" readonly autofocus required>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="role_desc" class="col-md-5 col-form-label text-md-right">Role Description</label>
            <div class="col-md-7">
              <input id="e_roledesc" type="text" class="form-control" name="role_desc" value="{{ old('e_roledesc') }}" autocomplete="off" maxlength="50" autofocus required>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="salesman" class="col-md-5 col-form-label text-md-right">Salesman</label>
            <div class="col-md-7">
            <label class="switch" for="e_slsmn" required>
                <input type="checkbox" id="e_slsmn" name="e_slsmn" value="Y" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group">
            <h3>
              <center><strong>Menu Access</strong></center>
            </h3>
            <br/>
            <h4>
              <center><strong>Transaksi</strong></center>
            </h4>
            <hr>
            <h5>
              <center><strong>End Of Day Process</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
              <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('EOD Process') }}</label>
              <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('EOD Process') }}</label>
              <div class="col-6">
                <label class="switch" for="e_cbeodp">
                  <input type="checkbox" id="e_cbeodp" name="e_cbeodp" value="TS01" />
                  <div class="slider round"></div>
                </label>
              </div>
            </div>
            <div class="form-group row">
              <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('PO EOD') }}</label>
              <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('PO EOD') }}</label>
              <div class="col-6">
                <label class="switch" for="e_cbpoeod">
                  <input type="checkbox" id="e_cbpoeod" name="e_cbpoeod" value="TS12" />
                  <div class="slider round"></div>
                </label>
              </div>
            </div>
            <hr>
            <h5>
              <center><strong>PO Receipt</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('PO Receipt') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('PO Receipt') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbpor">
                <input type="checkbox" id="e_cbpor" name="e_cbpor" value="TS02" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <hr>
            <h5>
              <center><strong>Salesman</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Salesman Activity') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Salesman Activity') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsa">
                <input type="checkbox" id="e_cbsa" name="e_cbsa" value="TK01" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO SALES') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO SALES') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsosales">
                <input type="checkbox" id="e_cbsosales" name="e_cbsosales" value="TS06" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <hr>
            <h5>
              <center><strong>Shipment</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SPB Maintenance') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SPB Maintenance') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsosc">
                <input type="checkbox" id="e_cbsosc" name="e_cbsosc" value="TS03" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SPB Create') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SPB Create') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsj">
                <input type="checkbox" id="e_cbsj" name="e_cbsj" value="TS04" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Stock SPB') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Stock SPB') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbstockspb">
                <input type="checkbox" id="e_cbstockspb" name="e_cbstockspb" value="TS13" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SPB View Only') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SPB View Only') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbspbview">
                <input type="checkbox" id="e_cbspbview" name="e_cbspbview" value="TSV" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <hr>
            <h5>
              <center><strong>SO</strong></center>
            </h5>
            <hr>
            <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO SAD') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO SAD') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsosad">
                <input type="checkbox" id="e_cbsosad" name="e_cbsosad" value="TS05" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO Onhold Approval') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO Onhold Approval') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsooa">
                <input type="checkbox" id="e_cbsooa" name="e_cbsooa" value="TS07" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO Retur Browse') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO Retur Browse') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsorbrow">
                <input type="checkbox" id="e_cbsorbrow" name="e_cbsorbrow" value="TS08" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO Consign') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO Consign') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsoc">
                <input type="checkbox" id="e_cbsoc" name="e_cbsoc" value="TS09" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('SO View Only') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('SO View Only') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbviewso">
                <input type="checkbox" id="e_cbviewso" name="e_cbviewso" value="TS10" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>

	        <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Sales Activity Browse') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Sales Activity Browse') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbsab">
                <input type="checkbox" id="e_cbsab" name="e_cbsab" value="TK02" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>

          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Checking SO QAD') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Checking SO QAD') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbcheckso">
                <input type="checkbox" id="e_cbcheckso" name="e_cbcheckso" value="TS11" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>

            <br/>
            <h3>
              <center><strong>Master</strong></center>
            </h3>
            <hr>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('User Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('User Master') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbUser">
                <input type="checkbox" id="e_cbUser" name="e_cbUser" value="MT01" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Role Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Role Master') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbRole">
                <input type="checkbox" id="e_cbRole" name="e_cbRole" value="MT02" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Site Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Site Master') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbSite">
                <input type="checkbox" id="e_cbSite" name="e_cbSite" value="MT03" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Item Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Item Master') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbItem">
                <input type="checkbox" id="e_cbItem" name="e_cbItem" value="MT04" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
          <div class="form-group row">
            <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Supplier Master') }}</label>
            <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Supplier Master') }}</label>
            <div class="col-6">
              <label class="switch" for="e_cbSupplier">
                <input type="checkbox" id="e_cbSupplier" name="e_cbSupplier" value="MT05" />
                <div class="slider round"></div>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Customer Master') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Customer Master') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbCustomer">
              <input type="checkbox" id="e_cbCustomer" name="e_cbCustomer" value="MT06" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Cust Relation') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Cust Relation') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbCustomerRelation">
              <input type="checkbox" id="e_cbCustomerRelation" name="e_cbCustomerRelation" value="MT07" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Activity Master') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Activity Master') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbActivity">
              <input type="checkbox" id="e_cbActivity" name="e_cbActivity" value="MT08" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Customer ST') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Customer ST') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbCustomerST">
              <input type="checkbox" id="e_cbCustomerST" name="e_cbCustomerST" value="MT09" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Item Konversi') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Item Konversi') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbItemKonv">
              <input type="checkbox" id="e_cbItemKonv" name="e_cbItemKonv" value="MT10" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Approval Level MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Approval Level MT') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbapprovallmt">
              <input type="checkbox" id="e_cbapprovallmt" name="e_cbapprovallmt" value="MT11" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Location MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Location MT') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbLocation">
              <input type="checkbox" id="e_cbLocation" name="e_cbLocation" value="MT12" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Running Number MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Running Number MT') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbrunningnumber">
              <input type="checkbox" id="e_cbrunningnumber" name="e_cbrunningnumber" value="MT13" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Item Parent Child MT') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Item Parent Child MT') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbpchildmt">
              <input type="checkbox" id="e_cbpchildmt" name="e_cbpchildmt" value="MT14" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Inventory') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Inventory') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbinventory">
              <input type="checkbox" id="e_cbinventory" name="e_cbinventory" value="IV01" />
              <div class="slider round"></div>
            </label>
          </div>
        </div>
        <div class="form-group row">
          <label for="level" class="col-6 col-form-label text-right full-txt">{{ __('Dashboard') }}</label>
          <label for="level" class="col-6 col-form-label text-right min-txt">{{ __('Dashboard') }}</label>
          <div class="col-6">
            <label class="switch" for="e_cbdashboard">
              <input type="checkbox" id="e_cbdashboard" name="e_cbdashboard" value="HO01" />
              <div class="slider round"></div>
            </label>
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
<div class="modal fade" id="deleterole" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Delete Role</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form class="form-horizontal" method="post" action="deleterole">
        {{ csrf_field() }}

        <div class="modal-body">
          <input type="hidden" name="tmp_rolecode" id="tmp_rolecode">
          Anda yakin ingin menghapus role <b> <span id="d_rolecode"></span> -- <span id="d_roledesc"></span> </b> ?
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
<script type="text/javascript">
  $("table.order-list").on("click", ".ibtnDel", function(event) {
    $(this).closest("tr").remove();
    counter -= 1
  });

  $("#new").submit(function(e) {
    document.getElementById('btnclose').style.display = 'none';
    document.getElementById('btnconf').style.display = 'none';
    document.getElementById('btnloading').style.display = '';
  });

  $("#newedit").submit(function() {
    document.getElementById('e_btnclose').style.display = 'none';
    document.getElementById('e_btnconf').style.display = 'none';
    document.getElementById('e_btnloading').style.display = '';
  });

  $("#delete").submit(function() {
    document.getElementById('d_btnclose').style.display = 'none';
    document.getElementById('d_btnconf').style.display = 'none';
    document.getElementById('d_btnloading').style.display = '';
  });

  $(document).on('click', '.deleterole', function() {

    var role_code = $(this).data('rolecode');
    var role_desc = $(this).data('roledesc');

    document.getElementById('d_roledesc').innerHTML = role_desc;
    document.getElementById('d_rolecode').innerHTML = role_code;
    document.getElementById('tmp_rolecode').value = role_code;
  });

  $(document).on('click', '.editrole', function() {

    var rolecode = $(this).data('role_code');
    var roledesc = $(this).data('role_desc');
    var access = $(this).data('menu_access');

    document.getElementById('e_rolecode').value = rolecode;
    document.getElementById('e_roledesc').value = roledesc;

    $.ajax({
      type: "get",
      url: "{{URL::to("menugetrole")}}",
      data: {
        search: rolecode,
      },
      success: function(data) {
        //console.log(data);
        var listmenu = data;
        
        if(listmenu.search("Y")>=0){
          document.getElementById('e_slsmn').checked = true;
        }
        else{
          document.getElementById('e_slsmn').checked = false;
        }
        if (listmenu.search("MT01") >= 0) {
          document.getElementById('e_cbUser').checked = true;
        } else {
          document.getElementById('e_cbUser').checked = false;
        }

        if (listmenu.search("MT02") >= 0) {
          document.getElementById('e_cbRole').checked = true;
        } else {
          document.getElementById('e_cbRole').checked = false;
        }

        if (listmenu.search("MT03") >= 0) {
          document.getElementById('e_cbSite').checked = true;
        } else {
          document.getElementById('e_cbSite').checked = false;
        }

        if (listmenu.search("MT04") >= 0) {
          document.getElementById('e_cbItem').checked = true;
        } else {
          document.getElementById('e_cbItem').checked = false;
        }

        if (listmenu.search("MT05") >= 0) {
          document.getElementById('e_cbSupplier').checked = true;
        } else {
          document.getElementById('e_cbSupplier').checked = false;
        }

        if (listmenu.search("MT06") >= 0) {
          document.getElementById('e_cbCustomer').checked = true;
        } else {
          document.getElementById('e_cbCustomer').checked = false;
        }

        if (listmenu.search("MT07") >= 0) {
          document.getElementById('e_cbCustomerRelation').checked = true;
        } else {
          document.getElementById('e_cbCustomerRelation').checked = false;
        }

        if (listmenu.search("MT08") >= 0) {
          document.getElementById('e_cbActivity').checked = true;
        } else {
          document.getElementById('e_cbActivity').checked = false;
        }

        if (listmenu.search("MT09") >= 0) {
          document.getElementById('e_cbCustomerST').checked = true;
        } else {
          document.getElementById('e_cbCustomerST').checked = false;
        }

        if (listmenu.search("MT10") >= 0) {
          document.getElementById('e_cbItemKonv').checked = true;
        } else {
          document.getElementById('e_cbItemKonv').checked = false;
        }

        if (listmenu.search("MT11") >= 0) {
          document.getElementById('e_cbapprovallmt').checked = true;
        } else {
          document.getElementById('e_cbapprovallmt').checked = false;
        }

        if (listmenu.search("MT12") >= 0) {
          document.getElementById('e_cbLocation').checked = true;
        } else {
          document.getElementById('e_cbLocation').checked = false;
        }

        if (listmenu.search("MT13") >= 0) {
          document.getElementById('e_cbrunningnumber').checked = true;
        } else {
          document.getElementById('e_cbrunningnumber').checked = false;
        }

        if (listmenu.search("MT14") >= 0) {
          document.getElementById('e_cbpchildmt').checked = true;
        } else {
          document.getElementById('e_cbpchildmt').checked = false;
        }

        if (listmenu.search("TS01") >= 0) {
          document.getElementById('e_cbeodp').checked = true;
        } else {
          document.getElementById('e_cbeodp').checked = false;
        }

        if (listmenu.search("TS02") >= 0) {
          document.getElementById('e_cbpor').checked = true;
        } else {
          document.getElementById('e_cbpor').checked = false;
        }

        if (listmenu.search("TS03") >= 0) {
          document.getElementById('e_cbsosc').checked = true;
        } else {
          document.getElementById('e_cbsosc').checked = false;
        }

        if (listmenu.search("TS04") >= 0) {
          document.getElementById('e_cbsj').checked = true;
        } else {
          document.getElementById('e_cbsj').checked = false;
        }

        if (listmenu.search("TS05") >= 0) {
          document.getElementById('e_cbsosad').checked = true;
        } else {
          document.getElementById('e_cbsosad').checked = false;
        }

        if (listmenu.search("TS06") >= 0) {
          document.getElementById('e_cbsosales').checked = true;
        } else {
          document.getElementById('e_cbsosales').checked = false;
        }

        if (listmenu.search("TS07") >= 0) {
          document.getElementById('e_cbsooa').checked = true;
        } else {
          document.getElementById('e_cbsooa').checked = false;
        }

        if (listmenu.search("TS08") >= 0) {
          document.getElementById('e_cbsorbrow').checked = true;
        } else {
          document.getElementById('e_cbsorbrow').checked = false;
        }
        if (listmenu.search("TS09") >= 0) {
          document.getElementById('e_cbsoc').checked = true;
        } else {
          document.getElementById('e_cbsoc').checked = false;
        }
        if (listmenu.search("TS10") >= 0) {
          document.getElementById('e_cbspbview').checked = true;
        } else {
          document.getElementById('e_cbspbview').checked = false;
        }
        if (listmenu.search("TS11") >= 0) {
          document.getElementById('e_cbcheckso').checked = true;
        } else {
          document.getElementById('e_cbcheckso').checked = false;
        }
        if (listmenu.search("TS12") >= 0) {
          document.getElementById('e_cbpoeod').checked = true;
        } else {
          document.getElementById('e_cbpoeod').checked = false;
        }
        if (listmenu.search("TS13") >= 0) {
          document.getElementById('e_cbstockspb').checked = true;
        } else {
          document.getElementById('e_cbstockspb').checked = false;
        }
        if (listmenu.search("TSV") >= 0) {
          document.getElementById('e_cbviewso').checked = true;
        } else {
          document.getElementById('e_cbviewso').checked = false;
        }

        if (listmenu.search("TK01") >= 0) {
          document.getElementById('e_cbsa').checked = true;
        } else {
          document.getElementById('e_cbsa').checked = false;
        }
	     if (listmenu.search("TK02") >= 0) {
          document.getElementById('e_cbsab').checked = true;
        } else {
          document.getElementById('e_cbsab').checked = false;
        }
       if (listmenu.search("IV01") >= 0) {
          document.getElementById('e_cbinventory').checked = true;
        } else {
          document.getElementById('e_cbinventory').checked = false;
        }


       if (listmenu.search("HO01") >= 0) {
          document.getElementById('e_cbdashboard').checked = true;
        } else {
          document.getElementById('e_cbdashboard').checked = false;
        }


      }
    });

    // flag tunggu semua menu
  });

  function clear_icon() {
    $('#id_icon').html('');
    $('#post_title_icon').html('');
  }

  function fetch_data(page, sort_type, sort_by, rolecode, roledesc) {
    $.ajax({
      url: "/rolemaster/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&rolecode=" + rolecode + "&roledesc=" + roledesc,
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      }
    })
  }


  
  $(document).on('click', '#btnsearch', function() {
    var rolecode = $('#s_rolecode').val(); //tambahan
    var roledesc = $('#s_roledesc').val(); //tambahan
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = $('#hidden_page').val();


    document.getElementById("rolecodetemp").value =rolecode;
    document.getElementById("roledesctemp").value =roledesc;
    

    fetch_data(page, sort_type, column_name, rolecode, roledesc);
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
    var rolecode = $('#s_rolecode').val(); 
    var roledesc = $('#s_roledesc').val(); 
    fetch_data(page, reverse_order, column_name, rolecode, roledesc);
  });

  
  $(document).on('click', '.pagination a', function(event) {
    event.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    $('#hidden_page').val(page);
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();

    var rolecode = $('#rolecodetemp').val(); 
    var roledesc = $('#roledesctemp').val(); 
    fetch_data(page, sort_type, column_name, rolecode, roledesc);

  });
</script>
@endsection

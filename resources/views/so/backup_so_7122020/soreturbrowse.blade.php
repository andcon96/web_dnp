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

<div class="col-12">
  <button class="btn bt-action newUser" data-toggle="modal" data-target="#createModal">
    Create SO Return</button>
</div>
<div class="col-md-12"><hr></div>
<!--SEARCHING RETUR BROWSER-->
<div class="form-group row col-md-12">
  <label for="s_returnbr" class="col-md-2 col-form-label text-md-right">{{ __('SO Return Number') }}</label>
  <div class="col-md-3">
    <input id="s_returnbr" type="text" class="form-control" name="s_returnbr" value="" autofocus autocomplete="off">
  </div>
  <label for="s_sonumber" class="col-md-2 col-form-label text-md-right">{{ __('SO Number') }}</label>
  <div class="col-md-3">
    <input id="s_sonumber" type="text" class="form-control" name="s_sonumber" value="" autofocus autocomplete="off">
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="s_customer" class="col-md-2 col-form-label text-md-right">{{ __('Customer') }}</label>
  <div class="col-md-3">
    <input id="s_customer" type="text" min="0" class="form-control" name="s_customer" autofocus autocomplete="off">
  </div>

  <label for="s_site" class="col-md-2 col-form-label text-md-right">{{ __('Site') }}</label>
  <div class="col-md-3">
    <input id="s_site" type="text" min="0" class="form-control" name="s_site" autofocus autocomplete="off">
  </div>
</div>

<div class="form-group row col-md-12">
  <label for="s_shipto" class="col-md-2 col-form-label text-md-right">{{ __('Ship To') }}</label>
  <div class="col-md-3 col-lg-3">
    <input type="text" id="s_shipto" class="form-control" name='s_shipto' required autofocus autocomplete="off">
  </div>

  <label for="s_status" class="col-md-3 col-form-label text-md-right">{{ __('') }}</label>
  <div class="offset-0">
    <input type="button" class="btn bt-action newUser" id="btnsearch" value="Search" />
    <!-- <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-refresh"></i></button> -->
  </div>
</div>

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
        <th>SO Number</th>
        <th>Remarks</th>
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
          <label for="e_customer" class="col-md-3 col-form-label text-md-right">Customer</label>
          <div class="col-md-6">
            <input id="e_customer" type="text" class="form-control" name="e_customer" value="{{ old('e_customer') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_shipto" class="col-md-3 col-form-label text-md-right">Ship To</label>
          <div class="col-md-6">
            <input id="e_shipto" type="text" class="form-control" name="e_shipto" value="{{ old('e_shipto') }}" autocomplete="off" maxlength="24" autofocus required readonly>
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
        <form class="form-horizontal" method="POST" action="/createsoretur" onkeydown="return event.key != 'Enter';">
          {{ csrf_field() }}

          <div class="form-group row col-md-12">
            <label for="custcode" class="col-md-2 col-form-label text-md-right">Customer</label>
            <div class="col-md-3 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <select id="custcode" name="custcode" class="form-control" required>
                <option value="">-- Select Data --</option>
                @foreach($customer as $cust)
                <option value="{{$cust->cust_code}}">{{$cust->cust_code}} -- {{$cust->cust_desc}}</option>
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
            <label for="address" class="col-md-2 col-form-label text-md-right">Address</label>
            <div class="col-md-8">
              <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="remarks" class="col-md-2 col-form-label text-md-right">Remarks</label>
            <div class="col-md-8">
              <input id="remarks" type="text" class="form-control" name="remarks" autocomplete="off" maxlength="24" autofocus>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="duedate" class="col-md-2 col-form-label text-md-right">Due Date</label>
            <div class="col-md-3">
              <input id="duedate" type="text" class="form-control" name="duedate" autocomplete="off" maxlength="24" autofocus placeholder="YYYY-MM-DD" required>
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
                  <td colspan="5">
                    <input type="button" class="btn btn-lg btn-block btn-focus" id="addrow" value="Add Row" style="background-color:#1234A5; color:white; font-size:16px" />
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


@endsection


@section('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    var counter = 0;

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

    function selectRefresh() {
      $('.barang').select2({
        tags: true,
        placeholder: "Select an Option",
        width: '100%'
      });
      $('.barang').select2('open');
    }

    $("#addrow").on("click", function() {

      var newRow = $("<tr>");
      var cols = "";


      cols += '<td data-label="Barang">';
      cols += '<select id="barang" class="form-control barang" name="barang[]" required autofocus>';
      cols += '<option value = ""> -- Select Data -- </option>'
      @foreach($item as $item)
      cols += '<option value="{{$item->itemcode}}"> {{$item->itemcode}} -- {{$item->itemdesc}} </option>';
      @endforeach
      cols += '</select>';
      cols += '</td>';

      cols += '<td data-title="jumlah[]" data-label="Jumlah"><input type="number" class="form-control" autocomplete="off" name="jumlah[]" style="height:37px" required min="1"/></td>';

      cols += '<td data-title="um[]" data-label="Satuan"><input type="text" class="form-control um" autocomplete="off" name="um[]" style="height:37px" min="1" step="1" required readonly/></td>';
      cols += '<td data-title="loc[]" data-label="Satuan"><input type="text" class="form-control loc" autocomplete="off" name="loc[]" style="height:37px" min="1" step="1" required readonly/></td>';

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

    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      $('#hidden_page').val(page);
      var column_name = $('#hidden_column_name').val();
      var sort_type = $('#hidden_sort_type').val();

      fetch_data(page, sort_type, column_name);
    });

    $("#custcode").on("change", function(e) {

      var cust = document.getElementById('custcode').value;
      var custdesc = $("#custcode option:selected").text();;
      var i = 0;
      var toAppend = '';

      $.ajax({
        url: "/alamatsearch",
        data: {
          cust: cust,
        },
        success: function(data) {
          console.log(data);
          document.getElementById('address').value = data.trim();
        }
      })

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


        }
      })
    });

    $(document).on('change', '.barang', function() {
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
          loc.val($.trim(newdata[1]));
        }
      })
    });

  });

  function fetch_data(page, sort_type, sort_by, returnumber, sonumber, site, customer, shipto) {
    $.ajax({
      url: "/soretur/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&returnumber=" + returnumber + "&sonumber=" + sonumber + "&site=" + site + "&customer=" + customer + "&shipto=" + shipto,
      success: function(data) {
        console.log(data);
        $('tbody').html('');
        $('tbody').html(data);
      }
    })
  }

  $(document).on('click', '#btnsearch', function() {
    var returnumber = $('#s_returnbr').val();
    var sonumber = $('#s_sonumber').val();
    var site = $('#s_site').val();
    var customer = $('#s_customer').val();
    var shipto = $('#s_shipto').val();
    var column_name = $('#hidden_column_name').val();
    var sort_type = $('#hidden_sort_type').val();
    var page = $('#hidden_page').val();

    fetch_data(page, sort_type, column_name, returnumber, sonumber, site, customer, shipto);
  });

  $(document).on('click', '.viewmodal', function() {
    var sonbr = $(this).data('sonbr');
    var cust = $(this).data('cust');
    var shipto = $(this).data('shipto');
    var namacust = $(this).data('namacust');
    var namaship = $(this).data('namaship');

    document.getElementById('e_customer').value = cust + ' - ' + namacust;
    document.getElementById('e_shipto').value = shipto + ' - ' + namaship;
    document.getElementById('e_sonbr').value = sonbr;


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
</script>
@endsection

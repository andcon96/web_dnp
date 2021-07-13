@extends('layout.newlayout')

@section('content-title')
<div class="col-12">
  <div class="page-header float-left">
    <div class="page-title">
      <h1>Transaksi / Sales Order</h1>
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
<!--Table Menu-->
<div class="col-12">
  <button class="btn bt-actsales newUser" data-toggle="modal" data-target="#createModal">
    Buat SO</button>
</div>
<div class="col-md-12"><hr></div>
<div class="table-responsive col-12">
  <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr style="background-color: #3b8686;">
        <th>Nomor SO</span></th>
        <th>Pelanggan</span></th>
        <th>Tanggal</th>
        <!-- <th>Site</th> -->
        <th>Batas Waktu</th>
        <th>Total</th>
        @if(Session::get('salesman') != 'Y')
        <th>Created By</th>
        @endif
        <th width="10%">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @include('so.table-sosales')
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
        <h5 class="modal-title text-center" id="exampleModalLabel">Lihat Detail SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group row col-md-12">
          <label for="e_custcode" class="col-md-3 col-form-label text-md-right">Pelanggan</label>
          <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
            <input id="e_custcode" type="text" class="form-control" name="e_custcode" value="{{ old('e_custcode') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_addresscust" class="col-md-3 col-form-label text-md-right">Alamat Pelanggan</label>
          <div class="col-md-6">
            <input id="e_addresscust" type="text" class="form-control" name="e_addresscust" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_shipto" class="col-md-3 col-form-label text-md-right">Kirim Ke</label>
          <div class="col-md-6">
            <input id="e_shipto" type="text" class="form-control" name="e_shipto" value="{{ old('e_shipto') }}" autocomplete="off" readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_address" class="col-md-3 col-form-label text-md-right">Alamat Kirim</label>
          <div class="col-md-6">
            <input id="e_address" type="text" class="form-control" name="e_address" value="{{ old('e_address') }}" autocomplete="off" maxlength="24" autofocus readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_duedate" class="col-md-3 col-form-label text-md-right">Batas Waktu</label>
          <div class="col-md-6">
            <input id="e_duedate" type="text" class="form-control" name="e_duedate" value="{{ old('e_duedate') }}" autocomplete="off" maxlength="24" autofocus placeholder="yy-mm-dd" required readonly>
          </div>
        </div>
        <div class="form-group row col-md-12">
          <label for="e_notes" class="col-md-3 col-form-label text-md-right">Pegawai Toko</label>
          <div class="col-md-6">
            <input id="e_notes" type="e_notes" class="form-control" name="e_notes" required readonly>
          </div>
        </div>

        <h4 class="mb-3" style="margin-left:5px;"><strong>Detail</strong></h4>

        <table id='suppTable' class='table table-bordered dataTable no-footer order-list mini-table' >
          <thead>
            <tr id='full' style="background-color: #3b8686;">
              <th style="width:30%">Barang</th>
              <th style="width:15%">Jumlah</th>
              <th style="width:15%">Satuan</th>
              <th style="width:15%;">Lokasi</th>
            </tr>
          </thead>
          <tbody id='e_detailapp'>
          </tbody>
        </table>


      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-actsales" id="btnclose" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!--Modal Create-->
<div class="modal fade" id="createModal" role="dialog" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="exampleModalLabel">Buat SO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>


      <div class="modal-body">
        <form class="form-horizontal" method="post" id="new" action="/createsosaless">
          {{ csrf_field() }}
          <div class="form-group row col-md-12">
            <label for="custcode" class="col-md-3 col-form-label text-md-right">Pelanggan</label>
            <div class="col-md-6 {{ $errors->has('uname') ? 'has-error' : '' }}">
              <select id="custcode" name="custcode" class="form-control" tabindex="1" required>
                <option value="">-- Select Data --</option>
                @foreach($customer as $cust)
                <option value="{{$cust->cust_code}}">{{$cust->cust_code}} -- {{$cust->cust_alt_name}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="addresscust" class="col-md-3 col-form-label text-md-right">Alamat Pelanggan</label>
            <div class="col-md-6">
              <input id="addresscust" type="text" class="form-control" name="addresscust" tabindex="2" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12" id='rowshipto'>
            <label for="shipto" class="col-md-3 col-form-label text-md-right">Kirim Ke</label>
            <div class="col-md-6">
              <select id="shipto" name="shipto" class="form-control" tabindex="4">

              </select>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="address" class="col-md-3 col-form-label text-md-right">Alamat Kirim</label>
            <div class="col-md-6">
              <input id="address" type="text" class="form-control" name="address" tabindex="2" autocomplete="off" maxlength="24" autofocus readonly>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="duedate" class="col-md-3 col-form-label text-md-right">Batas Waktu</label>
            <div class="col-md-6">
              <input id="duedate" type="text" class="form-control" name="duedate" tabindex="3"  autocomplete="off" maxlength="24" autofocus value="{{$datebsk}}" placeholder="yy-mm-dd" readonly style="background-color: white !important" required>
            </div>
          </div>
          <div class="form-group row col-md-12">
            <label for="notes" class="col-md-3 col-form-label text-md-right">Pegawai Toko</label>
            <div class="col-md-6">
              <input id="notes" type="notes" class="form-control" name="notes" tabindex="5" required>
            </div>
          </div>

          <div class="form-group">
            <h4><strong>Detail</strong></h4>
          </div>

          <table id='suppTable' class='table table-striped table-bordered dataTable no-footer order-list mini-table' style="table-layout: fixed;">
            <thead>
              <tr id='full' style="background-color: #3b8686;">
                <th style="width:30%">Barang</th>
                <th style="width:15%">Jumlah</th>
                <th style="width:15%">Satuan</th>
                <th style="width:15%">Lokasi</th>
                <th style="width:10%">Delete</th>
              </tr>
            </thead>
            <tbody id='detailapp'>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5">
                  <input type="button" class="btn btn-lg btn-block bt-actsales btn-focus" id="addrow" value="Tambah Baris" style="font-size:16px" />
                </td>
              </tr>
            </tfoot>
          </table>


      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-info bt-actsales btn-focus" id="btnclosecreate" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success bt-actsales btn-focus" id="btnconf">Simpan</button>
        <button type="button" class="btn bt-action" id="btnloading" style="display:none; background-color: #3b8686">
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
        $("#shipto").focus();
      }
    });

    $("#custcode").select2({
      width: '100%'
    });
    $("#shipto").select2({
      width: '100%'
    });

   


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

      cols += '<td data-title="jumlah[]" data-label="Jumlah"><input type="number" class="form-control form-control-sm jumlah" autocomplete="off" name="jumlah[]" style="height:37px" required min="1"/></td>';

      cols += '<td data-title="um[]" data-label="Satuan"><input type="text" class="form-control um" autocomplete="off" name="um[]" style="height:37px" min="1" step="1" required readonly/></td>';
      cols += '<td data-title="um[]" data-label="Loc"><input type="text" class="form-control loc" autocomplete="off" name="loc[]" style="height:37px" required readonly/></td>';

      cols += '<td data-title="Action"><input type="button" class="ibtnDel btn btn-danger"  value="Delete"></td>';
      cols += '</tr>'
      newRow.append(cols);

      $("table.order-list").append(newRow);
      selectRefresh();
      counter++;
    });

    $("table.order-list").on("click", ".ibtnDel", function(event) {
      $(this).closest("tr").remove();
      counter -= 1
    });

    $(document).on('change', 'select.barang', function() {
      var um = $(this).closest('tr').find('.um');
      var loc = $(this).closest('tr').find('.loc');
      var item = $(this).val();

      $.ajax({
        url: "/searchum",
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

    $(document).on('click', '.viewmodal', function(e) {
      var sonbr = $(this).data('sonbr');
      var cust = $(this).data('cust');
      var desc = $(this).data('custdesc');
      var alamat = $(this).data('alamat');
      var duedate = $(this).data('duedate');
      var shipto = $(this).data('shipto');
      var notes = $(this).data('remarks');

      document.getElementById('e_custcode').value = cust + ' - ' + desc;
      document.getElementById('e_addresscust').value = alamat;
      document.getElementById('e_duedate').value = duedate;
      document.getElementById('e_shipto').value = shipto;
      document.getElementById('e_notes').value = notes;


      $.ajax({
        url: "/detailsales",
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

    $(document).on('click', '.newUser', function(e) {
      
          $('#detailapp').html('');
    });


    $("#new").submit(function(e) {
      document.getElementById('btnclosecreate').style.display = 'none';
      document.getElementById('btnconf').style.display = 'none';
      document.getElementById('btnloading').style.display = '';

    });

    $("#edit").submit(function() {
      document.getElementById('e_btnclose').style.display = 'none';
      document.getElementById('e_btnconf').style.display = 'none';
      document.getElementById('e_btnloading').style.display = '';
    });

    $("#delete").submit(function() {
      document.getElementById('d_btnclose').style.display = 'none';
      document.getElementById('d_btnconf').style.display = 'none';
      document.getElementById('d_btnloading').style.display = '';
    });
  });

    function selectRefresh() {
      $('.selectpicker').selectpicker().focus();
    }


    function fetch_data(page, sort_type, sort_by) {
      $.ajax({
        url: "/sosales/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by,
        success: function(data) {
          console.log(data);
          $('tbody').html('');
          $('tbody').html(data);
        }
      })
    }

    $(document).on('click', '.pagination a', function(event) {
      event.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      $('#hidden_page').val(page);
      var column_name = $('#hidden_column_name').val();
      var sort_type = $('#hidden_sort_type').val();

      fetch_data(page, sort_type, column_name);
    });
</script>
@endsection

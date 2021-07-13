@extends('layout.newlayout')

@section('content-title')
<div class="col-4">
  <div class="page-header float-left full-head">
    <div class="page-title">
      <h1>Transaksi / Checking SO QAD</h1>
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

<!--- Submit EOD -->
<form method="post" action="/checkspbqadweb">
  {{csrf_field()}}
  <div class="form-group row col-md-12">
    <label for="site" class="col-md-2 col-form-label text-md-right">{{ __('Site') }}</label>
      <div class="col-md-3">
      <select id="site" class="form-control" name="site" autofocus autocomplete="off">
      <option value=""> Select Data </option>
          @foreach($site as $site)
            <option value="{{$site->site_code}}">{{$site->site_code}} -- {{$site->site_desc}}</option>
          @endforeach 
      </select>
    </div>

    <label for="datepick" class="col-md-2 col-form-label text-md-right">{{ __('Date') }}</label>
      <div class="col-md-3">
      <input id="datepick" type="text" class="form-control" name="datepick" value="{{$datenow}}" required autofocus autocomplete="off">
    </div>
  </div>

  <div class="form-group row col-md-12">
    <label for="" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
    <div class="offset-0">
      <input type="submit" class="btn bt-action newUser" style="margin-left: 15px;" id="btnsearch" value="Check SO" data-toggle="modal" data-target="#loadingtable" 
        data-backdrop="static" data-keyboard="false" />
    </div>
  </div>
</form>

<div class="table-responsive col-10 offset-1">
  <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
    <thead>
      <tr>
      	<th style="width:5%">No.</th>
        <th style="width:15%">SPB Number</th>
        <th style="width:15%">SO Number</th>
        <th>Order Date</th>
        <th>Line</th>
        <th>Item Code Web</th>
        <th>Item Code QAD</th>
        <th>Qty Order Web</th>
        <th>Qty Order QAD</th>
      </tr>
    </thead>
    <tbody>
      	@forelse($data as $data)
      		<tr>
      			<td>{{$loop->iteration}}</td>
      			<td>{{$data->so_nbr}}</td>
            <td>{{$data->so_ord_date}}</td>
            <td>{{$data->so_line}}</td>

      			@if($data->so_part != $data->so_part_qad)
      			<td style="background:#f8d7da;color: #721c24;">{{$data->so_part}}</td>
      			<td style="background:#f8d7da;color: #721c24;">{{$data->so_part_qad}}</td>
      			@else
      			<td>{{$data->so_part}}</td>
      			<td>{{$data->so_part_qad}}</td>
      			@endif
      			
      			@if($data->so_qty_ord != $data->so_qty_ord_qad)
      			<td style="background:#f8d7da;color: #721c24;">{{$data->so_qty_ord}}</td>
      			<td style="background:#f8d7da;color: #721c24;">{{$data->so_qty_ord_qad}}</td>
      			@else
      			<td>{{$data->so_qty_ord}}</td>
      			<td>{{$data->so_qty_ord_qad}}</td>
      			@endif
      		</tr>
      	@empty
			<tr>
				<td colspan="9" style="color:red;"> <center>No Data Available</center></td>
			</tr>
      	@endforelse
    </tbody>
  </table>
  <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
  <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
  <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
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
  <script type="text/javascript">
      $('#datepick').datepicker({
        dateFormat: 'yy-mm-dd'
      });

      $("#site").select2({
        width: '100%'
      });



  </script>
@endsection
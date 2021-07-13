@extends('layout.newlayout')

@section('content-title')
<div class="col-4">
  <div class="page-header float-left full-head">
    <div class="page-title">
      <h1>SPB / Check Stock SPB</h1>
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


<!--- Submit EOD -->
	<div class="form-group row col-md-12">
	  <label for="site" class="col-md-2 col-form-label text-md-right">{{ __('Site') }}</label>
	  	<div class="col-md-3">
      @if(Session::get('pusat_cabang') == 1)
      <select id="site" class="form-control" name="site" autofocus autocomplete="off">
        <option value=""> Select Data </option>
        @foreach($site as $site)
          <option value="{{$site->site_code}}">{{$site->site_code}} -- {{$site->site_desc}}</option>
        @endforeach
      </select>
      @else
        <input id="site" type="text" class="form-control" name="site" value="{{Session::get('site')}}" autofocus autocomplete="off" readonly>
      @endif
          
		  </div>

	  <label for="itemnbr" class="col-md-2 col-form-label text-md-right">{{ __('Item Code') }}</label>
	  	<div class="col-md-3">
			<select id="itemnbr" class="form-control" name="itemnbr" autofocus autocomplete="off">
          <option value=""> Select Data </option>
        @foreach($item as $item)
          <option value="{{$item->itemcode}}">{{$item->itemcode}} -- {{$item->itemdesc}}</option>
        @endforeach
      </select>
		</div>
	</div>

	<div class="form-group row col-md-12">
	  <label for="" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
	  <div class="offset-0">
	    <input type="submit" class="btn bt-action newUser" style="margin-left: 15px;" id="btnsearch" value="Submit"/>
	  </div>
	</div>

  <!--- TABLE MENU -->
  <div class="col-10 offset-1">
    <div class="table-responsive">
      <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>SPB Number</th>
            <th>SO Number</th>
            <th>Item Code</th>
            <th>Item Desc</th>
            <th>Qty</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="e_detailapp">
          
        </tbody>
      </table>
      <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
      <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
      <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
    </div>
  </div>
  


<!-- refresh table modal -->
  <div class="modal fade" id="loadingtable" tabindex="-1" role="dialog" aria-hidden="true" id="myModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="spinner-grow text-danger"></div>
      <div class="spinner-grow text-warning" style="animation-delay:0.2s;"></div>
      <div class="spinner-grow text-success" style="animation-delay:0.45s;"></div>
      <div class="spinner-grow text-info"style="animation-delay:0.65s;"></div>
      <div class="spinner-grow text-primary"style="animation-delay:0.85s;"></div>
    </div>
  </div>

  <div id="loader" class="lds-dual-ring hidden overlay"></div>
@endsection


@section('scripts')
  <script type="text/javascript">

    $(document).ready(function() {

      $("#itemnbr").select2({
        width: '100%'
      });

      $(document).on('click', '#btnsearch', function() {
         var site = document.getElementById('site').value;
         var itemcode = document.getElementById('itemnbr').value;

          $.ajax({
            url: "/spbcheck",
            data: {
              site: site,
              itemcode: itemcode,
            },
            beforeSend: function () { // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
            $('#loader').removeClass('hidden')            },
            success: function(data) {
            console.log(data);
            // document.getElementById('address').value = data.trim();
            $('#e_detailapp').html(data);
            },
            complete: function () { // Set our complete callback, adding the .hidden class and hiding the spinner.
            $('#loader').addClass('hidden')
            },
          })
      });

    });

  </script>
@endsection
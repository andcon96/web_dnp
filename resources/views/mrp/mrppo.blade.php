@extends('layout.newlayout')

@section('content-title')
<div class="col-4">
  <div class="page-header float-left full-head">
    <div class="page-title">
      <h1>MRP / Create PO MRP</h1>
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
			<input id="site" type="text" class="form-control" name="site" value="{{Session::get('site')}}" readonly autofocus autocomplete="off">
		</div>

    <label for="type" class="col-md-2 col-form-label text-md-right">{{ __('Type') }}</label>
      <div class="col-md-3">
      <select id="type" name="type" class="form-control">
          <option value="All"> All </option>
          <option value="SP"> SP </option>
          <option value="NSP"> NSP </option>
      </select>
    </div>

    <div class="col-md-2">
          <button class="btn bt-ref" style="float:left;height: 36;margin-right: 10px;" id='btnsearch'>
            <i class="fa fa-search"></i>
          </button>
          <form method="get" action="{{url('/exportMRPPO')}}">
            <input type="hidden" name="c_type" id='c_type' value="">
            <button class="btn bt-ref" style="float:left;height: 36;" id='btnexcel'>
              <i class="fa fa-file-excel"></i>
            </button>
          </form>
    </div>
          
	</div>

<form method="post" action="/createmrppo">
  {{csrf_field()}}

  <div class="form-group row col-md-12">
    <label for="" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
    <div class="offset-0">
      <input type="submit" class="btn bt-action newUser" style="margin-left: 15px;" id="btnsearch" value="Generate PO SP" data-toggle="modal" data-target="#loadingtable" 
        data-backdrop="static" data-keyboard="false" />
    </div>
  </div>
</form>

<div class="col-10 offset-1">
  <div class="table-responsive">
    <table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th>#</th>
          <th>Item Code</th>
          <th>Item Desc</th>
          <th>Item Type</th>
          <th>Qty</th>
        </tr>
      </thead>
      <tbody id="bodypo">
        @include('mrp.table-mrppo')
      </tbody>
    </table>
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

<script type="text/javascript">
  $(document).on('click', '#btnsearch', function() {

    var type = document.getElementById('type').value;
    
    $.ajax({
      url: "/mrppo/search",
      data: {
        type: type,
      },
      success: function(data) {
        console.log(data);
        $("#bodypo").html('').append(data);
      }
    })

  });

  $(document).on('click', '#btnexcel', function() {

    var type = document.getElementById('type').value;
    
    document.getElementById('c_type').value = type;

  });

  
</script>

@endsection
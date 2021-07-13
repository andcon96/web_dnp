@extends('layout.newlayout')

@section('content-title')
<div class="col-4">
  <div class="page-header float-left full-head">
    <div class="page-title">
      <h1>MRP / Generate MRP</h1>
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
<form method="post" action="/createeod">
	{{csrf_field()}}
	<div class="form-group row col-md-12">
	  <label for="site" class="col-md-2 col-form-label text-md-right">{{ __('Site') }}</label>
	  	<div class="col-md-3">
			<input id="site" type="text" class="form-control" name="site" value="{{Session::get('site')}}" readonly autofocus autocomplete="off">
		</div>

	  <label for="lasteod" class="col-md-2 col-form-label text-md-right">{{ __('Last Run') }}</label>
	  	<div class="col-md-3">
			<input id="lasteod" type="text" class="form-control" name="lasteod" value="{{$data->lasteod}}" readonly autofocus autocomplete="off">
		</div>
	</div>
	<div class="form-group row col-md-12">
	  <label for="" class="col-md-2 col-form-label text-md-right">{{ __('') }}</label>
	  <div class="offset-0">
	    <input type="submit" class="btn bt-action newUser" style="margin-left: 15px;" id="btnsearch" value="Submit" data-toggle="modal" data-target="#loadingtable" 
        data-backdrop="static" data-keyboard="false" />
	  </div>
	</div>
</form>


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

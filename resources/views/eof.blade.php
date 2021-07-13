@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Transaksi / End Of Day</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
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

      <form class="form-horizontal" method="post" action="/eofsubmit">
      		{{ csrf_field() }}
		<div class="col-12 form-group row">
      	<!--Search Disini-->
	    <label for="lastrun" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Last Run') }}</label>
	    <div class="col-md-4 col-sm-4 mb-2 input-group">
          <input id="lastrun" type="text" class="form-control" name="lastrun" 
          value="{{$data->last_run}}" autofocus autocomplete="off" readonly>
      	</div>
      		<label for="" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
			<div class="col-md-4 col-sm-4 mb-2 input-group">
				<input type="submit" class="btn bt-action" id="btnsearch" value="Submit" style="float:right" onclick="return (confirm('Are you sure?'))" data-toggle="modal" data-target="#loadingtable" />
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
</div>
@endsection


@section('scripts')
  <script type="text/javascript">
    $(document).ready(function () {
    	var date = document.getElementById('lastrun').value;
    	var today = new Date();
		var dd = String(today.getDate()).padStart(2, '0');
		var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = today.getFullYear();

		today = yyyy + '-' + mm + '-' + dd;

		if(date === today){
    		$('#btnsearch').attr('disabled','');
		}else{
			$('#btnsearch').removeAttr('disabled');
		}
    });
  </script>
	

@endsection
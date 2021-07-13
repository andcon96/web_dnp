@extends('layout.newlayout')
@section('content-title')
    <div class="col-sm-4 col-md-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Check in/Check out</h1>
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

<form class="form-horizontal" method="POST" id="sobutton" action="btnsales">
    {{ csrf_field() }}
    <input type="hidden" name="status" value="{{$status}}" id="status" class="form-control">
    <input type="hidden" name=id value="{{$id}}" id="id" class="form-control">
    <input type="hidden" name="kecustomer" value="{{$kecustomer}}" id="kecustomer" class="form-control">
    <input type="hidden" name=waktucheckin value="{{$waktucheckin}}" id="waktucheckin" class="form-control">
    <div class="text-center">
        
	<div class="d-lg-inline  d-sm-block">
	<label class="col-form-label text-md-left"><b>{{ __('Status saat ini :') }}</b></label>
        <label class="ml-2"><i><b><?php echo $status ?></b></i></label>
	</div>
	
	<div class="d-lg-inline d-sm-block">
	<label class="col-form-label text-md-left ml-4"><b>{{ __('Customer : ') }}</b></label>
        <label class="ml-2"><i><b><?php echo $kecustomer ?></b></i></label>
	</div>
	
	<div class="d-lg-inline d-sm-block">
        <label class="col-form-label text-md-left ml-4"><b>{{ __('Waktu Check In  : ') }}</b></label>
        <label class="ml-2"><i><b><?php echo $waktucheckin ?></b></i></label>
	</div>
    </div>
    <hr>
    <div class="row justify-content-center text-center">
        <div class="col-lg-6 col-md-6 col-xs-12">
            <div id="divbtncheckin">
                <select id="sacustomer" class="form-control" name="sacustomer">
                    <option value="">-- Pilih customer --</option>
                    @foreach($cust as $ct)
                    <option value="{{$ct->cust_code}}">{{$ct->cust_code}} -- {{$ct->cust_desc}}</option>
                    @endforeach
                </select>
            </div>
            <div id="divbtncheckout">
                <select id="sactivity" class="form-control" name="sactivity">
                    <option value="">-- Pilih activity --</option>
                    @foreach($acti as $at)
                    <option value="{{$at->activity_code}}">{{$at->activity_code}} -- {{$at->activity_desc}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-center  my-2">
            <button type="submit" style="min-width: 100%;" class="btn btn-success bt-action btn-lg btn-block" id="btncheckin">Check in</button>
            <button type="submit" style="min-width: 100%;" class="btn btn-success bt-action btn-lg btn-block" id="btncheckout">Check out</button>
        </div>

    </div>

</form>

@endsection
@section('scripts')
<script type="text/javascript">
    $("#sacustomer").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
    $("#sactivity").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });

    function fetch_data(id, customer, activity, status) {
        $.ajax({
            url: "/salesactivity?id=" + id + "&customer=" + customer + "&activity=" + activity + "&status" + status,
            success: function(data) {
                console.log(data);
            }
        })
    }

    $status = document.getElementById('status').value;
    if ($status == 'checkin') {
        document.getElementById('btncheckout').style.display = '';
        document.getElementById('btncheckin').style.display = 'none';
        document.getElementById('divbtncheckin').style.display = 'none';
        document.getElementById('divbtncheckout').style.display = '';
    } else if ($status == 'checkout') {
        document.getElementById('btncheckin').style.display = '';
        document.getElementById('btncheckout').style.display = 'none';
        document.getElementById('divbtncheckin').style.display = '';
        document.getElementById('divbtncheckout').style.display = 'none';
    }

    $(document).ready(function(e) {


        // alert($status);


        $("#btncheckin").click(function() {
            if ($status == 'checkin') {
                document.getElementById('btncheckin').style.display = 'none';
                document.getElementById('btncheckout').style.display = '';
                document.getElementById('divbtncheckin').style.display = 'none';
                document.getElementById('divbtncheckout').style.display = '';
            } else if ($status == 'checkout') {
                document.getElementById('btncheckin').style.display = '';
                document.getElementById('btncheckout').style.display = 'none';
                document.getElementById('divbtncheckin').style.display = '';
                document.getElementById('divbtncheckout').style.display = 'none';
            }
        });

        $("#btncheckout").click(function() {
            if ($status == 'checkin') {
                document.getElementById('btncheckin').style.display = 'none';
                document.getElementById('btncheckout').style.display = '';
                document.getElementById('divbtncheckin').style.display = 'none';
                document.getElementById('divbtncheckout').style.display = '';
            } else if ($status == 'checkout') {
                document.getElementById('btncheckin').style.display = '';
                document.getElementById('btncheckout').style.display = 'none';
                document.getElementById('divbtncheckin').style.display = '';
                document.getElementById('divbtncheckout').style.display = 'none';
            }
        });
    });
</script>
@endsection

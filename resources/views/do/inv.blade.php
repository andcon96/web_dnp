@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Inventory by Item</h1>
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
<form action="inv" method="post">
      {{ csrf_field() }}
    <div class="row mb-3">
        <label for="barang" class="col-form-label col-md-2">{{ __('Item Number') }}</label>
        <select id="barang" class="form-control selectpicker barang col-md-3" name="barang" data-live-search="true" autofocus>
        <option value = ""> -- Select Data -- </option>
        @foreach($item as $item)
        <option value="{{$item->itemcode}}"> {{$item->itemcode}} -- {{$item->itemdesc}} </option>';
        @endforeach
        </select>
    </div>
    <div class="row mb-3">
          <label for="site" class="col-form-label col-md-2">{{ __('Site') }}</label>
        
        @if(Session::get('pusat_cabang') == 1)
          <select id="site" class="form-control selectpicker site col-md-3" name="site" data-live-search="true" autofocus>
            <option value = ""> -- Select Data -- </option>
            @foreach($site as $site)
            <option value="{{$site->site_code}}"> {{$site->site_code}} -- {{$site->site_desc}} </option>';
            @endforeach
          </select>
        @else
          <input type="text" name="site" id="site" class="form-control site col-md-3" value="{{Session::get('site')}}" readonly>
        @endif


    </div>
    <div class="row mb-3">
        <label for="loc" class="col-form-label col-md-2">{{ __('Location') }}</label>
        <select id="loc" class="form-control selectpicker loc col-md-3" name="loc" data-live-search="true" autofocus>
        <option value = ""> -- Select Data -- </option>
        @foreach($loc as $loc)
        <option value="{{$loc->item_location}}"> {{$loc->item_location}}</option>';
        @endforeach
        </select>
    </div>

    <!--Table-->

      <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
      <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
      <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
      <button type="submit" class="btn bt-action mb-3" name='action' value="confirm" id='btnconf'>Search</button>
      <br>
      <table  class="table table-bordered no-footer mini-table">
        <thead>
          <td>Site</td>
          <td>Location</td>
          <td>Item Number</td>
          <td>Lot/Serial</td>
          <td>UM</td>
          <td>Qty on Hand</td>
          <td>Created</td>
        </thead>
        <tbody>
          @include('do.inv-table')
        </tbody>                   
      </table>
    </form>

@endsection


@section('scripts')
  <script type="text/javascript">
    
      function fetch_data(page, sort_type, sort_by, code, shipto, datefrom, dateto)
      {
          $.ajax({
           url:"/docreate/pagination?page="+page+"&sorttype="+sort_type+"&sortby="+sort_by+"&code="+code+"&shipto="+shipto+"&datefrom="+datefrom+"&dateto="+dateto,
           success:function(data)
           {
            console.log(data);
            $('tbody').html('');
            $('tbody').html(data);
           }
          })
      }

  </script>
@endsection

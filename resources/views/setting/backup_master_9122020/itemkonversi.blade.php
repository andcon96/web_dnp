@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Master / Item Convertion</h1>
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

<div class="col-md-2 col-sm-4 mb-3 offset-md-2 offset-lg-0">
        <form action="/loaditemkonv" method="POST">
        @csrf
            <input type="submit" data-toggle="modal" data-target="#loadingtable" class="btn bt-action" 
            id="btnrefresh" value="Load Table"  style="float:right" />
        </form>
    </div>
<div class="col-12"><hr></div>

<!--Table Menu--> <!--Search Disini-->
<div class="col-12 form-group row" style="float: left;">
    <label for="s_um1" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('UM 1') }}</label>
    <div class="col-lg-2 col-md-4 col-sm-4 mb-2 input-group">
        <select id="s_um1" type="text" class="form-control" name="s_um1" required>
            <option value="">-- Pilih UM --</option>
            @foreach($data as $iu1)
            <option value="{{$iu1->um_1}}">{{$iu1->um_1}}</option>
            @endforeach
        </select>
    </div>

    <label for="s_itemcode" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Item Code') }}</label>
    <div class="col-md-4 mb-2 input-group">
        <input id="s_itemcode" type="text" class="form-control" name="s_itemcode" value="" autofocus autocomplete="off">
    </div>
</div>
<div class="col-12 form-group row">
    <label for="s_um2" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('UM 2') }}</label>
    <div class="col-lg-2 col-md-4 col-sm-4 mb-2 input-group">
        <select id="s_um2" type="text" class="form-control" name="s_um2" required>
            <option value="">-- Pilih UM --</option>
            @foreach($data as $iu2)
            <option value="{{$iu2->um_2}}">{{$iu2->um_2}}</option>
            @endforeach
        </select>
    </div>
    <label class="col-md-2"></label>
    <div class="col-md-2 col-sm-4 offset-md-2 offset-lg-0">
        <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:left" />
    </div>
</div>
<div class="col-md-12"><hr></div>

<div class="table-responsive col-12" style="overflow: auto; display: block;white-space: nowrap; margin-left: auto; margin-right: auto;">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="30%" class="sorting" data-sorting_type="asc" data-column_name="item_code" style="cursor: pointer">Item Code<span id="name_icon"></span></th>
                <th width="10%" class="sorting" data-sorting_type="asc" data-column_name="um_1" style="cursor: pointer">UM 1 </th>
                <th width="10%" class="sorting" data-sorting_type="asc" data-column_name="um_2" style="cursor: pointer">UM 2 </th>
                <th width="10%" class="sorting" data-sorting_type="asc" data-column_name= "qty_item" style="cursor: pointer">Quantity </th>  

            </tr>
        </thead>
        <tbody>
            @include('setting.table-itemkonversi')
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
</div>


@endsection


@section('scripts')
<script>
    $(document).on('click','#btnrefresh',function(){
        $(document).ready(function () {
        $(document).keydown(function (event) {
            var charCode = event.charCode || event.keyCode || event.which;
            if (charCode == 27 ) {
            
            return false;
            }
        });
        });
    })
    
    function clear_icon() {
        $('#id_icon').html('');
        $('#post_title_icon').html('');
    }

    function fetch_data(page, sort_type, sort_by, itemcode, um1, um2) {
        $.ajax({
            url: "/itemkonversi/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&itemcode=" + itemcode + "&um1=" + um1 + "&um2=" + um2,
            success: function(data) {
                console.log(data);
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }


    
    $(document).on('click', '#btnsearch', function() {
        var itemcode = $('#s_itemcode').val(); 
        var um1 = $('#s_um1').val(); 
        var um2 = $('#s_um2').val(); 
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = 1;

        fetch_data(page, sort_type, column_name, itemcode, um1, um2);
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
        var itemcode = $('#s_itemcode').val(); 
        var um1 = $('#s_um1').val(); 
        var um2 = $('#s_um2').val(); 
        fetch_data(page, reverse_order, column_name, itemcode, um1, um2);
    });

    
    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();

        var itemcode = $('#s_itemcode').val();
        var um1 = $('#s_um1').val(); 
        var um2 = $('#s_um2').val(); 
        fetch_data(page, sort_type, column_name, itemcode, um1, um2);

    });
</script>
@endsection

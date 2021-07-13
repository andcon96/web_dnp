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
        <form action="/loaditemchild" method="POST">
        @csrf
            <input type="submit" data-toggle="modal" data-target="#loadingtable" class="btn bt-action" 
            id="btnrefresh" value="Load Table"  style="float:right" />
        </form>
    </div>
<div class="col-12"><hr></div>

<!--Table Menu--> <!--Search Disini-->
<div class="col-12 form-group row" style="float: left;">
    <label for="parentcode" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Parent Code') }}</label>
    <div class="col-lg-4 col-md-4 col-sm-4 mb-2 input-group">
        <select id="parentcode" type="text" class="form-control" name="parentcode" required>
            <option value="">-- Pilih Parent --</option>
            @foreach($dropdown as $parent)
                <option value="{{$parent->item_code}}">{{$parent->item_code}}</option>
            @endforeach

        </select>
    </div>
</div>
<div class="col-12 form-group row">
    <label for="childcode" class="col-md-2 col-sm-2 col-form-label text-md-right">{{ __('Child Code') }}</label>
    <div class="col-lg-4 col-md-4 col-sm-4 mb-2 input-group">
        <select id="childcode" type="text" class="form-control" name="childcode" required>
            <option value="">-- Pilih Child --</option>
            @foreach($dropdown as $child)
                <option value="{{$child->item_child}}">{{$child->item_child}}</option>
            @endforeach

        </select>
    </div>
    <label class="col-md-2"></label>
    <div class="col-md-2 col-sm-4 offset-lg-0">
        <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:left" />
    </div>
</div>
<div class="col-md-12"><hr></div>

<div class="table-responsive col-12" style="overflow: auto; display: block;white-space: nowrap; margin-left: auto; margin-right: auto;">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th width="30%" class="sorting" data-sorting_type="asc" data-column_name="item_code" style="cursor: pointer">Item Code<span id="name_icon"></span></th>
                <th width="30%" class="sorting" data-sorting_type="asc" data-column_name="item_child" style="cursor: pointer">Item Child</th>
                <th width="10%" class="sorting" data-sorting_type="asc" data-column_name= "qty_item" style="cursor: pointer">Qty Per</th>  
            </tr>
        </thead>
        <tbody>
            @include('setting.table-itemchild')
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

    $("#parentcode").select2({
        width : '100%'
    });

    $("#childcode").select2({
        width : '100%'
    });
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

    function fetch_data(page, sort_type, sort_by, itemcode, childcode) {
        $.ajax({
            url: "/itemchild/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&itemcode=" + itemcode + "&childcode=" + childcode,
            success: function(data) {
                console.log(data);
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }


    
    $(document).on('click', '#btnsearch', function() {
        var itemcode = $('#parentcode').val(); 
        var childcode = $('#childcode').val(); 
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = 1;

        fetch_data(page, sort_type, column_name, itemcode, childcode);
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
        var itemcode = $('#parentcode').val(); 
        var childcode = $('#childcode').val(); 
        fetch_data(page, sort_type, column_name, itemcode, childcode);
    });

    
    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();

        var itemcode = $('#parentcode').val(); 
        var childcode = $('#childcode').val(); 
        fetch_data(page, sort_type, column_name, itemcode, childcode);
    });
</script>
@endsection

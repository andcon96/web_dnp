@extends('layout.newlayout')

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

<!--Table Menu-->
<div class="col-12">
</div>


<div class="col-12 form-group row">
    <!--FORM Search Disini-->
    <label for="s_sosa_cs" class="col-md-1 col-sm-2 col-form-label text-md-left">{{ __('Cust code') }}</label>
    <div class="col-md-4 col-sm-4 mb-2 input-group ">
        <input id="s_sosa_cs" type="text" class="form-control" name="s_sosa_cs" value="" autofocus autocomplete="off">
    </div>
    <label for="s_sosa_sj" class="col-md-3 col-sm-2 col-form-label text-md-right">{{ __('Surat Jalan') }}</label>
    <div class="col-md-4 col-sm-4 mb-2 input-group">
        <input id="s_sosa_sj" type="text" class="form-control" name="s_sosa_sj" value="" autofocus autocomplete="off" min="0">
    </div>
    <label for="" class="col-md-1 col-sm-2 col-form-label text-md-right">{{ __('') }}</label>
    <div class="col-md-4 col-sm-4 mb-2 input-group">
        <input type="button" class="btn bt-action" id="btnsearch" value="Search" style="float:right" />
    </div>
</div>
<div class="col-md-12"><hr></div>

<div class="table-responsive col-12">
    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th class="sorting" data-sorting_type="asc" data-column_name="do_nbr" style="cursor: pointer" width="30%">Surat Jalan<span id="name_icon"></span></th>
                <th class="sorting" data-sorting_type="asc" data-column_name="do_cust" style="cursor: pointer" width="30%">Customer code<span id="name_icon"></span></th>
                <th class="sorting" data-sorting_type="asc" data-column_name="cust_desc" style="cursor: pointer" width="30%">Customer name<span id="username_icon"></span></th>
                <th width="10%">Action</th>
            </tr>
        </thead>
        <tbody>
            @include('so.table-soshipmentconfirm')
        </tbody>
    </table>
    <input type="hidden" name="hidden_page" id="hidden_page" value="1" />
    <input type="hidden" name="hidden_column_name" id="hidden_column_name" value="do_nbr" />
    <input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</div>


<!-- modal accept -->
<div class="modal fade" id="confirmodal" role="dialog" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalLabel">Accept Shipment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form class="form-horizontal" method="post" action="soconfirm">
                {{ csrf_field() }}

                <div class="modal-body">
                    <div class="form-group row">
                        <span for="e_suratjalan" value="" class="col-md-3 col-form-label text-md-right">Surat Jalan</span>
                        <div class="col-md-4 {{ $errors->has('uname') ? 'has-error' : '' }}">
                            <input id="e_suratjalan" type="text" class="form-control" name="e_suratjalan" value="{{ old('e_suratjalan') }}" autocomplete="off" maxlength="6" readonly autofocus>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="e_custname" class="col-md-3 col-form-label text-md-right">Cust Name</label>
                        <div class="col-md-4">
                            <input id="e_custname" type="text" class="form-control" name="e_custname" value="{{ old('e_custname') }}" autocomplete="off" maxlength="24" readonly autofocus required>
                        </div>
                    </div>
                    <div class="table-responsive form-group row">
                        <div class="col-md-12">
                            <table class="table" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th width="10%">SO</th>
                                        <th width="10%">Item code</th>
                                        <th width="10%">Item Desc</th>
                                        <th width="10%">Shipto</th>
                                        
                                        <th width="10%">Quantity</th>
                                        <th width="10%">Price</th>
                                        <th width="10%">Due date</th>
                                        <th width="10%">Order date</th>
                                    </tr>
                                </thead>
                                <tbody id="testtable">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info bt-action" id="e_btnclose" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success bt-action" id="e_btnconf">Confirm</button>
                    <button type="button" class="btn bt-action" id="e_btnloading" style="display:none">
                        <i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).on('click', '.soshipmentgetinfo', function() {

        var suratjalan = $(this).data('do_nbr');
        var custcode = $(this).data('do_cust');
        var custname = $(this).data('do_custname');
        document.getElementById('e_suratjalan').value = suratjalan;
        document.getElementById('e_custname').value = custcode.concat(" --- ", custname);

        $.ajax({
            type: "get",
            url: "{{URL::to("soshipmentgetinfo")}}",
            data: {
                search: suratjalan,
            },
            success: function(data) {
                $('#testtable').html(data);


            }
        })
    });

    function clear_icon() {
        $('#id_icon').html('');
        $('#post_title_icon').html('');
    }

    function fetch_data(page, sort_type, sort_by, customercode, suratjalan) {
        $.ajax({
            url: "/soshipmentconfirm/pagination?page=" + page + "&sorttype=" + sort_type + "&sortby=" + sort_by + "&customercode=" + customercode + "&suratjalan=" + suratjalan,
            success: function(data) {
                console.log(data);
                $('tbody').html('');
                $('tbody').html(data);
            }
        })
    }



    $(document).on('click', '#btnsearch', function() {
        var customercode = $('#s_sosa_cs').val(); //tambahan
        var suratjalan = $('#s_sosa_sj').val(); //tambahan
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();
        var page = $('#hidden_page').val();

        fetch_data(page, sort_type, column_name, customercode, suratjalan);
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
        var customercode = $('#s_sosa_cs').val();
        var suratjalan = $('#s_sosa_sj').val();
        fetch_data(page, reverse_order, column_name, customercode, suratjalan);
    });


    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();

        var customercode = $('#s_sosa_cs').val();
        var suratjalan = $('#s_sosa_sj').val();
        fetch_data(page, sort_type, column_name, customercode, suratjalan);

    });
</script>
@endsection
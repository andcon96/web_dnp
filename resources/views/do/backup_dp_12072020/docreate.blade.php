@extends('layout.newlayout')

@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                <h1>Create SPB</h1>
            </div>
        </div>
    </div>
@endsection
@section('content')

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
  $( function() {
      $( "#datefrom" ).datepicker({
          dateFormat : 'dd/mm/yy'
      });
      $( "#dateto" ).datepicker({
          dateFormat : 'dd/mm/yy'
      });
  });
</script>



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



    <div class="form-group row">
          <label for="custcode" class="col-md-2 col-lg-2 col-form-label">{{ __('Customer Code') }}</label>
          <div class="col-md-4 col-lg-3">
              <input id="custcode" type="text" class="form-control" name="custcode" 
              value="" autofocus>
          </div>
          <label for="shipto" class="col-md-2 col-lg-2 col-form-label">{{ __('Ship To Code') }}</label>
          <div class="col-md-4 col-lg-3">
              <input id="shipto" type="text" class="form-control" name="shipto" 
              value="" autofocus>
          </div>
    </div>
    <div class="form-group row">
          <label for="datefrom" class="col-md-2 col-lg-2 col-form-label">{{ __('Sales Order Date From') }}</label>
          <div class="col-md-4 col-lg-3">
              <input type="text" id="datefrom" class="form-control" name='datefrom' placeholder="DD/MM/YYYY"
                      required autofocus autocomplete="off">
          </div>
          <label for="dateto" class="col-md-2 col-lg-2 col-form-label">{{ __('Date To') }}</label>
          <div class="col-md-4 col-lg-3">
              <input type="text" id="dateto" class="form-control" name='dateto' placeholder="DD/MM/YYYY"
                      required autofocus autocomplete="off">
          </div>
          <div class="col-md-2 offset-md-2 offset-lg-0 d-flex">
            <input type="button" class="btn bt-action seconddata" id="btnsearch" value="Search" />
            <button class="btn bt-action seconddata" id='btnrefresh' style="font-size:17px; margin-left: 10px; width: 40px !important"><i class="fa fa-refresh"></i></button>
          </div>
    </div>

    <div class="col-md-12"><hr></div>
    <!--Table-->
    <form action="/createdoTemp" method="post">
      {{ csrf_field() }}
      @include('do.docreate-view')

      <button type="submit" class="btn bt-action" name='action' value="confirm" id='btnconf'>CREATE</button>
    </form>

@endsection


@section('scripts')
  <script type="text/javascript">
    
      function fetch_data(page, sort_type, sort_by)
      {
          $.ajax({
           url:"/sosales/pagination?page="+page+"&sorttype="+sort_type+"&sortby="+sort_by,
           success:function(data)
           {
            console.log(data);
            $('tbody').html('');
            $('tbody').html(data);
           }
          })
      }

      $(document).on('click', '.pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();

        fetch_data(page, sort_type, column_name);
      });

      $('#btnsearch').on('click',function(){      
        var code      = document.getElementById("custcode").value;
        var shipto    = document.getElementById("shipto").value;
        var datefrom  = document.getElementById("datefrom").value;
        var dateto    = document.getElementById("dateto").value;
        
        jQuery.ajax({
            type : "get",
            url : "{{URL::to("dosearch") }}",
            data:{
              code      : code,
              shipto    : shipto,
              datefrom  : datefrom,
              dateto    : dateto,
            },
            success:function(data){
              //alert('tyas');
              console.log(data);
              $(".tag-container").empty().html(data);
            }
        });
      });

      $('#btnrefresh').on('click',function(){
      
      var code      = '';
      var shipto    = '';
      var datefrom  = '';
      var dateto    = '';

      jQuery.ajax({
          type : "get",
          url : "{{URL::to("dosearch") }}",
          data:{
            code      : code,
            shipto    : shipto,
            datefrom  : datefrom,
            dateto    : dateto,
          },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $(".tag-container").empty().html(data);
            document.getElementById("custcode").value = '';
            document.getElementById("shipto").value = '';
            document.getElementById("datefrom").value = '';
            document.getElementById("dateto").value = '';
          }
      });
      });

  </script>
@endsection

@extends('layout.newlayout')
@if($action == "doadd")
      @php($lastnbr = 'add')
      @php($dodate  = Carbon\Carbon::now()->format('d/m/Y'))
      @php($donote  = "")
    @else
      @php($lastnbr = $lastdo)
      @php($dodate  = $domstrs->do_date)
      @php($donote  = $domstrs->do_notes)
    @endif
@section('content-title')
    <div class="col-4">
        <div class="page-header float-left full-head">
            <div class="page-title">
                @if($lastnbr == 'add')
                  <h1>Surat Pengiriman Barang</h1>
                @else
                  <h1>Surat Pengiriman Barang - {{$lastnbr}}</h1>
                @endif
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
      $( "#dodate" ).datepicker({
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

    

    <?php
        //$lastnbr = "DO".str_pad($lastnbr+1,6,"0",STR_PAD_LEFT);
    ?>

    <div id="doqtyerr" class="alert alert-danger" style="display: none">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Quantity shipments must not be more than the quantity open!</strong>
    </div>
<div id="doqtyerrstock" class="alert alert-danger" style="display: none">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Quantity shipments must not be more than the quantity stock!</strong>
    </div>
   
    
    <!--Table-->
    <FORM method="post" id="formsave" action="dosave">
      {{csrf_field()}}
    <input type="hidden" name="donbr" value="{{$lastnbr}}">
    <input type="hidden" id="act" name="act" value="{{$action}}">
    <div class="form-group row">
          <label for="dodate" class="col-md-2 col-lg-2 col-form-label">{{ __('Delivery Date') }}</label>
          <div class="col-md-4 col-lg-3">
              <input type="text" id="dodate" class="form-control" name='dodate' placeholder="DD/MM/YYYY"
                      required value="{{$dodate}}">
          </div>
          <label for="donote" class="col-md-2 col-lg-2 col-form-label">{{ __('Notes') }}</label>
          <div class="col-md-4 col-lg-3">
              <input type="text" id="donote" class="form-control" name='donote' value="{{$donote}}" autofocus>
          </div>
    </div>

      @include('do.docreate-table')
      
      <div class="row float-right">
      <button type="button" class="btn btn-info bt-action" id="btnclose" >Cancel</button>
      <input type="submit" id="btnSubmit" name="btnSubmit" class="btn bt-action ml-3 mr-5 float-right" value="Save">
      <button type="button" class="btn bt-action mr-3" id="btnloading" style="display:none"><i class="fa fa-circle-o-notch fa-spin"></i> &nbsp;Loading
      </button>
     </div>
    </FORM>

@endsection


@section('scripts')
  <script type="text/javascript">
    

      $('#btnclose').on('click',function(){
  
  var nextact = document.getElementById('act').value;
  
  if(nextact=='doadd'){
     location.href='/createdo';
  }
  else if(nextact== 4 || nextact == 1){
     location.href='/do';
  }
  

  });

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
      $(document).on('submit','#formsave',function(e){
        document.getElementById('btnloading').style.display = 'block';
        document.getElementById('btnclose').style.display = 'none';
        document.getElementById('btnSubmit').style.display = 'none';

      });

      $(document).on('click', '.pagination a', function(event){
        event.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        $('#hidden_page').val(page);
        var column_name = $('#hidden_column_name').val();
        var sort_type = $('#hidden_sort_type').val();

        fetch_data(page, sort_type, column_name);
      });

      $('#btnsearch').on('click',function(){
      var code = document.getElementById("custcode").value;
      var datefrom = document.getElementById("datefrom").value;
      var dateto = document.getElementById("dateto").value;
      
      jQuery.ajax({
          type : "get",
          url : "{{URL::to("createdo") }}",
          data:{
            code : code,
            datefrom : datefrom,
            dateto : dateto,
          },
          success:function(data){
            //$('tbody').html(data);
            console.log(data);
            $(".tag-container").empty().html(data);
          }
      });
      });

      $(document).on('change','#cek',function(e){
          var checkbox = $(this), // Selected or current checkbox
          value = checkbox.val(); // Value of checkbox
          
          //document.getElementById('doqtyerr').style.display = 'none';

          if (checkbox.is(':checked'))
          {
              $(this).closest("tr").find('.doqty').prop('readonly',false).css("background-color", "white").focus();
        $(this).closest("tr").find('.tick').val(1);
          } else
          {
              $(this).closest("tr").find('.doqty').prop('readonly',true).css("background-color", "#ced8f2");
        $(this).closest("tr").find('.tick').val(0);
          }        
      });
      $(document).ready(function () {
          $(document).keydown(function (event) {
            var charCode = event.charCode || event.keyCode || event.which;
            if (charCode == 13 ) {
              
              return false;
            }
          });
        });
      $(document).on('input','#doqty',function(e){
        
        
                var tablelength = document.getElementById('dataTable').rows.length;
                var table = document.getElementById('dataTable');

                
                var doqty     = $(this).closest("tr").find(".doqty").val();
                var doqtyso   = $(this).closest("tr").find(".doqtyso").val();
                var doqtyopen = $(this).closest("tr").find(".doqtyopen").val();
                var doqtystock = $(this).closest("tr").find(".doqtystock").val();
                
                if(parseInt(doqty) > parseInt(doqtystock)){
                  document.getElementById('doqtyerr').style.display = 'none';
                  document.getElementById('doqtyerrstock').style.display = 'block';
                   document.getElementById('btnSubmit').style.display = 'none';
                }
                else if (parseInt(doqty) > parseInt(doqtyopen)  ) {
                  document.getElementById('doqtyerrstock').style.display = 'none';
                   document.getElementById('doqtyerr').style.display = 'block';
                   document.getElementById('btnSubmit').style.display = 'none';
                } else {
                   for(i = 1; i < tablelength; i++){
                    var checklist = table.rows[i].cells[11].children[1].value;
                    
                    if(checklist == 1){
                      var doqtycheck = table.rows[i].cells[10].firstElementChild.value;
                      var doqtysocheck = table.rows[i].cells[7].firstElementChild.value;
                      var doqtyopencheck = table.rows[i].cells[8].firstElementChild.value;
                      var doqtystockcheck = table.rows[i].cells[9].firstElementChild.value;
                        
                      if(parseInt(doqtycheck) > parseInt(doqtystockcheck)){
                        
                        document.getElementById('doqtyerr').style.display = 'none';
                        document.getElementById('doqtyerrstock').style.display = 'block';
                        document.getElementById('btnSubmit').style.display = 'none';
                        
                      }
                      else if (parseInt(doqtycheck) > parseInt(doqtyopencheck)  ) {
                        
                        document.getElementById('doqtyerrstock').style.display = 'none';
                        document.getElementById('doqtyerr').style.display = 'block';
                        document.getElementById('btnSubmit').style.display = 'none';
                        
                      }
                      else{
                      
                        document.getElementById('doqtyerr').style.display = 'none';
                        document.getElementById('doqtyerrstock').style.display = 'none';
                        document.getElementById('btnSubmit').style.display = 'block';
                      }
                    }
                    else if(checklist == 0){
                      
                    }
                  }
                   
                }
            });



  </script>
@endsection

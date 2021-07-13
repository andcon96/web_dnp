<!DOCTYPE html>
<html>
<head>
	<title>Surat Pengiriman Barang </title>
</head>
<body>

<style type="text/css">
  @page { margin: 80px 30px 130px 30px; }

  #header { 
    position: fixed; 
    left: 0px; 
    top: -30px; 
    right: 0px;  
    text-align: center; 
    padding-top: -10px;
    padding-bottom: -10px; 
  }
  #footer {
    position: fixed;
    bottom: -40px;
    width: 100%;
    height: 0px;
    text-align: right;
    padding-top: 0px;
    padding-bottom: 0px; 
  }
  .pindah{
    display: block;
    page-break-before: always;
  }
  table.minimalistBlack {
        width: 100%;
        border-spacing: 0px;
    }
  table.minimalistBlack td, table.minimalistBlack th {
    padding-top: 0px;
    padding-bottom: 0px; 
    padding-left: 5px;
    vertical-align: top;
    white-space: nowrap;
    font-family: Arial, Helvetica, sans-serif;
  }
    table.minimalistBlack tbody td {
        font-size: 16px;
    }
    
  .noborder tr td{
    border:none;
    padding-top: -3px;
    padding-bottom: -3px; 
    font-family: Arial, Helvetica, sans-serif;
  }
  table.headertable {
      width: 100%;
      border-spacing: 0px;
      border-width: 0.5px;
      font-size: 16px;
  }
  .headertable td, th{
    border: solid;
    border-width: 0.5px;
    vertical-align: top;
    white-space: nowrap;
    font-size: 16px;
  }
  .khusushead{
    border-left:none;
    border-right:none;
    border-bottom:none;
    border-top: 1px solid red;
    padding-bottom: 0;
    margin-bottom: 0;
    line-height: 30px;
    border-spacing: 0px;
  }

  @media print {
      table tbody tr td:before,
      table tbody tr td:after {
          content : "" ;
          height : 4px ;
          display : block ;
      }
  }

</style>

<!--Header-->
<div id="header" style="text-align:center;margin-bottom:0px;">  
  <h2>SURAT PENYERAHAN BARANG</h2>
</div>

<!--Footer-->
<div id="footer" style="text-align:center;margin-bottom:50px;font-size: 16px;">
  @include('do.docetak-footer')
</div>

<div id="detail" style="margin-top: 0px;padding-top:0px;" >
  @php($i = 1)
  @php($header = 1)
  @php($batas = 8)
  @php($flg = 1)
  @php($hal = 1)
  @php($jmlHal = ceil(($show->count() + $par->count()) / $batas))
  @php($halAnak = 0)
  <table style="width:100%; margin-top:0px; " class="minimalistBlack">
      @foreach($show as $s)
        @php($posisi1 = strpos($s->dod_part,"-"))
        @php($vol = substr($s->dod_part,($posisi1 + 1)))
        @php($posisi2 = strpos($vol,"-"))
        @php($vol = substr($vol,($posisi2 + 1)))
        @php($part = substr($s->dod_part,0,($posisi1 + $posisi2 + 1)))

        @if($header == 1)
          @include('do.docetak-header')
        @endif
        @php($header = 2)

        @php($pnjDesc = strlen($s->itemdesc))
	    @if($pnjDesc > 50)
	    	@php($flg += 1)
	    @endif

        <tr>
          <td width="5%" style="padding: 2px 2px; text-align: center; border-style: hidden solid; border-width: 0.5px;">{{$i}}</td>
          <td width="20%">
            <table width="100%" class="noborder" style="table-layout: fixed;border-collapse: collapse;">
              <tr>
                <td width="40%" style="padding: 2px 2px; text-align: right;">{{number_format($s->dod_qty,0)}} {{$s->so_um}}</td>
                <td width="60%" style="padding: 2px 2px; text-align: right; padding-right: 7px;">{{$vol}}</td>
              </tr>
            </table>
          </td>
          <td width="15%" style="padding: 2px 2px; border-style: hidden solid; border-width: 0.5px;padding-left: 10px;">{{$part}}</td>
          <td width="50%" style="padding: 2px 2px;">{{$s->itemdesc}}</td>
          <td width="10%" style="padding: 2px 2px; text-align: center;border-style: hidden solid; border-width: 0.5px;">{{substr($s->dod_so,2,6)}}</td>
        </tr>

        @if($flg == $batas and $hal != $jmlHal)
          @php($hal += 1)
          @php($header = 1)
          @php($flg = 0)
          <tr><td colspan="5" style="border-top-style: solid; border-width: 0.5px;"></td></tr>
          <tr class="pindah"></tr>
          <tr style="font-size:0;margin:0;line-height:0;border: none;">
            <td style="border: none;font-size:0;margin:0;line-height:0;"></td>
          </tr>
        @endif

        @foreach($par as $p)
	      @if($p->ps_par == $s->dod_part && $p->dod_line == $s->dod_line && $p->ps_comp != null && $p->dod_so == $s->dod_so)
          @php($flg += 1)
          @php($header = 2)
          
          @if($flg == 1 && $halAnak == 0)
            @include('do.docetak-header')
            @php($halAnak == 1)
          @endif

	        <tr>
	          <td style="border-style: hidden solid; border-width: 0.5px;"></td>
	          <td style="border-style: hidden solid; border-width: 0.5px;"></td>
	          <td style="padding: 2px 2px; border-style: hidden solid; border-width: 0.5px;padding-left: 10px;">{{$p->ps_comp}}</td>
	          <td style="border-style: hidden solid; border-width: 0.5px;"></td>
	          <td style="border-style: hidden solid; border-width: 0.5px;"></td>
		      </tr>
	        
	        @if($flg == $batas && $hal != $jmlHal)
	          	@php($hal += 1)
	            @php($header = 2)
	            @php($flg = 0)
	            @php($halAnak = 1)
		          <tr><td colspan="5" style="border-top-style: solid; border-width: 0.5px;"></td></tr>
		          <tr class="pindah"></tr>
		          <tr style="font-size:0;margin:0;line-height:0;border: none;">
		            <td style="border: none;font-size:0;margin:0;line-height:0;"></td>
		          </tr>
            	@include('do.docetak-header')
	        @endif
      	  @endif
	    @endforeach  

        @php($i = $i + 1)
        @php($flg += 1)
        @php($halAnak = 0)

        @if($loop->last)
        	<tr><td colspan="5" style="border-top-style: solid; border-width: 0.5px;"></td></tr>
        @endif

     @endforeach
  </table>
</div>

</body>
</html>

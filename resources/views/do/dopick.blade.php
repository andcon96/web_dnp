<!DOCTYPE html>
<html>
<head>
	<title>Picking List</title>
</head>
<body>
	<style type="text/css">
		@page { margin: 25px 30px 30px 30px; }

		#header { 
	    	position: fixed; 
	    	left: 0px; 
	    	top: 25px; 
	    	right: 0px;  
	    	text-align: center; 
	    	font-family: Arial, Helvetica, sans-serif;
	    }
	    .pindah{
	    	display: block;
			page-break-before: always;
	    }

	    .noborder{
	    	border: hidden;
	    }

		table.minimalistBlack {
            border: none;
            width: 100%;
            border-spacing: 0px;
            font-family: Arial, Helvetica, sans-serif;
        }
        table.minimalistBlack td {
            border: none;
			vertical-align: top;
			padding-top: 0px;
		    padding-bottom: 0px; 
		    font-family: Arial, Helvetica, sans-serif;
        }
        table.minimalistBlack tbody td {
            font-size: 16px;
        }
        table.minimalistBlack thead th {
            font-size: 20px !important;
            font-weight: bold;
            color: #000000;
            text-align: center;
            border-style: 20px solid;
            font-family: Arial, Helvetica, sans-serif;
        }
        table.minimalistBlack tfoot td {
            font-size: 16px;
        }
	</style>

	@php($header = 1)
	@php($i = 1)
	
	<!--------------- Display Header --------------->
	@php($nama = $mastr->cust_desc)
	@php($alamat = $mastr->cust_alamat)
	@php($tgl = $mastr->do_date)
	@php($status = $mastr->do_status)
	@php($noDo = $mastr->do_nbr)
	@php($top = $mastr->cust_top)

	@php($flg = 0)
	@php($hal = 1)
	@php($header = 1)
	
	@php($spb = substr($noDo,0,2).'-'.substr($noDo,2,1).'-'.substr($noDo,3,5))
	@php($batas = 18)
	@php($jmlHal = ceil(($show->count() + $par->count()) / $batas))
	@php($nambah = ceil(($show->count() + $par->count()) % $batas))
	
	<div id="header" style="text-align:left;">
		<h3 style="margin-top: -30px !important; padding: 4px;">DKH - PICKING LIST</h3>	    
    </div>

    <table style="width:100%;" class="minimalistBlack">
   	@foreach($show as $s)
   		@if($header == 1)
   			@include('do.doprint-pick')
		@endif
		@php($header = 2)
		@php($flg += 1)

		<tr>
			<td width="5%" style="padding: 2px 5px; text-align:center; border-left: 0.5px solid">{{$i}}</td>
			<td width="12%"  style="padding: 2px 15px; text-align:right; border-left: 0.5px solid">{{number_format($s->dod_qty,0)}}   {{$s->so_um}}</td>
			<td width="20%"  style="padding: 2px 10px; border-left: 0.5px solid">{{$s->dod_part}}</td>
			<td width="63%" style="padding: 2px 5px; border-left: 0.5px solid; border-right: 0.5px solid" colspan="3">{{$s->itemdesc}}</td>
		</tr>

		@if($flg == $batas  && $hal != $jmlHal)
	      @php($flg = 0)
	      @php($hal += 1)	  
	      <tr><td colspan="6" style="text-align:right; border-top: 0.5px solid"></td></tr>    
	      <tr class="pindah"></tr>
	      <tr style="font-size:0;margin:0;line-height:0;border: none;">
            <td style="border: none;font-size:0;margin:0;line-height:0;"></td>
          </tr>
          @include('do.doprint-pick')
	    @endif
			
		@foreach($par as $p)
			@if($p->ps_par == $s->dod_part && $p->dod_line == $s->dod_line && $p->ps_comp != null)
				@php($flg += 1)
				@php($header = 2)
				<tr>
					<td style="border-left: 0.5px solid"></td>
					<td style="border-left: 0.5px solid"></td>
					<td style="padding: 2px 10px; border-left: 0.5px solid">{{$p->ps_comp}}</td>
					<td style="border-left: 0.5px solid;border-right: 0.5px solid" colspan="3"></td>
				</tr>
                
                @if($flg == $batas  && $hal != $jmlHal)
			      	@php($flg = 0)
			      	@php($hal += 1)
			      	<tr><td colspan="6" style="text-align:right; border-top: 0.5px solid"></td></tr>
			      	<tr class="pindah"></tr>
				    <tr style="font-size:0;margin:0;line-height:0;border: none;">
			            <td style="border: none;font-size:0;margin:0;line-height:0;"></td>
			        </tr>
			        @include('do.doprint-pick')
			    @endif
			@endif
		@endforeach
		@php($i = $i + 1)
	@endforeach
		<tr><td colspan="6" style="text-align:right; border-top: 0.5px solid"></td></tr>
	</table>
	
</body>
</html>

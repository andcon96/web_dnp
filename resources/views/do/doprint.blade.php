<!DOCTYPE html>
<html>
<head>
	<title>Surat Permintaan Barang</title>
</head>
<body>
	<style type="text/css">
		@page { margin: 70px 30px 10px 30px; }

		#header { 
	    	position: fixed; 
	    	left: 0px; 
	    	top: -30px; 
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

	@if($status == 1)
		
		@php($batas = 15)
		@php($jmlHal = ceil(($show->count() + $par->count()) / $batas))
		@php($nambah = ceil(($show->count() + $par->count()) % $batas))
		
		<div id="header2" style="text-align:left;">
			<h2 style="margin-top: -30px !important;">DKH - PICKING LIST</h2>	    
	    </div>

	    <table style="width:100%;" class="minimalistBlack">
	   	@foreach($show as $s)
	   		@if($header == 1)
	   			@include('do.doprint-pick')
			@endif
			@php($header = 2)
    		@php($flg += 1)

			<tr>
				<td width="5%" style=" text-align:center; border-left: 0.5px solid">{{$i}}</td>
				<td width="10%"  style="padding-right: 5px; text-align:right; border-left: 0.5px solid">{{number_format($s->dod_qty,0)}}   {{$s->so_um}}</td>
				<td width="20%"  style="padding-left: 5px; border-left: 0.5px solid">{{$s->dod_part}}</td>
				<td width="65%" style="padding-left: 5px; border-left: 0.5px solid; border-right: 0.5px solid" colspan="3">{{$s->itemdesc}}</td>
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
						<td style="padding-left: 5px; border-left: 0.5px solid">{{$p->ps_comp}}</td>
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
	@else
		
		@php($batas = 14)
		@php($jmlHal = ceil(($show->count() + $par->count() + $show->sum('lnDesc')) / $batas))
		@php($nambah = ceil(($show->count() + $par->count() + $show->sum('lnDesc')) % $batas))
		
		<div id="header" style="text-align:center;">
			<h2 style="margin-top: -30px !important;">PT Dana Kharisma  </h2>	    
		    <h2 style="margin-bottom: 0px;margin-top: -20px !important;">LAMPIRAN SURAT PENGIRIMAN BARANG</h2>
	    </div>

	    <table id="detail" style="width:100%;margin-top: 0px !important;" class="minimalistBlack" >
		@php($tot = 0)
		@foreach($show as $s)
			@if($header == 1)
				@include('do.doprint-lamp')
			@endif
			@php($header = 2)
    		@php($flg += 1)

    		@php($posisi1 = strpos($s->dod_part,"-"))
		    @php($vol = substr($s->dod_part,($posisi1 + 1)))
		    @php($posisi2 = strpos($vol,"-"))
		    @php($vol = substr($vol,($posisi2 + 1)))
		    @php($part = substr($s->dod_part,0,($posisi1 + $posisi2 + 1)))
		    @php($pnjDesc = strlen($s->itemdesc))
			
			<tr>			
				@php($jml = floor($s->dod_qty*$s->so_harga))			
				<td width="4%" style="padding: 2px 5px; text-align:center; border-left: 0.5px solid;">{{$i}}</td>
				<td width="18%" style="padding: 2px 5px; border-left: 0.5px solid;">
					<table width="100%" class="noborder" style="table-layout: fixed;border-collapse: collapse;">
		              <tr>
		                <td width="40%" style="text-align: right;">{{number_format($s->dod_qty,0)}} {{$s->so_um}}</td>
		                <td width="60%" style="text-align: right;">{{$vol}}</td>
		              </tr>
		            </table>
				</td>
				<td width="15%" style="padding: 2px 5px; border-left: 0.5px solid"> {{$part}} {{$flg}} </td>
				<td width="31%" style="padding: 2px 5px; border-left: 0.5px solid;">{{$s->itemdesc}}  </td>
				<td width="11%" style="padding: 2px 5px; text-align:right; border-left: 0.5px solid">{{ number_format($s->so_pr_list,0) }}</td>
				<td width="8%" style="padding: 2px 5px; text-align:right; border-left: 0.5px solid">{{$s->so_disc}}</td>
				<td width="14%" style="padding: 2px 5px;  text-align:right; border-left: 0.5px solid; border-right: 0.5px solid">{{ number_format(floor($jml),0) }} </td>
				@php($tot = $tot + floor($jml))
			</tr>	

 			@if($pnjDesc > 30)
		    	@if($flg == 1)
		    		@php($flg = 2)
		    	@else
		    		@php($flg += 1)
		    	@endif
		    @endif
			
			@if(($flg == $batas or $flg > $batas) and $hal != $jmlHal)
			      	@php($flg = 0)
			      	@php($hal += 1)
			      	<tr><td colspan="7" style="border-top-style: solid; border-width: 0.5px;"></td></tr>
			      	<tr class="pindah"></tr>
			      	<tr style="font-size:0;margin:0;line-height:0;border: none;">
			            <td style="border: none;font-size:0;margin:0;line-height:0;"></td>
			        </tr>
			        @include('do.doprint-lamp')
			  	
		    @endif

			@foreach($par as $p)
				@if($p->ps_par == $s->dod_part && $p->dod_line == $s->dod_line && $p->ps_comp != null)
					@php($flg += 1)
					@php($header = 2)
					<tr>
						<td style="border-left: 0.5px solid;"></td>
						<td style="border-left: 0.5px solid;"></td>
						<td style="border-left: 0.5px solid; padding: 2px 5px;">{{$p->ps_comp}} </td>
						<td style="border-left: 0.5px solid;"></td>
						<td style="border-left: 0.5px solid;"></td>
						<td style="border-left: 0.5px solid;"></td>
						<td style="border-left: 0.5px solid;  border-right: 0.5px solid"></td>
					</tr>

					@if($flg == $batas && $hal != $jmlHal)
				      	@php($flg = 0)
				      	@php($hal += 1)
				      	<tr><td colspan="7" style="border-top-style: solid; border-width: 0.5px;"></td></tr>
				      	<tr class="pindah"></tr>
				      	<tr style="font-size:0;margin:0;line-height:0;border: none;">
				            <td style="border: none;font-size:0;margin:0;line-height:0;"></td>
				        </tr>
				        @include('do.doprint-lamp')
				    @endif
				@endif
			@endforeach

		@php($i = $i + 1) 
		
		@endforeach
		<tr>
			<td colspan="5" style="text-align:center; border-top: 0.5px solid"></td>
			<td style="padding: 2px 5px; text-align:center; border-style: solid; border-width: 0.5px;"><b>Total</b></td>
			<td style="padding: 2px 5px; text-align:right; border-style: solid; border-width: 0.5px;">{{ number_format(floor($tot),0)}}</td>
		</tr>
		</table>
	@endif
</body>
</html>

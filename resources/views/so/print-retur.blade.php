<!DOCTYPE html>
<html>
<head>
	<title>Return DKH</title>
</head>
<body>
	<style type="text/css">
		/* table tr td,
		table tr th{
			font-size: 10pt;
		} */
		@page { margin: 130px 50px 50px 50px; }
	    #header { 
	    	position: fixed; 
	    	left: 0px; 
	    	top: -130px; 
	    	right: 0px;  
	    	text-align: center; 
	    }
	    .pindah{
	    	/*page-break-after: always;*/
	    	display: block;
			page-break-before: always;
	    }

	    .test{
	    	border: 1px solid black;
	    }

		table.minimalistBlack {
            width: 100%;
            border-spacing: 0px;
        }
        table.minimalistBlack td, table.minimalistBlack th {
            border: 0.5px solid #000000;
            padding: 5px 4px;
			vertical-align: top;
    		white-space: nowrap;
        }
        table.minimalistBlack tbody td {
            font-size: 14px;
        }
        #footer {
		  position: absolute;
		  bottom: -25px;
		  width: 100%;
		  height: 0px;
		  text-align: right;
		}
		.noborder tr td{
			border:none;
		}

		table.headertable {
            width: 100%;
            border-spacing: 0px;
            border: 0.5px solid #000000;
        }
		.headertable td, th{
			border: 0.5px solid #000000;
			vertical-align: top;
    		white-space: nowrap;
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

	@php($awal = strtotime($header->sodate))
    <!--Header-->
    <div id="header" style="text-align:center;margin-bottom:50px;">
    	<h2 style="margin-bottom:0px;">PT. Dana Kharisma</h2>
        <h5 style="margin-top:0px;">{{$alamat->site_addr}}</h5>
    
        <h2 style="margin-bottom:7px;">SURAT PENERIMAAN RETUR BARANG</h2>
    </div>

    <div id="detail" style="margin-top:0px;padding-top:0px;">		
		<table style="width:100%; margin-top:0px;" class="minimalistBlack">
			@php($flg = 0)
			@php($hal = 1)
			@php($head = 1)
			@foreach($data as $data)
				<!--Table Isi-->
				@if($head == 1)
				<tr>
					<td colspan="4" style="border:hidden;text-align: right">Hal. {{$hal}}</td>
				</tr>
				<tr>
					<td colspan="4" style="padding: 0px;">
						<table style="border-collapse: collapse;width: 100%">
							<tr>
								<td colspan="2" rowspan="3" width="60%">
									<table width="100%" style="table-layout: fixed;border-collapse: collapse;" class="noborder">
										<tr>
											<td><b>Kepada Yth.</b></td>
										</tr>
										<tr>
											<td style="padding-left: 15px;">{{$header->cust_desc}}</td>
										</tr>
										<tr>
											<td style="width: 100%;white-space: normal;padding-left: 15px;">{{$header->cust_alamat}}</td>
										</tr>
									</table>
								</td>
								<td><b>No. Retur</b></td>
								<td>{{$header->so_nbr}}</td>
							</tr>
							<tr>
								<td><b>Tgl. Retur</b></td>
								<td>{{date('d F Y',$awal)}}</td>
							</tr>
							<tr>
								<td><b>Nama Ekspedisi</b></td>
								<td></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<th width="5%">No</th>
					<th width="18%">Kuantitas</th>
					<th width="22%">Item Code</th>
					<th width="55%">Item Desc</th>
				</tr>
				@endif

				@php($flg += 1)
				@php($head += 1)
				<tr>
					<td style="text-align:right">{{$loop->iteration}}</td>
					<td style="text-align:center">{{$data->so_qty}} {{$data->item_um}}</td>
					<td>{{$data->so_itemcode}}</td>
					<td>{{$data->itemdesc}}</td>
				</tr>

				@if($flg == 17)
					@php($flg = 0)
					@php($hal += 1)
					@if(!$loop->last)
					<tr class="pindah"></tr>

					<tr style="font-size:0;margin:0;line-height:0;border: none;">
						<td style="border: none;font-size:0;margin:0;line-height:0;"></td>
					</tr>

					@php($head = 1)

					@endif
					
				@endif

				@if($loop->last)	
					<tr>
						<table width="100%">
							<tr style="border: none;">
								<td style="text-align: center;padding-top:20px;border: none;" colspan="2"><b>Diterima Oleh :</b></td>
								<td style="text-align: center;padding-top:20px;border: none;" colspan="2"><b>Diserahkan Oleh :</b></td>
								<td style="text-align: center;padding-top:20px;border: none;" colspan="2"><b>Dibuat Oleh :</b></td>
								<td style="text-align: center;padding-top:20px;border: none;" colspan="2"><b>Disetujui Oleh :</b></td>
							</tr>
							<tr style="border: none;">
								<td style="line-height: 10;text-align: center;border: none;">(</td>
								<td style="line-height: 10;text-align: center;border: none;">)</td>
								<td style="line-height: 10;text-align: center;border: none;">(</td>
								<td style="line-height: 10;text-align: center;border: none;">)</td>
								<td style="line-height: 10;text-align: center;border: none;">(</td>
								<td style="line-height: 10;text-align: center;border: none;">)</td>
								<td style="line-height: 10;text-align: center;border: none;">(</td>
								<td style="line-height: 10;text-align: center;border: none;">)</td>
							</tr>
						</table>
					</tr>
				@endif

			@endforeach
		</table>	
	</div>

</body>
</html>
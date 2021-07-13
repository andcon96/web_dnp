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
		@page { margin: 250px 50px 50px 50px; }
	    #header { 
	    	position: fixed; 
	    	left: 0px; 
	    	top: -236px; 
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
            border: 0.5px solid #000000;
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
    <div id="header" style="text-align:center;margin-bottom:20px;">
    	<h2 style="margin-bottom:0px;">PT. Dana Kharisma</h2>
        <h5 style="margin-top:0px;">{{$alamat->site_addr}}</h5>
    
        <h2 style="margin-bottom:7px;">SURAT PENERIMAAN RETUR BARANG</h2>

        <table style="width:100%;height: 123px;border:0.5px solid black;" class="headertable">
			<tr>
				<td width="60%" style="vertical-align:top;" rowspan="3">
					<table width="100%" style="table-layout: fixed;border:hidden;" class="noborder">
						<tr>
							<td><b>Kepada Yth.</b></td>
						</tr>
						<tr>
							<td>{{$header->cust_desc}}</td>
						</tr>
						<tr>
							<td style="width: 100%;white-space: normal;">{{$header->cust_alamat}}</td>
						</tr>
					</table>
				</td>
				<td width="20%" style="height:38px;vertical-align:top;"><b>No. Retur</b></td>
				<td width="20%" style="vertical-align:top;"> {{$header->so_nbr}}</td>
			</tr>
			<tr>
				<td style="height:38px;vertical-align:top;"><b>Tgl. Retur</b></td>
				<td style="vertical-align:top;">{{date('d F Y',$awal)}}</td>
			</tr>
			<tr>
				<td style="height:38px;vertical-align:top;"><b>Nama Ekspedisi</b></td>
				<td style="vertical-align:top;" ></td>
			</tr>
		</table>
    </div>

    <div id="detail" style="margin-top:9px;padding-top:0px;">
		
		<!--Isi Table-->
		<table style="width:100%; margin-top:0px;" class="minimalistBlack">
			<tr>
				<th width="5%">No</th>
				<th width="18%">Kuantitas</th>
				<th width="22%">Item Code</th>
				<th width="55%">Item Desc</th>
			</tr>
			@php($flg = 0)
			@php($hal = 1)
			@foreach($data as $data)
				@php($flg += 1)
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
					<tr>
						<th width="5%">No</th>
						<th width="18%">Kuantitas</th>
						<th width="22%">Item Code</th>
						<th width="55%">Item Desc</th>
					</tr>
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
<!DOCTYPE html>
<html>
<head>
	<title>Surat Permintaan Barang</title>
</head>
<body>
<style type="text/css">
	table.minimalistBlack {
	  border: 3px solid #000000;
	  width: 100%;
	  text-align: left;
	  border-collapse: collapse;
	}
	table.minimalistBlack td, table.minimalistBlack th {
	  border: 1px solid #000000;
	  padding: 5px 4px;
	}
	table.minimalistBlack tbody td {
	  font-size: 13px;
	}
	table.minimalistBlack thead {
	  background: #CFCFCF;
	  background: -moz-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
	  background: -webkit-linear-gradient(top, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
	  background: linear-gradient(to bottom, #dbdbdb 0%, #d3d3d3 66%, #CFCFCF 100%);
	  border-bottom: 3px solid #000000;
	}
	table.minimalistBlack thead th {
	  font-size: 15px;
	  font-weight: bold;
	  color: #000000;
	  text-align: left;
	}
	table.minimalistBlack tfoot {
	  font-size: 14px;
	  font-weight: bold;
	  color: #000000;
	  border-top: 3px solid #000000;
	}
	table.minimalistBlack tfoot td {
	  font-size: 14px;
	}
</style>

	<h1>Surat Permintaan Barang</h1>
	@php($header = 1)
	@php($i = 1)
	
	<!--------------- Display Header --------------->
	@foreach($mastr as $m)
		Nomor SPB 	: {{$m->do_nbr}} <br>
		Tanggal SPB : {{$m->do_date}} <br><br>
		Customer	: {{$m->do_cust}} - {{$m->cust_desc}}<br>
					  {{$m->cust_alamat}} <br><br>
		Ship To 	: {{$m->do_shipto}}<br><br>
	@endforeach
	<!--------------- Display Detail --------------->
	<table class="minimalistBlack">
	@foreach($show as $s)
		@if($header == 1)
		<thead>
		<tr>
			<th>No</th>
			<th>Nomor Order</th>
			<th>Item</th>
			<th>Deskripsi</th>
			<th>UM</th>
			<th>Quantity</th>
		</tr>
		<thead>
		@endif
		<tbody>
		<tr>
			<td>{{$i}}</td>
			<td>{{$s->dod_so}}</td>
			<td>{{$s->dod_part}}</td>
			<td>{{$s->itemdesc}}</td>
			<td>{{$s->do_um}}</td>
			<td>{{$s->dod_qty}}</td>
		</tr>
		</tbody>
		@php($i = $i + 1)
		@php($header = 2)
	@endforeach
	</table>
</body>
</html>

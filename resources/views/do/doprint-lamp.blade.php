<tr>
	<td colspan="7" style="text-align: right; padding-right: 25px;">Hal : {{$hal}} / {{$jmlHal}}</td>
</tr>
<tr>
	<td colspan="7">
		<table width="100%" style="table-layout: fixed; border-collapse: collapse;">
			<tr>
				<td width="67%"><b>Kepada Yth : </b> {{$nama}}</td>
				<td width="5%">&nbsp;</td>
				<td width="15%">No SPB</td>
				<td width="13%">: {{$spb}}</td>
			</tr>
			<tr>
				<td rowspan = "2" style=" padding-left: 15px;">{{$alamat}}</td>
				<td rowspan = "2">&nbsp;</td>
				<td>Tgl SPB</td>
				<td>: {{date('d-m-Y', strtotime($tgl))}} </td>
			</tr>
			<tr>
				<td>Tgl Jatuh Tempo</td>
				<td>: {{date('d-m-Y', strtotime('+'.$top.' days', strtotime($tgl)))}}</td>
			</tr>
		</table>
	</td>
</tr>
<tr>
	<td style="padding: -5px">&nbsp;</td>
</tr>
<tr>
	<th style="border-style: solid; border-width: 0.5px;">No</th>
	<th style="border-style: solid; border-width: 0.5px;">Kuantitas</th>
	<th style="border-style: solid; border-width: 0.5px;">Kode Barang</th>
	<th style="border-style: solid; border-width: 0.5px;">Nama Barang</th>
	<th style="border-style: solid; border-width: 0.5px;">Harga Satuan</th>
	<th style="border-style: solid; border-width: 0.5px;">Diskon (%)</th>
	<th style="border-style: solid; border-width: 0.5px;">Jumlah</th>
</tr>

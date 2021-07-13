@foreach($ship as $s)
  @php($shipname = $s->custname)
  @php($shipaddr = $s->custaddr)
@endforeach

@php($nama    = $mastr->cust_desc)
@php($alamat  = $mastr->cust_alamat)
@php($donbr   = $mastr->do_nbr)
@php($tgl     = $mastr->do_date)

@if($ship->count() == 0)
  @php($shipname = $nama )
  @php($shipaddr = $alamat)
@endif

@if($cust_nm != "")
  @php($nama    = $cust_nm)
  @php($alamat  = $cust_almt)
@endif

@php($spb = substr($donbr,0,2).'-'.substr($donbr,2,1).'-'.substr($donbr,3,5))

<tr>
  <td colspan="5">
    <table width="100%" style="table-layout: fixed;" >
      <tr>
        <td ><b>Kepada Yth : </b> {{$nama}}</td>
        <td >&nbsp;</td>
        <td colspan="2" style="text-align: right;">Hal : {{$hal}} / {{$jmlHal}}</td>
      </tr>
      <tr>
        <td width="70%" rowspan="2" style="padding-left: 20px;white-space: normal;">{{$alamat}}</td>
        <td width="5%" rowspan="2" >&nbsp;</td>
        <td width="15%">No SPB</td>
        <td width="10%">: {{$spb}}</td>
      </tr>
      <tr>
        <td>Tgl SPB</td>
        <td>: {{date('d-m-Y', strtotime($tgl))}}</td>
      </tr>
      <tr>
        <td><b>Diserahkan Kepada :</b> {{$shipname}} </td>
        <td>&nbsp;</td>
        <td>Nama Ekspedisi</td>
        <td>:</td>
      </tr>
      <tr>
        <td rowspan="2" style="padding-left: 20px;white-space: normal;">{{$shipaddr}}</td>
        <td rowspan="2" >&nbsp;</td>
        <td>No. Kendaraan</td>
        <td>:</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="4"><b>No. PO : </b> {{$so}}</td>
      </tr>
    </table>
  </td>
</tr>
<tr>
  <td style="padding: -10px">&nbsp;</td>
</tr>
<tr>
  <th>No</th>
  <th>Banyaknya</th>
  <th>Kode Barang</th>
  <th>Nama Barang</th>
  <th>No. SO</th>
</tr>  
 
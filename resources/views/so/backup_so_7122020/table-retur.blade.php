@forelse ($data as $show)
  <tr class="foottr">
    <td class="foot3" data-label="Nomor Retur">{{ $show->so_nbr }}</td>
    <td class="foot3" data-label="Site">{{ $show->so_site }}</td>
    <td class="foot2" data-label="Pelanggan">{{$show->so_cust}} -- {{ $show->cust_desc }}</td>
    <td class="foot2" data-label="Ship To">{{ $show->so_shipto }} -- {{$show->shipto_nama}}</td>
    <td class="foot3" data-label="Nomor SO">{{ $show->so_so_awal }}</td>
    <td class="foot2" data-label="Remarks">{{ $show->so_remarks }}</td>
    <td class="footend" data-label="Aksi">
        <a href="" class="viewmodal" data-toggle="modal" data-target="#viewModal" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"  data-shipto="{{$show->so_shipto}}" 
        data-namacust="{{$show->cust_desc}}" data-namaship="{{$show->shipto_nama}}"
        ><i class="icon-table fas fa fa-eye fa-lg"></i></a>
    </td>
  </tr>
@empty
	<tr>
		<td colspan="7" style="color:red;"> <center>No Data Available</center></td>
	</tr>
@endforelse 	
	<tr style="border: none !important;">
		<td>
			{!! $data->links() !!}
		</td>
	</tr>
	


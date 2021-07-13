@forelse ($data as $show)
  <tr class="foottr">
    <td class="foot1" data-label="Nomor SO">{{ $show->so_nbr }}</td>
    <td class="foot2" data-label="Pelanggan">{{ $show->cust_desc }}</td>
    <td class="foot2" data-label="Tanggal">{{ date('Y-m-d', strtotime($show->so_created)) }}</td>
    
    <td class="foot2" data-label="Batas Waktu">{{ $show->so_duedate }}</td>
    <td class="foot2" data-label="Harga">
        {{ strpos($show->so_price,".00000") !== false ? number_format($show->so_price,2,'.',',') : (strpos(strrev(rtrim(($show->so_price), "0")), ".") == 1  ? number_format($show->so_price,2,'.',',') : rtrim(number_format($show->so_price,5,'.',','), "0") )}}
    </td>
    @if(Session::get('salesman') != 'Y')
    <td>
        {{$show->so_user}}
    </td>
    @endif
    <td class="footend">
    <a href="" class="viewmodal showact" data-toggle="modal" data-target="#viewModal" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"
        data-alamat="{{$show->cust_alamat}}" data-duedate="{{$show->so_duedate}}"
        data-shipto="{{$show->so_shipto}}" data-remarks="{{$show->so_notes}}"
        data-custdesc="{{$show->cust_desc}}" data-tooltip="View SO"
        ><i class="icon-table fas fa fa-eye fa-lg"></i></a>
    </td>
  </tr>
@empty
	<tr>
		<td colspan="7" style="color:red;"> <center>No Data Available</center></td>
	</tr>
@endforelse 	
	<tr style="border: none !important;">
		<td colspan="7">
			{!! $data->onEachSide(1)->links() !!}
		</td>
	</tr>
	


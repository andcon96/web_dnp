@forelse ($data as $show)
  <tr class="foottr">
    <td class="foot3" data-label="Nomor SO">{{ $show->so_nbr }}</td>
    <td class="foot2" data-label="Pelanggan">{{ $show->cust_desc}}</td>
    <td class="foot2" data-label="Batas Waktu">{{ $show->so_duedate }}</td>
    <td class="foot2" data-label="Harga">{{ strpos($show->so_price,".00000") !== false ? number_format($show->so_price,2,'.',',') : (strpos(strrev(rtrim(($show->so_price), "0")), ".") == 1  ? number_format($show->so_price,2,'.',',') : rtrim(number_format($show->so_price,5,'.',','), "0") )}}</td>
    <td class="foot2" data-label="Next Approver">
        {{$show->name}}
    </td>
    <td class="footend">
        <a href="" class="editmodal showact" data-toggle="modal" data-target="#viewModal" data-sonbr="{{$show->so_nbr}}" data-desc="{{$show->cust_desc}}"
            data-duedate="{{$show->so_duedate}}" data-ammount="{{$show->so_price}}"
            data-reason="{{$show->so_status}}" data-nextapp="{{$show->approval_approver}}"
            data-nextorder="{{$show->approval_seq}}" data-session="{{Session::get('username')}}"
            data-tooltip="View SO"> 
            <i class="icon-table fa fa-edit fa-lg"></i></a>
    </td>
  </tr>
@empty
	<tr>
		<td colspan="7" style="color:red;"> <center>No Data Available</center></td>
	</tr>
@endforelse 	
	<tr style="border: none !important;">
		<td colspan="7"> 
			{!! $data->links() !!}
		</td>
	</tr>
	


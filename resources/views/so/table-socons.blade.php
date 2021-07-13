@forelse ($data as $show)
  <tr class="foottr">
    <td class="foot3" data-label="Nomor SO">{{ $show->so_nbr }}</td>
    <td class="foot2" data-label="Pelanggan">{{ $show->cust_desc }}</td>
    <td class="foot2" data-label="Tanggal">{{ date('Y-m-d', strtotime($show->so_created)) }}</td>
    <td class="foot2" data-label="Batas Waktu">{{ $show->so_duedate }}</td>
    <td class="foot2" data-label="Status">
    	@if($show->so_status == 1)
    		Created
    	@elseif($show->so_status == 2 or $show->so_status == 3 or $show->so_status == 4)
            On Hold
        @elseif($show->so_status == 5)
            Deleted
        @elseif($show->so_status == 6)
            Rejected
	    @elseif($show->so_status == 10)
            Created
        @elseif($show->so_status == 11)
            Confirmed
        @elseif($show->so_status == 12)
            Deleted
    	@endif
    </td>
    <td class="foot2" data-label="Harga">
        {{ strpos($show->so_price,".00000") !== false ? number_format($show->so_price,2,'.',',') : (strpos(strrev(rtrim(($show->so_price), "0")), ".") == 1  ? number_format($show->so_price,2,'.',',') : rtrim(number_format($show->so_price,5,'.',','), "0")) }}
    </td>
    <td class="footend" data-label="Aksi">
        <a href="" class="viewmodal showact" data-toggle="modal" data-target="#viewModal" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"
        data-alamat="{{$show->cust_alamat}}" data-duedate="{{$show->so_duedate}}"
        data-shipto="{{$show->so_shipto}}" data-remarks="{{$show->so_notes}}"
        data-custdesc="{{$show->cust_alt_name}}" data-tooltip="View SO"
        data-brelname="{{$show->cust_desc}}"
        ><i class="icon-table fas fa fa-eye fa-lg"></i></a>

        @if(!str_contains( Session::get('menu_access'),'TS10'))
        @if($show->so_status == 10)
        <a href="" class="editmodal showact" data-toggle="modal" data-target="#editModal" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"
        data-alamat="{{$show->cust_alamat}}" data-duedate="{{$show->so_duedate}}"
        data-shipto="{{$show->so_shipto}}" data-remarks="{{$show->so_notes}}"
        data-custdesc="{{$show->cust_desc}}" data-tooltip="Edit SO"
        ><i class="icon-table fas fa fa-edit fa-lg"></i></a>

        <a href="" class="confmodal showact" data-toggle="modal" data-target="#confModal" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"
        data-alamat="{{$show->cust_alamat}}" data-duedate="{{$show->so_duedate}}"
        data-shipto="{{$show->so_shipto}}" data-remarks="{{$show->so_notes}}"
        data-custdesc="{{$show->cust_alt_name}}" data-tooltip="Confirm SO"
        data-brelname="{{$show->cust_desc}}"
        ><i class="icon-table fas fa fa-check-circle fa-lg"></i></a>

        <a href="" class="deletemodal showact" data-toggle="modal" data-target="#deleteModal" 
        data-sonbr="{{$show->so_nbr}}" data-tooltip="Delete SO"
        ><i class="icon-table fas fa fa-trash fa-lg"></i></a>
        @endif
        @endif
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
	


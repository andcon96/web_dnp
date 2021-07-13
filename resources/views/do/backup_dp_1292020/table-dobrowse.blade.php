@forelse ($data as $show)
  <tr>
    <td class="foot1" data-label="Nomor DO">{{ $show->do_nbr }}</td>
    <td class="foot1" data-label="Pelanggan">{{ $show->cust_desc }}</td>
    <td class="foot1" data-label="Batas Waktu">{{ $show->do_date }}</td>
    @if($show->do_status == 1)
        <td class="foot1" data-label="Status">Open</td>
    @elseif($show->do_status == 2)
        <td class="foot1" data-label="Status">Confirm</td>
    @else
        <td class="foot1" data-label="Status">Delete</td>
    @endif
    <td class="footend" data-label="Aksi">
        <a href="" class="viewmodal" data-toggle="modal" data-target="#viewModal" 
        data-nbr="{{$show->do_nbr}}" data-cust="{{$show->do_cust}}"
        data-alamat="{{$show->cust_alamat}}" data-dodate="{{$show->do_date}}"
        data-shipto="{{$show->do_shipto}}" data-note="{{$show->do_notes}}"
        data-custdesc="{{$show->cust_desc}}" data-site="{{$show->do_site}}"
        ><i class="icon-table fas fa fa-eye fa-lg"></i></a>
        @if($show->do_status == 1)
        <a href="dotemp?donbr={{$show->do_nbr}}" class="editmodal">
        <i class="icon-table fas fa fa-edit fa-lg"></i></a>
        @endif
        @if($show->do_status == 1)
        <a href="" class="soshipmentgetinfo" data-toggle="modal" data-target="#confirmodal" 
        data-do_nbr="{{$show->do_nbr}}" data-do_cust="{{$show->do_cust}}" 
        data-do_custname="{{$show->cust_desc}}" ><i class="icon-table fas fa-check-circle fa-lg"></i></a>
        @endif
        @if($show->do_status == 1 || $show->do_status == 2)
        <a href="doprint?donbr={{$show->do_nbr}}" class="printmodal">
        <i class="icon-table fas fa fa-print fa-lg"></i></a>
        @endif
        @if($show->do_status == 1 || $show->do_status == 2)
        <a href="" class="deletemodal" data-toggle="modal" data-target="#deleteModal" 
        data-donbr="{{$show->do_nbr}}" 
        ><i class="icon-table fas fa fa-trash fa-lg"></i></a>
        @endif
        
    </td>
  </tr>
@empty
	<tr>
		<td colspan="5" style="color:red;"> <center>No Data Available</center></td>
	</tr>
@endforelse 	
	<tr style="border: none !important;">
		<td>
			{!! $data->links() !!}
		</td>
	</tr>
	


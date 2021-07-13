@forelse ($data as $show)
  <tr>
    <td class="foot1" data-label="Nomor DO">{{ $show->do_nbr }}</td>
    <td class="foot1" data-label="Pelanggan">{{ $show->do_cust }} - {{ $show->cust_desc }}</td>
    <td class="foot1" data-label="Batas Waktu">{{ $show->do_date }}</td>
    @if($show->do_status == 1)
        <td class="foot1" data-label="Status">Waiting</td>
    @elseif($show->do_status == 2)
        <td class="foot1" data-label="Status">Confirm</td>
    @elseif($show->do_status == 4)
        <td class="foot1" data-label="Status">Ready to Ship</td>
    @else
        <td class="foot1" data-label="Status">Delete</td>
    @endif

    <td class="footend" data-label="Aksi">
        <a href="" class="viewmodal showact" data-toggle="modal" data-tooltip="View SPB" data-target="#viewModal" 
        data-nbr="{{$show->do_nbr}}" data-cust="{{$show->do_cust}}"
        data-alamat="{{$show->cust_alamat}}" data-dodate="{{$show->do_date}}"
        data-shipto="{{$show->do_shipto}}" data-note="{{$show->do_notes}}"
        data-custdesc="{{$show->cust_desc}}" data-site="{{$show->do_site}}"
        ><i class="icon-table fas fa fa-eye fa-lg"></i></a>
    @if(!str_contains( Session::get('menu_access'),'TSV'))    
	@if($show->do_status == 1)
        <a href="dotemp?donbr={{$show->do_nbr}}&act={{$show->do_status}}"  data-tooltip="Edit SPB" class="editmodal showact">
        <i class="icon-table fas fa fa-edit fa-lg"></i></a>
        <a href="dotemp?donbr={{$show->do_nbr}}&act=4"  data-tooltip="SPB ready to ship" class="redmodal showact">
        <i class="icon-table fas fa-truck"></i></a>
        @endif
        
	@if($show->do_status == 4)
	<a href="dotemp?donbr={{$show->do_nbr}}&act={{$show->do_status}}" data-tooltip="Edit SPB" class="editmodal showact">
        <i class="icon-table fas fa fa-edit fa-lg"></i></a>        
	<a href="" class="soshipmentgetinfo showact" data-toggle="modal" data-tooltip="Confirm SPB" data-target="#confirmodal" 
        data-do_nbr="{{$show->do_nbr}}" data-do_cust="{{$show->do_cust}}" 
        data-do_custname="{{$show->cust_desc}}" ><i class="icon-table fas fa-check-circle fa-lg"></i></a>
        @endif
        
	@if($show->do_status == 1 || $show->do_status == 2 || $show->do_status == 4)
        <a href="docetak?donbr={{$show->do_nbr}}&prt=1" target="_blank"   class="printmodal showact" data-tooltip="Print SPB">
        <i class="icon-table fas fa fa-print fa-lg"></i></a>
    @endif

    @if($show->do_status == 1)
        <a href="doprint?donbr={{$show->do_nbr}}&prt=2" target="_blank" class="printmodal showact" data-tooltip="Print Picking List">
        <i class="icon-table fas fa fa-file fa-lg"></i></a>
    @endif

    @if($show->do_status == 4  || $show->do_status == 2)
        <a href="doprint?donbr={{$show->do_nbr}}&prt=2" target="_blank" class="printmodal showact" data-tooltip="Print Lampiran SPB">
        <i class="icon-table fas fa fa-file fa-lg"></i></a>
    @endif
        
	@if($show->do_status == 1 || $show->do_status == 4)
       	<a href="" class="deletemodal showact" data-toggle="modal" data-tooltip="Delete SPB" data-target="#deleteModal" 
        data-donbr="{{$show->do_nbr}}" 
        ><i class="icon-table fas fa fa-trash fa-lg"></i></a>
        @endif
       
    </td>
    @endif
  </tr>
@empty
	<tr>
		<td colspan="5" style="color:red;"> <center>No Data Available</center></td>
	</tr>
@endforelse 	
	<tr style="border: none !important;" >
		<td colspan = 5>
			{!! $data->links() !!}
		</td>
	</tr>
	


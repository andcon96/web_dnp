@forelse ($data as $show)
  <tr class="foottr">
    <td class="foot3" data-label="Nomor Retur">{{ $show->so_nbr }}</td>
    <td class="foot3" data-label="Site">{{ $show->so_site }}</td>
    <td class="foot2" data-label="Pelanggan">{{$show->so_cust}} -- {{ $show->cust_desc }}</td>
    <td class="foot2" data-label="Ship To">{{ $show->so_shipto }}</td>
    <td class="foot2" data-label="Ship To">{{ $show->sodate }}</td>
    <td class="foot2" data-label="Remarks">{{ $show->so_remarks }}</td>
    <td class="foot2" data-label="Remarks">
        @if($show->so_status == '1')
            Created
        @elseif($show->so_status == '2')
            Confirmed
        @elseif($show->so_status == '3')
            Deleted
        @endif
    </td>
    <td class="footend" data-label="Aksi">
        <a href="" class="viewmodal showact" data-toggle="modal" data-target="#viewModal" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"  data-shipto="{{$show->so_shipto}}" 
        data-namacust="{{$show->cust_alt_name}}" data-namaship="{{$show->shipto_nama}}" data-pricedate="{{$show->price_date}}"
        title="View SO" data-tooltip="View SO" data-brelname="{{$show->cust_desc}}"
        ><i class="icon-table fas fa fa-eye fa-lg"></i></a>

        @if(!str_contains( Session::get('menu_access'),'TS10'))
        @if($show->so_status == '1')
         <a href="" class="editmodalweb showact" data-toggle="modal" data-target="#editmodalweb" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"  data-shipto="{{$show->so_shipto}}" 
        data-namacust="{{$show->cust_desc}}" data-namaship="{{$show->shipto_nama}}" data-desc="{{$show->cust_desc}}" data-pricedate="{{$show->price_date}}"
        data-remarks="{{$show->so_remarks}}" title="Edit SO" data-tooltip="Edit SO"
        ><i class="icon-table fas fa fa-edit fa-lg"></i></a>
        
        <a href="" class="editmodal showact" data-toggle="modal" data-target="#editModal" 
        data-sonbr="{{$show->so_nbr}}" data-cust="{{$show->so_cust}}"  data-shipto="{{$show->so_shipto}}" 
        data-namacust="{{$show->cust_desc}}" data-namaship="{{$show->shipto_nama}}" data-desc="{{$show->cust_desc}}" data-pricedate="{{$show->price_date}}"
        data-remarks="{{$show->so_remarks}}" title="Confirm SO" data-tooltip="Confirm SO"
        ><i class="icon-table fas fa fa-check-circle fa-lg"></i></a>

        <a href="" class="deletemodal showact" data-toggle="modal" data-target="#delModal" 
        data-sonbr="{{$show->so_nbr}}" title="Delete SO" data-tooltip="Delete SO"
        ><i class="icon-table fas fa fa-trash fa-lg"></i></a>
        @endif
        
        <a href="returpdf?sonbr={{$show->so_nbr}}" target="_blank" class="printmodal showact" data-tooltip="Print Retur">
        <i class="icon-table fas fa fa-print fa-lg"></i></a>
        @endif
    </td>
  </tr>
@empty
	<tr>
		<td colspan="8" style="color:red;"> <center>No Data Available</center></td>
	</tr>
@endforelse 	
	<tr style="border: none !important;">
		<td colspan="8">
			{!! $data->links() !!}
		</td>
	</tr>
	


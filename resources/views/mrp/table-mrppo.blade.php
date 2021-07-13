@forelse ($data as $show)
  <tr class="foottr">
  	<td>{{$loop->iteration}}</td>
    <td class="foot2" data-label="Item Code">{{ $show->item_part }}</td>
    <td class="foot2" data-label="Item Desc">{{ $show->item_desc }}</td>
    <td class="foot2" data-label="Item Type">{{ $show->item_type }}</td>
    <td class="foot2" data-label="Qty PO">{{ $show->qty_po }}</td>
  </tr>
@empty
  <tr>
    <td colspan="4" style="color:red;"> <center>No Data Available</center></td>
  </tr>
@endforelse 
  


@forelse($data as $item)
 	<tr>
 		<td>{{$item->itemcode}}</td>
		 <td>{{$item->itemdesc}}</td>
		 <td>{{$item->safety_stock}}</td>
		 <td>{{$item->item_um}}</td>
		 <td>{{$item->item_site}}</td>

		 <td>{{$item->item_location}}</td>
 	</tr>
 @empty
 	<tr>
 		<td colspan="6" style="color:red"><center>No Data Available</center></td>
 	</tr>
 @endforelse  
 	<tr style="border-bottom:none !important;">
 		<td colspan="6">
 			{{$data->links()}}
 		</td>
 	</tr>
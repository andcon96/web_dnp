@forelse($data as $itemkonv)
 	<tr>
 		<td>{{$itemkonv->item_code}}</td>
		 <td>{{$itemkonv->um_1}}</td>
		 <td>{{$itemkonv->um_2}}</td>
		 <td>{{$itemkonv->qty_item}}</td>
 	</tr>
 @empty
 	<tr>
 		<td colspan="4" style="color:red"><center>No Data Available</center></td>
 	</tr>
 @endforelse  
 	<tr style="border-bottom:none !important;">
 		<td colspan="4">
 			{{$data->links()}}
 		</td>
 	</tr>
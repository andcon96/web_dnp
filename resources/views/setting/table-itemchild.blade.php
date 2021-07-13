@forelse($data as $itemkonv)
 	<tr>
 		<td>{{$itemkonv->item_code}}</td>
 		<td>{{$itemkonv->item_child}}</td>
 		<td>{{$itemkonv->item_qty_per}}</td>
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
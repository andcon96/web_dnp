@forelse($datas as $data)
 	<tr>
 		<td>{{$data->cust_code}}</td>
		 <td>{{$data->cust_desc}}</td>
		 <td>{{$data->customer_site}}</td>
		 <td>{{$data->customer_region}}</td>
		<td>{{$data->cust_top}}</td>
		<td>{{$data->custcredit_limit}}</td>
		<td>{{$data->cust_alamat}}</td> 
 		
 	</tr>
 @empty
 	<tr>
 		<td colspan="7" style="color:red"><center>No Data Available</center></td>
 	</tr>
 @endforelse  
 	<tr style="border-bottom:none !important;">
 		<td colspan="7">
 			{{ $datas->links() }}
 		</td>
 	</tr>
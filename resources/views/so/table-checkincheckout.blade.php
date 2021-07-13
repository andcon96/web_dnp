@forelse ($data as $show)
  <tr class="foottr">
    <td class="foot3" data-label="Salesman Name">{{$show->username}} -- {{ $show->name }}</td>
    <td class="foot2" data-label="Customer">{{ $show->cust_desc }}</td>
    <td class="foot2" data-label="Activity">{{ $show->activity_desc }}</td>
    <td class="foot2" data-label="Check In Date">{{$show->checkindate}}</td>
    <td class="foot2" data-label="Check Out Date">{{$show->checkoutdate}}</td>
  </tr>
@empty
	<tr>
		<td colspan="5" style="color:red;"> <center>No Data Available</center></td>
	</tr>
@endforelse 	
	<tr style="border: none !important;">
		<td  colspan="7">
			{{$data->links()}}
		</td>
	</tr>

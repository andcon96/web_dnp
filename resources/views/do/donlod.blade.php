<!DOCTYPE html>
<html>
<head>
  <title>Data SPB</title>
</head>
<body>
	<table>
		<tr>
			<td><b>SPB Number</b></td>
			<td><b>Customer</b></td>
			<td><b>Delivery Date</b></td>
			<td><b>Status</b></td>
		</tr>
		@foreach($data as $dd)
		<tr>
			<td>{{$dd->do_nbr}}</td>
			<td>{{$dd->do_cust}} - {{$dd->cust_desc}} </td>
			<td>{{$dd->do_date}}</td>
			@if($dd->do_status == 1)
		        <td>Waiting</td>
		    @elseif($dd->do_status == 2)
		        <td>Confirm</td>
		    @elseif($dd->do_status == 4)
		        <td>Ready to Ship</td>
		    @else
		        <td>Delete</td>
		    @endif
		</tr>
		@endforeach
	</table>
</body>
</html>
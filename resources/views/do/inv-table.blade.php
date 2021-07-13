@foreach($inv as $inv)
	<tr>
		<td>{{$inv->inv_site}}</td>
		<td>{{$inv->inv_location}}</td>
		<td>{{$inv->inv_item}} - {{$inv->inv_item_desc}}</td>
		<td>{{$inv->inv_lot}}</td>
		<td>{{$inv->inv_um}}</td>
		<td style="text-align:right">{{number_format($inv->inv_qty,2)}}</td>
		<td>{{$inv->inv_create}}</td>
	</tr>
@endforeach
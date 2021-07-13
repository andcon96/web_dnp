@forelse($data as $dt)
<tr>
    <td>{{$dt->custcodeship}} -- {{$dt->cust_desc}}</td>
    <td>{{$dt->shipto}} -- {{$dt->custname}}</td>
    <td>{{$dt->custaddr}}</td>

    
</tr>
@empty
<tr>
    <td colspan="2" style="color: red"><center>No Data Available</center></td>
</tr>
@endforelse
<tr style="border-bottom: none !important;">
    <td colspan="3">
        {{$data->links()}}
    </td>
</tr>

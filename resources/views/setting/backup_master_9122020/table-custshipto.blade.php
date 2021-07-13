@forelse($data as $dt)
<tr>
    <td>{{$dt->cust_code}} -- {{$dt->cust_desc}}</td>
    <td>{{$dt->shipto}} -- {{$dt->custname}}</td>

    
</tr>
@empty
<tr>
    <td colspan="2" style="color: red"><center>No Data Available</center></td>
</tr>
@endforelse
<tr style="border-bottom: none !important;">
    <td>
        {{$data->links()}}
    </td>
</tr>
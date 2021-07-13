@forelse ($data as $show)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $show->loc_site }}</td>
    <td>{{ $show->loc_loc }}</td>
</tr>
@empty
<tr>
    <td colspan="3" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr style="border-bottom:none !important;">
    <td colspan="3">
        {{$data->links()}}
    </td>
</tr>

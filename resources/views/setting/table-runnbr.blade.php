@forelse($data as $show)
<tr>
    <td>{{$show->site_code}}</td>
    <td>{{$show->site_desc}}</td>
    <td>{{$show->r_nbr_so}}</td>
    <td>{{$show->r_nbr_cons}}</td>
    <td>{{$show->r_nbr_retur}}</td>
    <td>{{$show->r_nbr_spb}}</td>
    <td>
        <a href="" class="edituser" data-toggle="modal" data-target="#editModal" 
        data-sitecode="{{$show->site_code}}" data-sitedesc="{{$show->site_desc}}"
        data-rnbrso="{{$show->r_nbr_so}}" data-rnbrspb="{{$show->r_nbr_spb}}"
        data-rnbrretur="{{$show->r_nbr_retur}}" data-rnbrcons="{{$show->r_nbr_cons}}">
            <i class="icon-table fa fa-edit fa-lg"></i></a>
    </td>
</tr>
@empty
<tr>
    <td colspan="12" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
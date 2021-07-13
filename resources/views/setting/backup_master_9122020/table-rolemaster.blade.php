@forelse ($data as $show)
<tr>
    <td>{{ $show->role_code }}</td>
    <td>{{ $show->role_desc }}</td>
    <td>{{ $show->salesman}} </td>
    <td>
        @if($show->role_desc != 'admin')
        <a href="" class="editrole" data-toggle="modal" data-target="#editModal" data-role_code="{{ $show->role_code}} " data-role_desc="{{ $show->role_desc}}"><i class="icon-table fa fa-edit fa-lg"></i></a>

        <a href="" class="deleterole" data-toggle="modal" data-target="#deleterole" data-rolecode="{{$show->role_code}} " data-roledesc="{{ $show->role_desc }}"><i class="icon-table fa fa-trash fa-lg"></i></a>

        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="4" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr style="border-bottom:none !important;">
 		<td>
 			{{$data->links()}}
 		</td>
 	</tr>

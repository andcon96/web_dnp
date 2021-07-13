@forelse ($data as $show)
<tr>
  <td>{{ $show->name }}</td>
  <td>{{ $show->username }}</td>
  <td>{{ $show->role_desc }}</td>
  <td>{{ $show->site_desc }}</td>
  <td>
    @if($show->username != 'admin' and $show->username != 'imi')
    <a href="" class="edituser" data-toggle="modal" data-target="#editModal" data-name="{{$show->name}}" data-username="{{$show->username}}" data-role="{{$show->role_code}}" data-site="{{$show->site_code}}"><i class="icon-table fa fa-edit fa-lg"></i></a>
    &nbsp;
    <a href="" class="deleteuser" data-toggle="modal" data-target="#deleteModal" data-name="{{$show->name}}" data-username="{{$show->username}}"><i class="icon-table fa fa-trash fa-lg"></i></a>
    @endif
  </td>
</tr>
@empty
<tr>
  <td colspan="5" style="color:red;">
    <center>No Data Available</center>
  </td>
</tr>
@endforelse
<tr style="border-bottom: none !important;">
  <td colspan="5">
    {{ $data->links() }}
  </td>
</tr>
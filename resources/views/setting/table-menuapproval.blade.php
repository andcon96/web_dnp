@forelse ($data as $show)
<tr>
  <td>{{ $show->site_code }}</td>
  <td>{{ $show->site_desc }}</td>
  @if($show->site_app == null)
    <td>Tidak Ada</td>
  @else
    <td>Ada</td>
  @endif
  <td>
    <a href="" class="editApprovalLevel" data-toggle="modal" data-target="#editModal" data-sitename="{{$show->site_desc}}" data-siteid="{{$show->site_code}}">
      <i class="icon-table fa fa-edit fa-lg"></i>
    </a>
  </td>
</tr>
@empty
<tr>
  <td colspan="4" style="color:red">
    <center>No Data Available</center>
  </td>
</tr>
@endforelse
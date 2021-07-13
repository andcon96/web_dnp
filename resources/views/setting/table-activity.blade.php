@forelse($data as $show)
<tr>
    <td>{{$show->activity_code}}</td>
    <td>{{$show->activity_desc}}</td>
    <td>
        <a href="" class="editactivity" data-toggle="modal" data-target="#editModal" data-activityid="{{$show->activity_code}}" data-desc="{{$show->activity_desc}}">
            <i class="icon-table fa fa-edit fa-lg"></i></a>

        <a href="" class="deleteactivity" data-toggle="modal" data-target="#deleteModal" data-activityid="{{$show->activity_code}}" data-desc="{{$show->activity_desc}}">
            <i class="icon-table fa fa-trash fa-lg"></i></a>
    </td>
</tr>
@empty
<tr>
    <td colspan="12" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr style="border-bottom:none !important;">
    <td colspan="3">
    {!! $data->render() !!}
    </td>
</tr>
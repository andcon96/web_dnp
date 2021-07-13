@forelse($data as $show)
<tr>
    <td>{{$show->cust_code_parent}} -- {{$show->cust_desc_parent}}</td>
    <td>{{$show->cust_code_child}} -- {{$show->cust_desc_child}}</td>
    <!--
    <td>
        <a href="" class="editrelation" data-toggle="modal" data-target="#editModal"
        data-custparent="{{$show->cust_code_parent}}" data-custchild="{{$show->cust_code_child}}"
        data-id="{{$show->id}}">
        <i class="icon-table fa fa-edit fa-lg"></i></a>

        <a href="" class="deleterelation" data-toggle="modal" data-target="#deleteModal"
        data-custparent="{{$show->cust_code_parent}}" data-custchild="{{$show->cust_code_child}}"
        data-id="{{$show->id}}" data-parentdesc="{{$show->cust_desc_parent}}" data-childdesc="{{$show->cust_desc_child}}">
        <i class="icon-table fa fa-trash fa-lg"></i></a>
    </td>
    -->
</tr>
@empty
    <tr>
        <td colspan="12" style="color: red;"><center>No Data Available</center></td>
    </tr>
@endforelse
    <tr style="border-bottom:none !important;">
        <td colspan="3">
            {{$data->links()}}
        </td>
    </tr>

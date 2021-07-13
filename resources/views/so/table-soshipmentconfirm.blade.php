@forelse ($soslist as $show)
<tr>
  <td>{{ $show->do_nbr }}</td>
  <td>{{ $show->do_cust }}</td>
  <td>{{ $show->cust_desc }}</td>
  <td>    
    <a href="" class="soshipmentgetinfo showact" data-toggle="modal" data-target="#confirmodal" data-do_nbr="{{$show->do_nbr}}" data-do_cust="{{$show->do_cust}}" data-do_custname="{{$show->cust_desc}}" data-tooltip="Confirm SO"><i class="icon-table fa fa-edit fa-lg"></i></a>
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
  <td>
    {!!$soslist->links()!!}
  </td>
</tr>
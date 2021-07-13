
@foreach($data as $show)
<tr>
  
   <td>
      @if(!str_contains( Session::get('menu_access'),'TS10'))
        <input type="checkbox" name="data[]" value="{{$show->so_nbr}}">
        @endif  
   </td>

   <td>{{ $show->so_nbr }}</td>
   <td>{{ $show->so_po }}</td>
   <td>{{ $show->so_cust }} - {{ $show->cust_desc }}</td>
   <td>{{ $show->so_shipto }}</td>
   
   
   <td>{{ $show->so_duedate }}</td>
</tr>
@endforeach
<tr style="border: none !important;">
  <td colspan="3">
  </td>
</tr>
<tr style="border: none !important;">
  <td colspan="5">
    {!! $data->links() !!}
  </td>
</tr>


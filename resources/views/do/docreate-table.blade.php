<div class="table-responsive col-12">
<table class="table table-bordered no-footer mini-table" id="dataTable" width="100%" cellspacing="0">
  <thead>
    <tr>
    <th>Customer</th>
    <th>Ship To</th>
    <th>Sales Order</th>
    <th>Line</th>
    <th>Item No</th>
    <th>Item Desc</th>
    <th>UM</th>
    <th>Qty SO</th>
    <th>Qty Open</th>
    <th>Qty Stock</th>
    <th>Qty Delivery</th>
    <th>Pick</th>
    </tr>
  </thead>
  <body>
      @foreach($temp as $show)
      <tr>
        <td>{{ $show->so_cust }} - {{ $show->so_custname }}<input type="hidden" name="docust[]" value="{{ $show->so_cust }}"></td>
        <td>{{ $show->so_shipto }}<input type="hidden" name="doship[]" value="{{ $show->so_shipto }}"></td>
        <td>{{ $show->so_nbr }}<input type="hidden" name="doso[]" value="{{ $show->so_nbr }}"></td>
        <td>{{ $show->so_line }}<input type="hidden" name="doline[]" value="{{ $show->so_line }}"></td>
        <td>{{ $show->so_itemcode }}<input type="hidden" name="doitem[]" value="{{ $show->so_itemcode }}"></td>
        <td>{{ $show->so_itemdesc }}</td>
        <td>{{ $show->so_um }}<input type="hidden" name="doum[]" value="{{$show->so_um}}"></td>
        <td style="text-align: right">{{ number_format($show->so_qtyso,2) }} <input type="hidden" id="doqtyso" name="doqtyso[]" class="doqtyso" value="{{$show->so_qtyso}}"></td>
        <td style="text-align: right">{{ number_format($show->so_qtyopen,2) }}<input type="hidden" id="doqtyopen" name="doqtyopen[]" class="doqtyopen" value="{{$show->so_qtyopen}}"></td>
	      <td style="text-align: right">{{ number_format($show->so_qtystock,2) }}<input type="hidden" id="doqtystock" name="doqtystock[]" class="doqtystock" value="{{$show->so_qtystock}}"></td>

  @if($show->so_qtyopen < $show->so_qtystock)
    @if($show->so_qtyopen == 0)
     <td><input type="number" id="doqty" style="width:100%; background-color:#ced8f2" name="doqty[]" class="doqty" value="{{ $show->so_qtyd }}" min = "0" max ="{{ $show->so_qtyopen }}" readonly></td>
    @else
      @if($show->so_status == 5)
        <td><input type="number" id="doqty" style="width:100%;background-color:#ced8f2" name="doqty[]" class="doqty" value="{{ $show->so_qtyd }}" min = "0" max ="{{ $show->so_qtyopen }}" focus></td>
      @else
        <td><input type="number" id="doqty" style="width:100%;background-color:white" name="doqty[]" class="doqty" value="{{ $show->so_qtyd }}" min = "0" max ="{{ $show->so_qtyopen }}" focus></td>
      @endif
    @endif  
  @else
    @if($show->so_qtystock == 0)
      <td><input type="number" id="doqty" style="width:100%; background-color:#ced8f2" name="doqty[]" class="doqty" value="{{ $show->so_qtyd }}" min = "0" max ="{{ $show->so_qtystock }}" readonly></td>
    @else
      @if($show->so_status == 5)
        <td><input type="number" id="doqty" style="width:100%;background-color:#ced8f2" name="doqty[]" class="doqty" value="{{ $show->so_qtyd }}" min = "0" max ="{{ $show->so_qtystock }}" focus></td>
      @else
        <td><input type="number" id="doqty" style="width:100%;background-color:white" name="doqty[]" class="doqty" value="{{ $show->so_qtyd }}" min = "0" max ="{{ $show->so_qtystock }}" focus></td>
      @endif
    @endif
  @endif
  
  @if($show->so_status == 5)
    <td style="text-align: center;"><input type="checkbox" id="cek" name="cek[]" class="cek">
    <input type="hidden" name="tick[]" id="tick" class="tick" value="0"></td> 
  @else
    @if($show->so_qtyopen == 0 || $show->so_qtystock == 0)
      <td style="text-align: center;"><input type="checkbox" id="cek" name="cek[]" class="cek">
      <input type="hidden" name="tick[]" id="tick" class="tick" value="0"></td>
    @else
      <td style="text-align: center;"><input type="checkbox" id="cek" name="cek[]" class="cek" checked>
      <input type="hidden" name="tick[]" id="tick" class="tick" value="1"></td>
    @endif
  @endif
      </tr>
      @endforeach
  </body>
</table>
</div>

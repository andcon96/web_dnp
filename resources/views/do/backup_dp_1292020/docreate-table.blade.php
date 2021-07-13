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
    <th>Qty Delivery</th>
    <th>Pick</th>
    </tr>
  </thead>
  <body>
      @foreach($temp as $show)
      <tr>
        <td>{{ $show->so_cust }} - {{ $show->so_custname }}<input type="hidden" name="docust[]" value="{{ $show->so_cust }}"></td>
        <td>{{ $show->so_shipto }} - {{ $show->so_shipdesc }}<input type="hidden" name="doship[]" value="{{ $show->so_shipto }}"></td>
        <td>{{ $show->so_nbr }}<input type="hidden" name="doso[]" value="{{ $show->so_nbr }}"></td>
        <td>{{ $show->so_line }}<input type="hidden" name="doline[]" value="{{ $show->so_line }}"></td>
        <td>{{ $show->so_itemcode }}<input type="hidden" name="doitem[]" value="{{ $show->so_itemcode }}"></td>
        <td>{{ $show->so_itemdesc }}</td>
        <td>{{ $show->so_um }}<input type="hidden" name="doum[]" value="{{ $show->so_um }}"></td>
        <td style="text-align: right">{{ $show->so_qtyso }}<input type="hidden" id="doqtyso" name="doqtyso[]" class="doqtyso" value="{{ $show->so_qtyso }}"></td>
        <td style="text-align: right">{{ $show->so_qtyopen }}<input type="hidden" id="doqtyopen" name="doqtyopen[]" class="doqtyopen" value="{{ $show->so_qtyopen }}"></td>
        <td><input type="number" id="doqty" name="doqty[]" class="doqty" value="{{ $show->so_qtyd }}" readonly style="background-color: #dfe6f7;"></td>
        <td style="text-align: center;"><input type="checkbox" id="cek" name="cek" class="cek"></td>
      </tr>
      @endforeach
  </body>
</table>
</div>

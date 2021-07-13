@forelse ($data as $show)
<tr>
    <td>{{ $show->site_code }}</td>
    <td>{{ $show->site_desc }}</td>
    @if($show->site_flag == 'Y')
    <td>Ada</td>
    @elseif($show->site_flag =='N')
    <td>Tidak Ada</td>
    @else
    <td></td>
    @endif
    
    @if($show->pusat_cabang == 0)
    <td>Cabang</td>
    @else
    <td>Pusat</td>
    @endif
    @if($data2 != null)
    <td>
        <a href="" class="editsite" data-toggle="modal" data-target="#editsitee" data-sitecode="{{ $show->site_code}} " data-sitedesc="{{ $show->site_desc}}" data-siteflag="{{ $show->site_flag}}" data-pusatcabang="{{ $show->pusat_cabang}}" data-pusatcabang2="{{$data2->site_code}}" data-pusatcabang3="notnull"><i class="icon-table fa fa-edit fa-lg"></i></a>
    </td>
    @else
    <td>
        <a href="" class="editsite" data-toggle="modal"  data-target="#editsitee" data-sitecode="{{ $show->site_code}} " data-sitedesc="{{ $show->site_desc}}" data-siteflag="{{ $show->site_flag}}" data-pusatcabang="{{ $show->pusat_cabang}}" data-pusatcabang2="{{$data2}}" data-pusatcabang3="allnull"><i class="icon-table fa fa-edit fa-lg"></i></a>
    </td>
    @endif
</tr>
@empty
<tr>
    <td colspan="5" style="color:red">
        <center>No Data Available</center>
    </td>
</tr>
@endforelse
<tr style="border-bottom:none !important;">
    <td colspan="5">
        {{$data->links()}}
    </td>
</tr>

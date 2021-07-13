<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Facades\Session;


class MRPPOExport implements FromQuery, WithHeadings, ShouldAutoSize
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    function __construct($c_type) {
            $this->c_type = $c_type;
    }

    public function query()
    {
        $c_type = $this->c_type;

        if($c_type == "All"){
            return DB::table('po_eod')
                        ->select('site','item_part','item_desc','item_type','qty_po')
                        ->where('site','=',Session::get('site'))
                        ->orderBy('item_type');
        }else{
            return DB::table('po_eod')
                        ->select('site','item_part','item_desc','item_type','qty_po')
                        ->where('item_type','=',$c_type)
                        ->where('site','=',Session::get('site'))
                        ->orderBy('item_type');
        }
    	
    }					

    public function headings(): array
    {
        return [
            'Site',
            'Item Code',
            'Item Desc',
            'Item Type',
            'Qty PO',
        ];
    }
}

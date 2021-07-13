<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class SalesExport implements FromQuery, WithHeadings, ShouldAutoSize
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function query()
    {
    	/*
        return Safety::query()->select('safety_part', 'safety_desc', 'safety_um', 
        								'safety_qty_oh', 'safety_safe_stock', 'safety_prod_line', 
                                        'safety_qty_ord', 'safety_qty_all')
    							->orderBy('safety_domain');
    	*/
    	return DB::table('contract_dets')
    					->join('items','items.itemcode','=','contract_dets.item_code')
    					->selectRaw('idgroup,item_code, count(*)')
    					->groupBy('items.idgroup')
    					->orderBy('items.idgroup');
    }					

    public function headings(): array
    {
        return [
            'ID Group',
            'Item Code Testing Length',
            'Jumlah',
        ];
    }
}

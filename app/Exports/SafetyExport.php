<?php

namespace App\Exports;

use App\Safety;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SafetyExport implements FromQuery, WithHeadings
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function query()
    {
        return Safety::query()->select('safety_part', 'safety_desc', 'safety_um', 
        								'safety_qty_oh', 'safety_safe_stock', 'safety_prod_line', 
                                        'safety_qty_ord', 'safety_qty_all')
    							->orderBy('safety_domain');
    }					

    public function headings(): array
    {
        return [
            'Item Part',
            'Item Desc',
            'UM',
            'Qty On Hands',
            'Safety Stock',
            'Safety Prod Line',
            'Safety Qty Ord',
            'Safety_qty_all',
        ];
    }
}

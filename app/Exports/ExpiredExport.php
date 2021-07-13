<?php

namespace App\Exports;

use App\Expired;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExpiredExport implements FromQuery, WithHeadings
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    /*public function collection()
    {
        return Expired::all();
    }*/

    public function query()
    {
        return Expired::query()->select('expired_part', 'expired_desc', 'expired_expdate', 
        								'expired_loc', 'expired_lot', 'expired_amt')
    							->orderBy('expired_domain');
    }					

    public function headings(): array
    {
        return [
            'Expired Part',
            'Expired Desc',
            'Expired Date',
            'Location',
            'Serial / Lot',
            'Ammount',
        ];
    }
}

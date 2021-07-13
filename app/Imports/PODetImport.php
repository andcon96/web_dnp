<?php

namespace App\Imports;

use App\XpodDet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class PODetImport implements ToModel, WithCustomCsvSettings
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new XpodDet([
            'xpod_domain' => $row[0],
            'xpod_nbr' => $row[1], 
            'xpod_line' => $row[2],
            'xpod_part' => $row[3],
            'xpod_desc' => $row[4],
            'xpod_um' => $row[5],
            'xpod_qty_ord' => $row[6],
            'xpod_price' => $row[7],
            'xpod_loc' => $row[8],
            'xpod_lot' => $row[9],
            'xpod_date' => $row[10],
            'xpod_status' => 'Created',
        ]);
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
}

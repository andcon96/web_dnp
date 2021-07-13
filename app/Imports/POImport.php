<?php

namespace App\Imports;

use App\XpoMstr;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class POImport implements ToModel, WithCustomCsvSettings
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new XpoMstr([
            'xpo_domain' => $row[0],
            'xpo_nbr' => $row[1], 
            'xpo_ord_date' => $row[2],
            'xpo_vend' => $row[3],
            'xpo_ship' => $row[4],
            'xpo_curr' => $row[5],
            'xpo_due_date' => $row[6]
        ]);
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
}

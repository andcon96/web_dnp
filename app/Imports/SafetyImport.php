<?php

namespace App\Imports;

use App\Safety;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;


class SafetyImport implements ToModel, WithCustomCsvSettings
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Safety([
            'safety_domain' => '10-USA',
            'safety_part' => $row[0], 
            'safety_desc' => $row[1],
            'safety_um' => $row[2],
            'safety_prod_line' => $row[3],
            'safety_safe_stock' => $row[4],
            'safety_qty_oh' => $row[5],
            'safety_qty_ord' => $row[6],
            'safety_qty_all' => $row[7],
            'safety_flag' => $row[8],
        ]);
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';'
        ];
    }
}

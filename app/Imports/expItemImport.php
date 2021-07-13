<?php

namespace App\Imports;

use App\Expired;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Session;

class expItemImport implements ToModel
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new Expired([
            'expired_domain' => '10-USA',
            'expired_part' => $row[0], 
            'expired_desc' => $row[1],
            'expired_expdate' => $row[2],
            'expired_loc' => $row[3],
            'expired_lot' => $row[4],
            'expired_amt' => $row[5],
        ]);
    }
}

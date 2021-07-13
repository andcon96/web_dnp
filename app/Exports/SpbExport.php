<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SpbExport implements FromView, ShouldAutoSize
{
      
	public function __construct($spb,$cust,$dfrom,$dto,$status,$site)
    {
        $this->spb 		= $spb;
        $this->cust 	= $cust;
        $this->dfrom 	= $dfrom;
        $this->dto 		= $dto;
        $this->status 	= $status;
        $this->site 	= $site;
    }

    public function view(): View
    {        
        $spb    = $this->spb;
        $cust   = $this->cust;
        $dfrom  = $this->dfrom;
        $dto    = $this->dto;
        $status = $this->status;
        $site   = $this->site;

        $query = DB::table('do_mstr')
            ->join('customers','do_mstr.do_cust','=','customers.cust_code')
            ->orderby('do_mstr.do_nbr','desc');

        $query->when(isset($spb), function($q) use ($spb) {
				$q->where('do_nbr', $spb);
		});

		$query->when(isset($cust), function($q) use ($cust) {
				$q->where('do_cust', $cust);
		});

		$query->when(isset($dfrom), function($q) use ($dfrom,$dto) {
				$q->whereBetween('do_date',[ $dfrom , $dto ]);
		});

		$query->when(isset($status), function($q) use ($status) {
				$q->where('do_status', $status);
		});

		$query->when(isset($site), function($q) use ($site) {
				$q->where('do_site', $site);
		});

		$data = $query->get();

        return view('do.donlod', ['data' => $data]);
    }
}

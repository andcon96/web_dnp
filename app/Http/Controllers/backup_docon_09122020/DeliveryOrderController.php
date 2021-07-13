<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PDF;
use Carbon\Carbon;

class DeliveryOrderController extends Controller
{
    public function index(Request $req){
	
        $data = DB::table('do_mstr')
                    ->join('customers','do_mstr.do_cust','=','customers.cust_code')
		    ->orderby('do_date','desc')
		    ->orderby('do_nbr','desc')
                    ->paginate(5);
	//dd($data);

        $customer = DB::table('customers')
                    ->get();

        $item = DB::table('so_dets')
                    ->join('items','so_dets.so_itemcode','=','items.itemcode')
                    ->where('so_status',1)
                    ->get();

        return view('do.dobrowse',['data' => $data, 'customer' => $customer, 'item' => $item]);
    }

    public function detaildo(Request $req){
        if($req->ajax()){
            
            $data = DB::table('do_mstr')
                        ->join('dod_det','do_mstr.do_nbr','=','dod_det.dod_nbr')
                        ->join('items','items.itemcode','=','dod_det.dod_part')
                        ->where('dod_det.dod_nbr','=',$req->nbr)
                        ->get();

            if($data){
                $output = '';
                foreach($data as $data){
                    $output .= '<tr>'.
                               '<td data-label="SO Nbr">'.$data->dod_so.'</td>'.
                               '<td data-label="Line">'.$data->dod_line.'</td>'.
                               '<td data-label="Barang">'.$data->itemcode.' - '.$data->itemdesc.'</td>'.
                               '<td data-label="Jumlah">'.$data->dod_qty.'</td>'.
                               '<td data-label="Satuan">'.$data->do_um.'</td>'.
                               '</tr>';
                }

                return response($output);
            }

        }
    }

    public function noso(Request $req){

        if($req->ajax()){
            $data = DB::table('so_mstrs')
                ->where('so_cust',$req->cust)
                ->get();

            if($data){
                $output = '';
                $output .= '<option value="" >Select</option>';
                foreach($data as $data){

                    $output .= '<option value="'.$data->so_nbr.'" >'.$data->so_nbr.'</option>';
                               
                }

                return response($output);
            }

        }
    }

    public function createdo(){
	if(Session::get('pusat_cabang')==1){
	$data = DB::table('so_mstrs')
	    ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
	    ->where('so_status', '!=', 10)
	    ->where('so_status',1)
	    ->orderBy('so_mstrs.created_at','Desc')
	    ->paginate(5);

		return view('do.docreate', ['data' => $data]);
	}
	else if(Session::get('pusat_cabang')==0){
	$data = DB::table('so_mstrs')
	    ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
	    ->where('so_user', '=', Session::get('username'))
	    ->where('so_site','=',Session::get('site'))
	    ->where('so_status',1)
	    ->orderBy('so_mstrs.created_at','Desc')
	    ->paginate(5);

		return view('do.docreate', ['data' => $data]);
	}        

         
         
    }

    public function testdo(Request $req) {
	if($req->ajax()){
        
	$code       = $req->code;
        $shipto     = $req->shipto;
        $datefrom   = $req->datefrom;
        $dateto     = $req->dateto;
        //dd($req->all());
        if($code == null and $shipto == null and $datefrom == null and $dateto == null){
		if(Session::get('pusat_cabang')==1){
		$data = DB::table('so_mstrs')
		    ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
		    ->where('so_status', '!=', 10)
		    ->where('so_status',1)
		    ->orderBy('so_mstrs.created_at','Desc')
		    ->paginate(5);
		}
		else if(Session::get('pusat_cabang')==0){
		$data = DB::table('so_mstrs')
		    ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
		    ->where('so_user', '=', Session::get('username'))
		    ->where('so_site','=',Session::get('site'))
		    ->where('so_status',1)
		    ->orderBy('so_mstrs.created_at','Desc')
		    ->paginate(5);
		}                
        } else {
            
            if($datefrom == null){
                $datefrom = '2000-01-01';
            }else{
                $new_format_date_from = str_replace('/', '-', $req->datefrom); 

                // ubah ke int
                $new_date_from = strtotime($new_format_date_from);

                // ubah ke format date
                $datefrom = date('Y-m-d',$new_date_from);
            }

            if($dateto == null){
                $dateto = '3000-01-01';
            }else{
                // ini buat benerin tanggal klo ga  hasil formatted_date Y-d-m bukan Y-m-d
                $new_format_date_to = str_replace('/', '-', $req->dateto); 

                // ubah ke int
                $new_date_to = strtotime($new_format_date_to);

                // ubah ke format date
                $dateto = date('Y-m-d',$new_date_to);
            }

            $query = 'so_duedate >= "'.$datefrom.'" AND so_duedate <= "'.$dateto.'" ';

            if($code != null){
                $query .= "AND so_cust = '".$code."'";
            }

            if($shipto != null){
                $query .= "AND so_shipto = '".$shipto."'";
            }

	    if(Session::get('pusat_cabang')==1){
		$data = DB::table('so_mstrs')
		->join('customers','so_mstrs.so_cust','=','customers.cust_code')
		->where('so_status', '!=', 10)
		->where('so_status',1)
		->whereRaw($query)
		->orderBy('so_mstrs.created_at','Desc')
		->paginate(5);
	    }
	    else if(Session::get('pusat_cabang')==0){
		$data = DB::table('so_mstrs')
		->join('customers','so_mstrs.so_cust','=','customers.cust_code')
		->where('so_user', '=', Session::get('username'))
		->where('so_site','=',Session::get('site'))
		->where('so_status',1)
		->whereRaw($query)
		->orderBy('so_mstrs.created_at','Desc')
		->paginate(5);
	    } 
        }

           return view('do.docreate-view', ['data' => $data]);
	}
    }

    public function searchitem(Request $req) {
        if($req->ajax()){
            $data = DB::table('so_dets')
                ->join('items','items.itemcode','=','so_dets.so_itemcode')
                ->where('so_nbr',$req->nbr)
                ->get();

            if($data){
                $output = '';
                $output .= '<option value="" >Select</option>';
                foreach($data as $data){

                    $output .= '<option value="'.$data->itemcode.'" >'.$data->itemcode.' - '.$data->itemdesc.'</option>';
                               
                }

                $output .= '||'.$data->so_qty;

                return response($output);
            }
        }
    }

    public function createdoTemp(Request $req) {

        if($req->data == ''){
            session()->flash('error','Please Select at least 1 data');
            return back();
        }else{
            $validate   = "";
            $val        = "";
            DB::table('so_temp')
                ->where('so_user',Session::get('username'))
                ->delete();
            foreach($req->data as $data){
                $show = DB::table('so_dets')
                        ->join('so_mstrs','so_mstrs.so_nbr','=','so_dets.so_nbr')
                        ->join('items','itemcode','=','so_itemcode')
                        ->join('customers','cust_code','=','so_cust')
                        ->where('so_dets.so_nbr',$data)
                        ->get();
		
                foreach ($show as $s) {
                DB::table('so_temp')
                ->insert([
                    "so_nbr"        => $s->so_nbr,
                    "so_cust"       => $s->so_cust,
                    "so_custname"   => $s->cust_desc,
                    "so_duedate"    => $s->so_duedate,
                    "so_line"       => $s->so_line,
                    "so_itemcode"   => $s->so_itemcode,
                    "so_itemdesc"   => $s->itemdesc,
                    "so_um"         => $s->so_um,
                    "so_qtyso"      => $s->so_qty,
                    "so_qtyopen"    => $s->so_qty_open,
                    "so_user"       => Session::get('username'),
                    "so_site"       => $s->so_site,
                    "so_shipto"     => $s->so_shipto,
                ]);

                    if ($validate != "" && $validate != $s->so_cust) {
                        return redirect()->back()->with('error', 'Customer');
                    }

                    if ($val != "" && $val != $s->so_shipto) {
                        return redirect()->back()->with('error', 'Ship To');
                    }

                    $validate   = $s->so_cust;
                    $val        = $s->so_shipto;
                }
            }

            return redirect()->route('dotemp');
        }
    }

    public function dotemp(Request $req) {
        
        if (is_null($req->donbr)) {
            /*---------- Nomor SPB Terakhir ----------*/
            $data = DB::table('do_mstr')
                    ->selectRaw('max(substr(do_nbr, 3)) as donbr')
                    ->first();

            $temp = DB::table('so_temp')
                    ->where('so_user',Session::get('username'))
                    ->get();

            $action = "doadd";

            $domstrs = DB::table('do_mstr') /* trik */
                       ->where('do_nbr',"no")
                       ->first();

        } else {
            DB::table('so_temp')
                ->where('so_user',Session::get('username'))
                ->delete();

            $action = "doedit";

            $data   = $req->donbr;
            
            $show = DB::table('dod_det')
                    ->join('do_mstr','do_mstr.do_nbr','=','dod_det.dod_nbr')
                    ->join('items','itemcode','=','dod_part')
                    ->join('customers','cust_code','=','do_cust')
                    ->join('so_dets', function($join){
                        $join->on('so_dets.so_nbr','=','dod_det.dod_so')
                            ->on('so_dets.so_line','=','dod_det.dod_line')
                            ->on('so_dets.so_itemcode','=','dod_det.dod_part');})
                    ->where('dod_det.dod_nbr',$req->donbr)
                    ->get();
            $min = 0;
            //dd($show);
            
            foreach ($show as $s) {
                $min = $s->so_qty_open + $s->dod_qty;

                DB::table('so_dets')
                ->where('so_nbr',$s->dod_so)
                ->where('so_itemcode',$s->dod_part)
                ->where('so_line',$s->dod_line)
                ->update([
                    'so_qty_open' => $min
                ]);

                DB::table('so_temp')
                ->insert([
                    "so_nbr"        => $s->dod_so,
                    "so_cust"       => $s->do_cust,
                    "so_custname"   => $s->cust_desc,
                    "so_duedate"    => $s->do_date,
                    "so_line"       => $s->dod_line,
                    "so_itemcode"   => $s->dod_part,
                    "so_itemdesc"   => $s->itemdesc,
                    "so_um"         => $s->do_um,
                    "so_qtyso"      => $s->so_qty,
                    "so_qtyopen"    => $min,
                    "so_qtyd"       => $s->dod_qty,
                    "so_user"       => Session::get('username'),
                    "so_site"       => $s->do_site,
                    "so_shipto"     => $s->do_shipto,
                ]);
            }

            $temp = DB::table('so_temp')
                    ->where('so_user',Session::get('username'))
                    ->get();

            $domstrs = DB::table('do_mstr')
                       ->where('do_nbr',$req->donbr)
                       ->first();
        }
        
        return view('do.docreate-temp',['temp' => $temp, 'lastdo' => $data, 'action' => $action, 'domstrs' => $domstrs]);
    }

    public function dosave(Request $req) {
        $flg = 0;
	if ($req->donbr == 'add') {
		$data = DB::table('do_mstr')
                    ->selectRaw('max(substr(do_nbr, 3)) as donbr')
                    ->first();
		$lastnbr = $data->donbr;
      		$donbr 	 = "DO".str_pad($lastnbr+1,6,"0",STR_PAD_LEFT);
	} else {
		$donbr = $req->donbr;
	}

	foreach ($req->doqty as $data) {
            $qty        = $req->doqty[$flg];
            $qtyopen    = $req->doqtyopen[$flg]; 
            
            if ($qty > $qtyopen) {
                session()->flash('error','Quantity shipments must not be more than the quantity open !!!');
                return back();   
            }
            
            $flg = $flg + 1;
        }
        $flg = 0;

        foreach ($req->doqty as $data) {
            $item       = $req->doitem[$flg];
            $nbr        = $req->doso[$flg];
            $qty        = $req->doqty[$flg];
            $cust       = $req->docust[$flg];
            $shipto     = $req->doship[$flg];
            $line       = $req->doline[$flg];
            $qtyso      = $req->doqtyso[$flg];
            $qtyopen    = $req->doqtyopen[$flg];
            $um         = $req->doum[$flg];

            DB::table('so_temp')
            ->where('so_nbr',$nbr)
            ->where('so_itemcode',$item)
            ->where('so_user',Session::get('username'))
            ->update([
                'so_qtyd' => $qty
            ]);
            
            // ubah format tanggal
            $new_format_date_to = str_replace('/', '-', $req->dodate); 
            $new_date_to = strtotime($new_format_date_to);
            $dodate = date('Y-m-d',$new_date_to);
            
            if ($qty > 0 || $qty <> "") {
                DB::table('do_mstr')
                    ->updateOrInsert(
                        ['do_nbr' => $donbr],
                        [
                            'do_cust'    => $cust,
                            'do_date'    => $dodate,
                            'do_shipto'  => $shipto,
                            'do_notes'   => $req->donote,
                            'do_status'  => 1,
                            'do_user'    => Session::get('username'),
                            'created_at' => date("Y-m-d")
                        ]
                    );

                DB::table('dod_det')
                    ->updateOrInsert(
                        [   "dod_nbr"       => $donbr,
                            "dod_line"      => $line,
                            "dod_part"      => $item,
                        ],
                        [
                            "dod_so"        => $nbr,
                            "dod_qty"       => $qty,
                            "dod_status"    => 1,
                            "created_at"    => date("Y-m-d"),
                            "do_um"         => $um
                        ]
                    );

		    $min = $qtyopen - $qty;

                    if ($min == 0) {
                         DB::table('so_dets')
                        ->where('so_nbr',$nbr)
                        ->where('so_line',$line)
                        ->where('so_itemcode',$item)
                        ->update(['so_qty_open' => $min, 'so_status' => '6']);
                    } else {
                         DB::table('so_dets')
                        ->where('so_nbr',$nbr)
                        ->where('so_line',$line)
                        ->where('so_itemcode',$item)
                        ->update(['so_qty_open' => $min, 'so_status' => '1']);
                    }
                    
                    $ss = DB::table('so_dets')
                        ->where('so_nbr',$nbr)
                        ->sum('so_qty_open');
                    
                   if ($ss == 0) {
                         DB::table('so_mstrs')
                        ->where('so_nbr',$nbr)
                        ->update(['so_status' => '6']);
                   } else {
			DB::table('so_mstrs')
                        ->where('so_nbr',$nbr)
                        ->update(['so_status' => '1']);
		   }
                   
            } 
            
            $flg = $flg + 1;
        }

        DB::table('so_temp')
            ->where('so_user',Session::get('username'))
            ->delete();

        if ($req->donbr == 'add') {
		return redirect()->route('ddosave');
	} else {
		return redirect()->route('ddodelete');
	}
    }

    public function searchqty(Request $req) {
        if($req->ajax()){
            $data = DB::table('so_dets')
                ->where('so_nbr',$req->nbr)
                ->where('so_itemcode',$req->item)
                ->get();

            if($data){

                $output = $data->so_qty;

                return response($output);
            }
        }
    }

    public function deletetemp(Request $req) {
        if($req->ajax()){

            DB::table('so_temp')
                    ->where('so_nbr','=',$req->nbr)
                    ->where('so_itemcode','=',$req->item)
                    ->where('so_line','=',$req->line)
                    ->where('so_user','=',Session::get('username'))
                    ->delete();

            $data = DB::table('so_temp')
                    ->where('so_user',Session::get('username'))
                    ->get();

             return view('do.docreate-table',['temp' => $data]);
        } 
    }

    public function doprint(Request $req) {

            $show = DB::table('dod_det')
                    ->join('do_mstr','do_mstr.do_nbr','=','dod_det.dod_nbr')
                    ->join('items','itemcode','=','dod_part')
                    ->join('customers','cust_code','=','do_cust')
                    ->where('dod_det.dod_nbr',$req->donbr)
                    ->get();

            $mastr = DB::table('do_mstr')
                    ->join('customers','cust_code','=','do_cust')
                    ->where('do_mstr.do_nbr',$req->donbr)
                    ->get();

            $pdf = PDF::loadview('do.doprint',['show' => $show, 'mastr' => $mastr]); 

            //$pdf->save(public_path('print.pdf'));

            return $pdf->stream();  
    }

    public function dopage(Request $req) {
        //dd($req->all());
     if ($req->ajax()) {
	    $spbnumber = $req->get('spbnumber');
            $customer = $req->get('customer');
            $status = $req->get('status');
	    $deliverydatefrom = $req->get('dlvdatefrom');
            $deliverydateto = $req->get('dlvdateto'); 
            $sort_by    = $req->get('sortby');
            $sort_type  = $req->get('sorttype');
            $page       = $req->get('page');

	       if($spbnumber == '' and $customer == '' and $status == '' and $deliverydatefrom == '' and $deliverydateto == ''){
		 
			$data = DB::table('do_mstr')
		            ->join('customers','do_mstr.do_cust','=','customers.cust_code')
			    ->orderby('do_date','desc')
		            ->orderby('do_nbr','desc')
		            ->paginate(5);
			return view('do.table-dobrowse',['data' => $data]);

	       }else{
			if($deliverydatefrom == null){
			     $deliverydatefrom = '2000-01-01';
		        }
		        if($deliverydateto == null){
		             $deliverydateto = '3000-01-01';
		        }

		        $kondisi = "do_date BETWEEN '".$deliverydatefrom."' and '".$deliverydateto."'";	

		       if ($spbnumber != '') {
		          $kondisi .= ' and do_nbr = "' . $spbnumber . '"';
		        // dd($kondisi);
		       }
		       if ($customer != '') {
		          $kondisi .= ' and do_cust = "' . $customer . '"';
		       }
		       if ($status != '') {
		          $kondisi .= ' and do_status = "' . $status . '"';
		       }

		      $data = DB::table('do_mstr')
		            ->join('customers','do_mstr.do_cust','=','customers.cust_code')
		            ->whereRaw($kondisi)
			    ->orderby('do_date','desc')
		    	    ->orderby('do_nbr','desc')
		            ->paginate(5);
		      
		      return view('do.table-dobrowse',['data' => $data]);

		       	

	       }

        }
    }

    public function dodelete(Request $req) {

        $data = DB::table('dod_det')
                ->where('dod_nbr',$req->donbr)
                ->get();

       
        foreach ($data as $data) {
            $stat = DB::table('so_mstrs')
                ->select('so_status')
                ->where('so_nbr',$data->dod_so)
                ->first();

            $qtyopen = DB::table('so_dets')
                ->select('so_qty_open')
                ->where('so_nbr',$data->dod_so)
                ->where('so_itemcode',$data->dod_part)
                ->where('so_line',$data->dod_line)
                ->first();

            $min = $qtyopen->so_qty_open + $data->dod_qty;    
            //dd($min);
            
            if ($stat->so_status == 6) {
                DB::table('so_mstrs')
                    ->where('so_nbr',$data->dod_so)
                    ->update([
                        'so_status'     => 1
                    ]);
            }

            DB::table('so_dets')
                ->where('so_nbr',$data->dod_so)
                ->where('so_itemcode',$data->dod_part)
                ->where('so_line',$data->dod_line)
                ->update([
                    'so_qty_open'    => $min,
                    'so_status'     => 1
                ]);

            DB::table('do_mstr')
                ->where('do_nbr',$data->dod_nbr)
                ->update([
                    'do_status'     => 3
                ]);

            DB::table('dod_det')
                ->where('dod_nbr',$data->dod_nbr)
                ->update([
                    'dod_status'     => 3
                ]);
        }

        return redirect()->route('ddodelete');
    }
}

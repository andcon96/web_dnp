<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Log;
use PDF;
use Carbon\Carbon;

use App\Exports\SpbExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class DeliveryOrderController extends Controller
{

    private function httpHeader($req) {
            return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

    public function index(Request $req){
    
        if(Session::get('pusat_cabang')==1){
            $data = DB::table('do_mstr')
                        ->join('customers','do_mstr.do_cust','=','customers.cust_code')
                        ->orderby('do_mstr.do_nbr','desc')
                        ->paginate(5);

            $site = DB::table('site_mstrs')
                ->get();
        }
        else if(Session::get('pusat_cabang')==0){
            $data = DB::table('do_mstr')
                        ->join('customers','do_mstr.do_cust','=','customers.cust_code')
                        ->where('do_site','=',Session::get('site'))
                        ->orderby('do_mstr.do_nbr','desc')
                        ->paginate(5);

            $site = DB::table('site_mstrs')
                ->where('site_code','=',Session::get('site'))
                ->get();
        }
        
        $customer = DB::table('customers')
                    ->get();

        $item = DB::table('so_dets')
                    ->join('items','so_dets.so_itemcode','=','items.itemcode')
                    ->where('so_status',1)
                    ->get();

        return view('do.dobrowse',['data' => $data, 'customer' => $customer, 'item' => $item, 'site' => $site]);
    }

    public function detaildo(Request $req){
        if($req->ajax()){
            
            $data = DB::table('do_mstr')
                        ->join('dod_det','do_mstr.do_nbr','=','dod_det.dod_nbr')
                        ->join('items','items.itemcode','=','dod_det.dod_part')
                        ->where('dod_det.dod_nbr','=',$req->nbr)
                        ->orderby('dod_so')
                        ->orderby('dod_line')
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
        ->orderBy('so_mstrs.so_nbr','Desc')
        ->paginate(5);

        return view('do.docreate', ['data' => $data]);
    }
    else if(Session::get('pusat_cabang')==0){
    $data = DB::table('so_mstrs')
        ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
        ->where('so_site','=',Session::get('site'))
        ->where('so_status',1)
        ->orderBy('so_mstrs.so_nbr','Desc')
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
        // /dd($req->all());
        if($req->data == ''){
            session()->flash('error','Please Select at least 1 data');
            return back();
        }else{
            $test = array();
            $batasatas  = 0;
            $jumlahsama = 0;
            $errormsg   = "";
            $itembefore = "";
            $validate   = "";
            $val        = "";
            DB::table('so_temp')
                ->where('so_user',Session::get('username'))
                ->delete();
            $testvar = '';
            foreach($req->data as $data){
                    $show = DB::table('so_dets')
                        ->join('so_mstrs','so_mstrs.so_nbr','=','so_dets.so_nbr')
                        ->join('items','itemcode','=','so_itemcode')
                        ->join('customers','cust_code','=','so_cust')
                        ->where('so_dets.so_nbr',$data)
                        ->get();
                 
            
                
                foreach($show as $show){
                    
                    $dow= DB::table('dod_det')
                    ->join('do_mstr','do_mstr.do_nbr','=','dod_det.dod_nbr')
                    ->select('dod_qty')
                    ->where('dod_part','=',$show->so_itemcode)
                    ->where(function($query){$query->where('do_status','=',1) ->orWhere('do_status','=',4);})
                    ->where('do_site','=',Session::get('site'))
                    ->sum('dod_qty');

                        // Validasi WSA
                        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
                        $qxReceiver     = '';
                        $qxSuppRes      = 'false';
                        $qxScopeTrx     = '';
                        $qdocName       = '';
                        $qdocVersion    = '';
                        $dsName         = '';               
                        $timeout        = 0;

                        // ** Edit here
                        $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                                        <Body>
                                        <sisaQty xmlns="urn:iris.co.id:wsatrain">
                                                <inpdomain>'.'DKH'.'</inpdomain>
                                                <inpart>'.$show->so_itemcode.'</inpart>
                                                <insite>'.Session::get('site').'</insite>
                                        </sisaQty>
                                        </Body>
                                </Envelope>';
                
                        $curlOptions = array(CURLOPT_URL => $qxUrl,
                                            CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                                            CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                                            CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                                            CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                                            CURLOPT_POST => true,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_SSL_VERIFYPEER => false,
                                            CURLOPT_SSL_VERIFYHOST => false);
                                    
                        $getInfo = '';
                        $httpCode = 0;
                        $curlErrno = 0;
                        $curlError = '';
                        $qdocResponse = '';

                        $curl = curl_init();
                        if ($curl) {
                            curl_setopt_array($curl, $curlOptions);
                            $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
                            $curlErrno    = curl_errno($curl);
                            $curlError    = curl_error($curl);
                            $first        = true;
                        
                            foreach (curl_getinfo($curl) as $key=>$value) {
                                if (gettype($value) != 'array') {
                                    if (! $first) $getInfo .= ", ";
                                    $getInfo = $getInfo . $key . '=>' . $value;
                                    $first = false;
                                    if ($key == 'http_code') $httpCode = $value;
                                }
                            }
                            curl_close($curl);
                            
                        }
                             
                        $xmlResp = simplexml_load_string($qdocResponse);       
                    
                        $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain');  
                        
                        // if ($xmlResp->xpath('//ns1:outOK')[0] == 'false') {
                        //     $testcode = $show->so_itemcode;
                        //     $testdesc = $show->itemdesc;
                        //     session()->flash('error','Item'.' '.$testcode.' '.$testdesc.' '.'tidak ada di Inventory ');
                        //     return back();
                        // }
                        // else{
                            
                            if($xmlResp->xpath('//ns1:t_qty') == null){
                                DB::table('so_temp')
                                ->insert([
                                    "so_nbr"        => $show->so_nbr,
                                    "so_cust"       => $show->so_cust,
                                    "so_custname"   => $show->cust_desc,
                                    "so_duedate"    => $show->so_duedate,
                                    "so_line"       => $show->so_line,
                                    "so_itemcode"   => $show->so_itemcode,
                                    "so_itemdesc"   => $show->itemdesc,
                                    "so_um"         => $show->so_um,
                                    "so_qtyso"      => $show->so_qty,
                                    "so_qtyopen"    => $show->so_qty_open,
                                    "so_user"       => Session::get('username'),
                                    "so_site"       => $show->so_site,
                                    "so_shipto"     => $show->so_shipto,
                                    "so_qtyd"       => 0,
                                    "so_qtystock"   => 0,
                                ]);
                            }
                            else{
                                // dd($show->so_itemcode);
                                $batasatas = $xmlResp->xpath('//ns1:t_qty')[0];
                                $testbatas = $batasatas - $dow;
                                $doutest = doubleval($show->so_qty_open);
                                // dd($batasatas,$dow,$testbatas);
                                // DD($show->so_qty_open, $show->so_qty,$show->so_itemcode,$batasatas,$dow,$testbatas,$doutest);
                                if($doutest < $testbatas){
                            
                                    DB::table('so_temp')
                                    ->insert([
                                        "so_nbr"        => $show->so_nbr,
                                        "so_cust"       => $show->so_cust,
                                        "so_custname"   => $show->cust_desc,
                                        "so_duedate"    => $show->so_duedate,
                                        "so_line"       => $show->so_line,
                                        "so_itemcode"   => $show->so_itemcode,
                                        "so_itemdesc"   => $show->itemdesc,
                                        "so_um"         => $show->so_um,
                                        "so_qtyso"      => $show->so_qty,
                                        "so_qtyopen"    => $show->so_qty_open,
                                        "so_user"       => Session::get('username'),
                                        "so_site"       => $show->so_site,
                                        "so_shipto"     => $show->so_shipto,
                                        "so_qtyd"       => $show->so_qty_open,
                                        "so_qtystock"   => $testbatas,
                                    ]);
                                }
                            
                                elseif($doutest == $testbatas ||$doutest > $testbatas && $testbatas >=0 ){
                                    DB::table('so_temp')
                                    ->insert([
                                        "so_nbr"        => $show->so_nbr,
                                        "so_cust"       => $show->so_cust,
                                        "so_custname"   => $show->cust_desc,
                                        "so_duedate"    => $show->so_duedate,
                                        "so_line"       => $show->so_line,
                                        "so_itemcode"   => $show->so_itemcode,
                                        "so_itemdesc"   => $show->itemdesc,
                                        "so_um"         => $show->so_um,
                                        "so_qtyso"      => $show->so_qty,
                                        "so_qtyopen"    => $show->so_qty_open,
                                        "so_user"       => Session::get('username'),
                                        "so_site"       => $show->so_site,
                                        "so_shipto"     => $show->so_shipto,
                                        "so_qtyd"       => $testbatas,
                                        "so_qtystock"   => $testbatas,
                                    ]);
                                }
                        
                                elseif($testbatas < 0){
                                // dd('123');
                                    DB::table('so_temp')
                                    ->insert([
                                        "so_nbr"        => $show->so_nbr,
                                        "so_cust"       => $show->so_cust,
                                        "so_custname"   => $show->cust_desc,
                                        "so_duedate"    => $show->so_duedate,
                                        "so_line"       => $show->so_line,
                                        "so_itemcode"   => $show->so_itemcode,
                                        "so_itemdesc"   => $show->itemdesc,
                                        "so_um"         => $show->so_um,
                                        "so_qtyso"      => $show->so_qty,
                                        "so_qtyopen"    => $show->so_qty_open,
                                        "so_user"       => Session::get('username'),
                                        "so_site"       => $show->so_site,
                                        "so_shipto"     => $show->so_shipto,
                                        "so_qtyd"       => 0,
                                        "so_qtystock"   => 0,
                                    ]);     

                                }
                        
                            }

                            if ($validate != "" && $validate != $show->so_cust) {
                                    session()->flash('error','can only be processed for same customer');
                                    return redirect()->back()->with('error', 'Can Only Be Processed For Same Customer !!!');
                                }

                            if ($val != "" && $val != $show->so_shipto) {
                                    return redirect()->back()->with('error', 'Ship To');
                                }

                            $validate   = $show->so_cust;
                            $val        = $show->so_shipto;


                        //} if xpath false


                }
            }        
        }
                return redirect()->route('dotemp');
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

            $action = $req->act;

            $data   = $req->donbr;

            $show = DB::table('so_dets')
                    ->join('items','itemcode','=','so_itemcode')
                    ->join('so_mstrs','so_mstrs.so_nbr','=','so_dets.so_nbr')
                    ->join('customers','cust_code','=','so_cust')
                    ->whereRaw('so_dets.so_nbr in (SELECT dod_so FROM dod_det WHERE dod_nbr = "'. $req->donbr.'")')
                    ->get();


                $min = 0;
            //dd($show);
            foreach ($show as $s) {

                $dodqty = 0;

                $datado = DB::table('dod_det')
                        ->where('dod_so',$s->so_nbr)
                        ->where('dod_line',$s->so_line)
                        ->where('dod_nbr',$req->donbr)
                        ->first();

                if(is_null($datado)) {
                    $dodqty = 0;
                    $status = 5;
                } else {
                    $dodqty = $datado->dod_qty;
                    $status = $datado->dod_status;
                }
                //dd($dodqty);
               $dow= DB::table('dod_det')
                    ->join('do_mstr','do_mstr.do_nbr','=','dod_det.dod_nbr')
                    ->select('dod_qty')
                    ->where('dod_part','=',$s->so_itemcode)
                    ->where(function($query){$query->where('do_status','=',1) ->orWhere('do_status','=',4);})
                    ->where('do_site','=',Session::get('site'))
                    ->sum('dod_qty');

                    $dow2= DB::table('dod_det')
                    ->join('do_mstr','do_mstr.do_nbr','=','dod_det.dod_nbr')
                    ->select('dod_qty')
                    ->where('dod_part','=',$s->so_itemcode)
                    ->where(function($query){$query->where('do_status','=',1) ->orWhere('do_status','=',4);})
                    ->where('dod_nbr','=',$req->donbr)
                    ->where('do_site','=',Session::get('site'))
                    ->sum('dod_qty');
                
                // Validasi WSA
                $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
                $qxReceiver     = '';
                $qxSuppRes      = 'false';
                $qxScopeTrx     = '';
                $qdocName       = '';
                $qdocVersion    = '';
                $dsName         = '';
                
                $timeout        = 0;

                // ** Edit here
                $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                                <Body>
                                <sisaQty xmlns="urn:iris.co.id:wsatrain">
                                        <inpdomain>'.'DKH'.'</inpdomain>
                                        <inpart>'.$s->so_itemcode.'</inpart>
                                        <insite>'.Session::get('site').'</insite>
                                </sisaQty>
                                </Body>
                        </Envelope>';
        
                $curlOptions = array(CURLOPT_URL => $qxUrl,
                                    CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                                    CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                                    CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                                    CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                                    CURLOPT_POST => true,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_SSL_VERIFYHOST => false);
                            
                $getInfo = '';
                $httpCode = 0;
                $curlErrno = 0;
                $curlError = '';
                $qdocResponse = '';

                $curl = curl_init();
                if ($curl) {
                    curl_setopt_array($curl, $curlOptions);
                    $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
                    $curlErrno    = curl_errno($curl);
                    $curlError    = curl_error($curl);
                    $first        = true;
                
                    foreach (curl_getinfo($curl) as $key=>$value) {
                        if (gettype($value) != 'array') {
                            if (! $first) $getInfo .= ", ";
                            $getInfo = $getInfo . $key . '=>' . $value;
                            $first = false;
                            if ($key == 'http_code') $httpCode = $value;
                        }
                    }
                    curl_close($curl);
                    
                }
                
                
                $xmlResp = simplexml_load_string($qdocResponse);    
                $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain'); 

                
                /*if ($xmlResp->xpath('//ns1:outOK')[0] == 'false') {
                    session()->flash('error','data item tidak ada di QAD');
                    return back();
                } else {*/
                        
                    $batasatas = $xmlResp->xpath('//ns1:t_qty');

                    $min = $s->so_qty_open + $dodqty;
                    
                    if($xmlResp->xpath('//ns1:t_qty') == null){
                        DB::table('so_temp')
                            ->insert([
                                "so_nbr"        => $s->so_nbr,
                                "so_cust"       => $s->so_cust,
                                "so_custname"   => $s->cust_desc,
                                // "so_duedate"    => $s->do_date,
                                "so_line"       => $s->so_line,
                                "so_itemcode"   => $s->so_itemcode,
                                "so_itemdesc"   => $s->itemdesc,
                                "so_um"         => $s->so_um,
                                "so_qtyso"      => $s->so_qty,
                                "so_qtyopen"    => $min,
                                "so_qtyd"       => $dodqty,
                                "so_user"       => Session::get('username'),
                                "so_site"       => $s->so_site,
                                "so_shipto"     => $s->so_shipto,
                                "so_status"     => $status,
                                "so_qtystock"   => 0, //qty dari wsa

                        ]);
                    } else {
                        $batasatas = $xmlResp->xpath('//ns1:t_qty')[0];
                        $testbatas = $batasatas - $dow + $dow2;
                        $doutest = doubleval($s->so_qty_open);
                        
                        if($doutest < $testbatas){
                    
                            DB::table('so_temp')
                            ->insert([
                                "so_nbr"        => $s->so_nbr,
                                "so_cust"       => $s->so_cust,
                                "so_custname"   => $s->cust_desc,
                                // "so_duedate"    => $s->do_date,
                                "so_line"       => $s->so_line,
                                "so_itemcode"   => $s->so_itemcode,
                                "so_itemdesc"   => $s->itemdesc,
                                "so_um"         => $s->so_um,
                                "so_qtyso"      => $s->so_qty,
                                "so_qtyopen"    => $min,
                                "so_qtyd"       => $dodqty,
                                "so_user"       => Session::get('username'),
                                "so_site"       => $s->so_site,
                                "so_shipto"     => $s->so_shipto,
                                "so_status"     => $status,
                                "so_qtystock"   => $testbatas, //qty dari wsa

                            ]);
                        }              
                        elseif($doutest == $testbatas ||$doutest > $testbatas && $testbatas >=0 ) {
                            DB::table('so_temp')
                                    ->insert([
                                        "so_nbr"        => $s->so_nbr,
                                        "so_cust"       => $s->so_cust,
                                        "so_custname"   => $s->cust_desc,
                                        // "so_duedate"    => $s->do_date,
                                        "so_line"       => $s->so_line,
                                        "so_itemcode"   => $s->so_itemcode,
                                        "so_itemdesc"   => $s->itemdesc,
                                        "so_um"         => $s->so_um,
                                        "so_qtyso"      => $s->so_qty,
                                        "so_qtyopen"    => $min,
                                        "so_qtyd"       => $dodqty,
                                        "so_user"       => Session::get('username'),
                                        "so_site"       => $s->so_site,
                                        "so_shipto"     => $s->so_shipto,
                                        "so_status"     => $status,
                                       "so_qtystock"   => $testbatas, //qty dari wsa

                                    ]);
                        } elseif($testbatas < 0) {
                        
                            DB::table('so_temp')
                                    ->insert([
                                        "so_nbr"        => $s->so_nbr,
                                        "so_cust"       => $s->so_cust,
                                        "so_custname"   => $s->cust_desc,
                                        // "so_duedate"    => $s->do_date,
                                        "so_line"       => $s->so_line,
                                        "so_itemcode"   => $s->so_itemcode,
                                        "so_itemdesc"   => $s->itemdesc,
                                        "so_um"         => $s->so_um,
                                        "so_qtyso"      => $s->so_qty,
                                        "so_qtyopen"    => $min,
                                        "so_qtyd"       => $dodqty,
                                        "so_user"       => Session::get('username'),
                                        "so_site"       => $s->so_site,
                                        "so_shipto"     => $s->so_shipto,
                                        "so_status"     => $status,
                                        "so_qtystock"   => 0, //qty dari wsa

                                    ]);     

                        }
                    }
                /*}*/

           
            }

            $temp = DB::table('so_temp')
            ->where('so_user',Session::get('username'))
            ->orderby('so_nbr','desc')
            ->orderby('so_line','asc')
            ->get();

            $domstrs = DB::table('do_mstr')
               ->where('do_nbr',$req->donbr)
               ->first();
        }

        return view('do.docreate-temp',['temp' => $temp, 'lastdo' => $data, 'action' => $action, 'domstrs' => $domstrs]);
    }

    public function dosave(Request $req) {
        
        $flg = 0;
        $flgcheck = 0;
        $arraylog = [];
        $flaglog = 0;
        $arraytempcheck = [];
        /* Cek Nomor Baru atau Edit SPB */
        if ($req->donbr == 'add') {
        
            $data = DB::table('site_mstrs')
                    ->where('site_code','=',Session::get('site'))
                    ->first();

            $donbr = $data->r_nbr_spb + 1;

            if (substr($donbr,2,1) <> substr(date("Ymd"),3,1)) {
                $donbr = substr(Session::get('site'),0,2).substr(date("Ymd"),3,1)."00001";
            }
            foreach($req->doso as $so){
                Log::channel('dolog')->info('nomor SPB : '.$donbr.' nomor SO : '.$req->doso[$flaglog].' item : '.$req->doitem[$flaglog]);
                
                $flaglog++;
            }           

            $stat  = 1;                      

            $cek = DB::table('do_mstr')
                ->where('do_nbr','=',$donbr)
                ->get();

            $jml = $cek->count();

         	if ($cek->count() <> 0) {
             	session()->flash('error','Number '.$donbr.' is Exisiting !!!');
             	return back(); 
            }

        } else {
            $donbr   = $req->donbr;
            $stat  = $req->act;
        }

        $nol = 0;

        /* Cek Qty SPB */
        $jmlTick = 0;
        foreach ($req->doqty as $data) {
            $qty        = $req->doqty[$flg];
            $qtyopen    = $req->doqtyopen[$flg];
            $tick       = $req->tick[$flg]; 

            if ($qty > $qtyopen) {
                session()->flash('error','Quantity shipments must not be more than the quantity open !!!');
                return back();   
            }

            if ($qty > 0){
                $nol = 1;
            }
            else if ($qty == 0 && $tick == 1){
                $nol = 0;
                break;
            }

            $jmlTick = $jmlTick + $tick;
            
            $flg = $flg + 1;
        }
    
        if ($nol == 0) {
            session()->flash('error','Quantity can not zero !!!');
                    return back(); 
        }

        if ($jmlTick == 0) {
            session()->flash('error','Please Pick Item !!!');
            return back(); 
        }
        
        /* cek stok */
        $flg = 0;
        Schema::create('tempcheck', function($table)
        {
            $table->string('do_nbr');
            $table->string('so_nbr');
            $table->string('item');
            $table->integer('itemqty');
            $table->string('tick');
            $table->temporary();
        });
        
        foreach($req->doqty as $data=>$q){
            $arraycheck = array
            (
                'do_nbr'=>$req->donbr,
                'so_nbr'=>$req->doso[$data],
                'item' =>$req->doitem[$data],
                'itemqty' =>$req->doqty[$data],
                'tick'  =>$req->tick[$data],
            );

            DB::table('tempcheck')->insert($arraycheck);

        }
        $tickitem = DB::table('tempcheck')
                    ->where('tick','=','1')
                    ->get();

                
        foreach($tickitem as $doitem){
            $qtycheck = DB::table('tempcheck')
                         ->where('item','=',$doitem->item)
                         ->where('tick','=','1')
                         ->sum('itemqty');
                         



            // Validasi WSA --> SPB


                // Validasi WSA
                $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
                $qxReceiver     = '';
                $qxSuppRes      = 'false';
                $qxScopeTrx     = '';
                $qdocName       = '';
                $qdocVersion    = '';
                $dsName         = '';
                
                $timeout        = 0;

                // ** Edit here
                $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                                <Body>
                                  <sisaQty xmlns="urn:iris.co.id:wsatrain">
                                        <inpdomain>'.'DKH'.'</inpdomain>
                                        <inpart>'.$doitem->item.'</inpart>
                                        <insite>'.Session::get('site').'</insite>
                                  </sisaQty>
                                </Body>
                        </Envelope>';
          
                $curlOptions = array(CURLOPT_URL => $qxUrl,
                                     CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                                     CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                                     CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                                     CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                                     CURLOPT_POST => true,
                                     CURLOPT_RETURNTRANSFER => true,
                                     CURLOPT_SSL_VERIFYPEER => false,
                                     CURLOPT_SSL_VERIFYHOST => false);
                             
                $getInfo = '';
                $httpCode = 0;
                $curlErrno = 0;
                $curlError = '';
                $qdocResponse = '';

                $curl = curl_init();
                if ($curl) {
                    curl_setopt_array($curl, $curlOptions);
                    $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
                    $curlErrno    = curl_errno($curl);
                    $curlError    = curl_error($curl);
                    $first        = true;
                
                    foreach (curl_getinfo($curl) as $key=>$value) {
                        if (gettype($value) != 'array') {
                            if (! $first) $getInfo .= ", ";
                            $getInfo = $getInfo . $key . '=>' . $value;
                            $first = false;
                            if ($key == 'http_code') $httpCode = $value;
                        }
                    }
                    curl_close($curl);
                    
                }
                
                
                $xmlResp = simplexml_load_string($qdocResponse);       
            
                $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain');  
                $batasatas = $xmlResp->xpath('//ns1:t_qty')[0];
                
                $checkstock = floatval($batasatas) - intval($qtycheck);
            
                if($checkstock >=0){
                  array_push($arraytempcheck,"yes");
                }
                else if ($checkstock <0){
                    array_push($arraytempcheck,"no");
                
                }

        }
        if(in_array("no",$arraytempcheck)){
            Schema::dropIfExists('tempcheck');
            session()->flash('error', 'Jumlah Barang di QAD tidak cukup');
                return back();
        }
        else{
	    /* Kembalikan Qty Open SO ke kondisi awal */
            Schema::dropIfExists('tempcheck');
                foreach ($req->doqty as $data) {
                    
                    $nbr        = $req->doso[$flg];
                    $line       = $req->doline[$flg];
                    $item       = $req->doitem[$flg];
                    $qtyopen    = $req->doqtyopen[$flg];
                 
                    DB::table('so_dets')
                        ->where('so_nbr',$nbr)
                        ->where('so_line',$line)
                        ->where('so_itemcode',$item)
                        ->update(['so_qty_open' => $qtyopen]);

                    $flg = $flg + 1;
                }

                /* Hapus data sebelumnya*/
                /*DB::table('dod_det')
                        ->where('dod_nbr',$donbr)
                        ->delete();*/

                /* Simpan Data */
                $flg = 0;
                $flgcheck = 0;
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
                    $tick       = $req->tick[$flg];
                    $status     = $stat;

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

                    /* Hapus yang ngga di pilih */
                    if ($tick == 0) {

                    	if ($qtyopen == 0) {
                    		DB::table('so_dets')
        	                ->where('so_nbr',$nbr)
        	                ->where('so_line',$line)
        	                ->where('so_itemcode',$item)
        	                ->update(['so_qty_open' => $qtyopen, 'so_status' => '6']);
                    	} else {
                    		DB::table('so_dets')
        	                ->where('so_nbr',$nbr)
        	                ->where('so_line',$line)
        	                ->where('so_itemcode',$item)
        	                ->update(['so_qty_open' => $qtyopen, 'so_status' => '1']);
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
                    	
                    	DB::table('dod_det')
                        ->where('dod_nbr','=',$donbr)
                        ->where('dod_line','=',$line)
                        ->where('dod_part','=',$item)
                        ->where('dod_so','=',$nbr)
                        ->delete();
                    }

                    /* Yang disimpan hanya yang dicentang dan qty lebih dari 0 */
                    if ($tick == 1 && $qty > 0){
                        
                        $sekarang = Carbon::now()->toDateTimeString();
                        DB::table('do_mstr')
                            ->updateOrInsert(
                                ['do_nbr' => $donbr],
                                [
                                    'do_cust'    => $cust,
                                    'do_date'    => $dodate,
                                    'do_shipto'  => $shipto,
                                    'do_notes'   => $req->donote,
                                    'do_status'  => $status,
                                    'do_site'    => session::get('site'),
                                    'do_user'    => Session::get('username'),                    
                                ]
                            );
                        
                        $test = DB::table('do_mstr')        
                                ->where('do_nbr','=',$donbr)
                                ->orderBy('created_at','asc')
                                ->first();
                                    
                        if($test->created_at==null){
                            db::table('do_mstr')
                            ->where('do_nbr','=', $donbr)
                            ->update(['created_at'=>$sekarang]);        
                        }

                        DB::table('dod_det')
                            ->updateOrInsert(
                                [   "dod_nbr"       => $donbr,
                                    "dod_line"      => $line,
                                    "dod_part"      => $item,
                                    "dod_so"        => $nbr,
                                ],
                                [
                                    "dod_qty"       => $qty,
                                    "dod_status"    => $status,
                                    "do_um"         => $um
                                ]
                            );

                        $test2 = DB::table('dod_det')
                                ->where('dod_nbr','=',$donbr)
                                ->orderBy('created_at','asc')
                                ->first();

                        if($test2->created_at==null){
                            db::table('dod_det')
                            ->where('dod_nbr','=', $donbr)
                            ->update(['created_at'=>$sekarang]);   
                        }

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
                    }    /* if tick */

                    $flg = $flg + 1;

                } /* foreach $data*/

                DB::table('so_temp')
                    ->where('so_user',Session::get('username'))
                    ->delete();

                if ($req->donbr == 'add') {
                    $cek = DB::table('do_mstr')
                        ->where('do_nbr','=',$donbr)
                        ->get();

                    $jml = $cek->count();

                     if ($cek->count() == 0) {
                        session()->flash('updated','Create SPB Failed !!!');           
                    	return redirect()->route('ddosave');
                    } else {
                    	DB::table('site_mstrs')
                            ->where('site_code','=',session::get('site'))
                            ->update(['r_nbr_spb' => $donbr]); 

                    	session()->flash('updated','Save Success. SPB Number : '.$donbr);           
                    	return redirect()->route('ddosave',['donbr' => $donbr]);
                    }

                } else {
                    return redirect()->route('ddodelete');
                }
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

        $item = DB::table('dod_det')
                ->where('dod_nbr',$req->donbr)
                ->get();

        foreach ($item as $item) {        

            $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
            $qxReceiver     = '';
            $qxSuppRes      = 'false';
            $qxScopeTrx     = '';
            $qdocName       = '';
            $qdocVersion    = '';
            $dsName         = '';      
            $timeout        = 0;

            // ** Edit here
            $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                            <Body>
                                <itemSearch xmlns="urn:iris.co.id:wsatrain">
                                    <inpdomain>DKH</inpdomain>
                                    <inpart>'.$item->dod_part.'</inpart>
                                </itemSearch>
                            </Body>
                        </Envelope>';

            $curlOptions = array(CURLOPT_URL => $qxUrl,
                         CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                         CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                         CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                         CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                         CURLOPT_POST => true,
                         CURLOPT_RETURNTRANSFER => true,
                         CURLOPT_SSL_VERIFYPEER => false,
                         CURLOPT_SSL_VERIFYHOST => false);
                         
            $getInfo = '';
            $httpCode = 0;
            $curlErrno = 0;
            $curlError = '';
            $qdocResponse = '';

            $curl = curl_init();
            if ($curl) {
                curl_setopt_array($curl, $curlOptions);
                $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
                $curlErrno    = curl_errno($curl);
                $curlError    = curl_error($curl);
                $first        = true;
            
                foreach (curl_getinfo($curl) as $key=>$value) {
                    if (gettype($value) != 'array') {
                        if (! $first) $getInfo .= ", ";
                        $getInfo = $getInfo . $key . '=>' . $value;
                        $first = false;
                        if ($key == 'http_code') $httpCode = $value;
                    }
                }
                curl_close($curl);  
            }
              
            $xmlResp = simplexml_load_string($qdocResponse);       
       
            $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain'); 
//dd($qdocResponse);
            DB::table('item_parent')
                ->where('ps_par',$item->dod_part)
                ->delete();

            foreach($xmlResp->xpath('//ns1:tempRow') as $data) { 

               DB::table('item_parent')
                        ->Insert(
                            [   "ps_par"       => $data->t_part,
                                "ps_comp"      => $data->t_comp,
                                "ps_qty_per"   => $data->t_qty_per,
                            ]
                        );
            }
        }

        $mastr = DB::table('do_mstr')
                ->join('customers','cust_code','=','do_cust')
                ->where('do_mstr.do_nbr',$req->donbr)
                ->first();


        /* Mencari Nama dan Alamat Customer */
        $arraytemp = [];
        $arraykirim = [];
        $cust_nm     = "";
        $cust_almt   = "";

        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
        $qxReceiver     = '';
        $qxSuppRes      = 'false';
        $qxScopeTrx     = '';
        $qdocName       = '';
        $qdocVersion    = '';
        $dsName         = '';      
        $timeout        = 0;

        // ** Edit here
        $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                            <Body>
                                <addressSearch xmlns="urn:iris.co.id:wsatrain">
                                    <incust>'.$mastr->do_cust.'</incust>
                                </addressSearch>
                            </Body>
                        </Envelope>';

        $curlOptions = array(CURLOPT_URL => $qxUrl,
                     CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                     CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                     CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                     CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                     CURLOPT_POST => true,
                     CURLOPT_RETURNTRANSFER => true,
                     CURLOPT_SSL_VERIFYPEER => false,
                     CURLOPT_SSL_VERIFYHOST => false);
                     
        $getInfo = '';
        $httpCode = 0;
        $curlErrno = 0;
        $curlError = '';
        $qdocResponse = '';

        $curl = curl_init();
        if ($curl) {
            curl_setopt_array($curl, $curlOptions);
            $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;
        
            foreach (curl_getinfo($curl) as $key=>$value) {
                if (gettype($value) != 'array') {
                    if (! $first) $getInfo .= ", ";
                    $getInfo = $getInfo . $key . '=>' . $value;
                    $first = false;
                    if ($key == 'http_code') $httpCode = $value;
                }
            }
            curl_close($curl);  
        }
          
        $xmlResp = simplexml_load_string($qdocResponse);       
   
        $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain'); 

        if ($xmlResp->xpath('//ns1:outOK')[0] == 'false') {
            $cust_nm    = "";
            $cust_almt  = "";

        } else {
            foreach($xmlResp->xpath('//ns1:tempRow') as $data){    
                $cust_nm    = $data->t_name;
                $cust_almt  = $data->t_address;
            }
        }
       
        $par = DB::table('dod_det')
                ->join('item_parent','dod_det.dod_part','=','item_parent.ps_par')
                ->where('dod_nbr',$req->donbr)
                ->whereRaw('ps_comp <> ""')
                ->get();


        $show = DB::table('dod_det')
                ->join('do_mstr','do_mstr.do_nbr','=','dod_det.dod_nbr')
                ->join('items','itemcode','=','dod_part')
                ->join('customers','cust_code','=','do_cust')
                ->join('so_dets', function($join){
                    $join->on('so_dets.so_nbr','=','dod_det.dod_so')
                         ->on('so_dets.so_line','=','dod_det.dod_line')
                         ->on('so_dets.so_itemcode','=','dod_det.dod_part');})
                ->selectRaw('*,IF(ceil((LENGTH(itemdesc )/ 30))=1, 0, 1) as lnDesc')
                ->where('dod_det.dod_nbr',$req->donbr)
                ->orderby('itemcode','asc')
                ->orderby('dod_line','asc')
                ->get();
        //dd($show);

        $ship = DB::table('do_mstr')
                ->join('cust_shipto','do_mstr.do_shipto','=','cust_shipto.shipto')
                ->where('do_mstr.do_nbr',$req->donbr)
                ->get();

        $sopo = DB::table('dod_det')
                ->join('so_mstrs','so_mstrs.so_nbr','=','dod_det.dod_so')
                ->distinct('dod_so','so_po')
                ->select('dod_so','so_po')
                ->where('dod_det.dod_nbr',$req->donbr)
                ->whereNotNull('so_po')
                ->where('so_po', '<>', '')
                ->get();

        $flg = 1;
        $jml = $sopo->count();
        if ($jml == 0) {
            $so = '-';
        } else {
            foreach($sopo as $s){
                if ($flg == 1){
                    $so = $s->so_po;
                } else {
                    $so = $so.', '.$s->so_po;
                }
            } 
        }

        $status = $mastr->do_status;

        if ($req->prt == 1) {
            $pdf = PDF::loadview('do.docetak',['show' => $show, 'mastr' => $mastr, 'so' => $so, 'ship' => $ship, 'par' => $par, 'cust_nm' => $cust_nm, 'cust_almt' => $cust_almt])->setPaper([0, 0, 450, 700],'landscape'); 
        } else {
            if ($status == 1) {
                $pdf = PDF::loadview('do.dopick',['show' => $show, 'mastr' => $mastr, 'so' => $so, 'ship' => $ship, 'par' => $par,])->setPaper([0, 0, 450, 700],'landscape');
            } else {
                $pdf = PDF::loadview('do.doprint',['show' => $show, 'mastr' => $mastr, 'so' => $so, 'ship' => $ship, 'par' => $par,])->setPaper([0, 0, 450, 700],'landscape');
            } 
        }

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
        $site       = $req->get('site');

        $sort_by    = $req->get('sortby');
        $sort_type  = $req->get('sorttype');
        $page       = $req->get('page');
        

        if($spbnumber == '' and $customer == '' and $status == '' and $deliverydatefrom == '' and $deliverydateto == '' and $site == ''){
         	if(Session::get('pusat_cabang')==1){
            $data = DB::table('do_mstr')
                    ->join('customers','do_mstr.do_cust','=','customers.cust_code')
                    ->orderby('do_mstr.created_at','desc')  
                    ->paginate(5);
            }else{
            $data = DB::table('do_mstr')
                    ->join('customers','do_mstr.do_cust','=','customers.cust_code')
                    ->where('do_site','=',Session::get('site'))
                    ->orderby('do_mstr.created_at','desc')  
                    ->paginate(5);	
            }
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
            }
            if ($customer != '') {
              $kondisi .= ' and do_cust = "' . $customer . '"';
            }
            if ($status != '') {
              $kondisi .= ' and do_status = "' . $status . '"';
            }
            if ($site != '') {
              $kondisi .= ' and do_site = "' . $site . '"';
            }

            $data = DB::table('do_mstr')
                ->join('customers','do_mstr.do_cust','=','customers.cust_code')
                ->whereRaw($kondisi)
                ->orderby('do_mstr.created_at','desc')    
                ->paginate(5);
          
            return view('do.table-dobrowse',['data' => $data]);
        }
      }
    }

    public function docreatepage(Request $req) {
    
     if ($req->ajax()) {
            $pocode     = $req->get('pocode');
            $code       = $req->get('code');
            $shipto     = $req->get('shipto');
            $datefrom   = $req->get('datefrom');
            $dateto     = $req->get('dateto'); 
            $sort_by    = $req->get('sortby');
            $sort_type  = $req->get('sorttype');
            $page       = $req->get('page');
            $socode     = $req->get('socode');

        if($pocode == '' and $socode == '' and $code == '' and $shipto == '' and $datefrom == '' and $dateto == ''){
         
            if(Session::get('pusat_cabang')==1){
                $data = DB::table('so_mstrs')
                 ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                 ->where('so_status', '!=', 10)
                 ->where('so_status',1)
                 ->orderBy('so_mstrs.so_nbr','Desc')
                 ->paginate(5);

                return view('do.docreate-view', ['data' => $data]);
            } else if(Session::get('pusat_cabang')==0){
                $data = DB::table('so_mstrs')
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->where('so_site','=',Session::get('site'))
                ->where('so_status',1)
                ->orderBy('so_mstrs.so_nbr','Desc')
                ->paginate(5);

                return view('do.docreate-view', ['data' => $data]);
            }      
        } else{
            if($datefrom == null){
                $datefrom = '2000-01-01';
            }
            
            if($dateto == null){
                $dateto = '3000-01-01';
            }

            // ubah format tanggal
            $new_format_date_to = str_replace('/', '-', $datefrom); 
            $new_date_to = strtotime($new_format_date_to);
            $datefrom = date('Y-m-d',$new_date_to); 

            // ubah format tanggal
            $new_format_date_to_1 = str_replace('/', '-', $dateto); 
            $new_date_to_1 = strtotime($new_format_date_to_1);
            $dateto = date('Y-m-d',$new_date_to_1);           

            $kondisi = "so_duedate BETWEEN '".$datefrom."' and '".$dateto."'";  
            
            if($socode != ''){
                $kondisi .= ' and so_nbr = "' . $socode . '"';
            }
            
            if ($code != '') {
                $kondisi .= ' and so_cust = "' . $code . '"';
            }
            if ($shipto != '') {
                $kondisi .= ' and so_shipto = "' . $shipto . '"';
            }
            if($pocode != ''){
                $kondisi .= ' and so_po = "'. $pocode . '"'; 
            }

//dd($kondisi);            
            if(Session::get('pusat_cabang')==1){
                $data = DB::table('so_mstrs')
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->where('so_status', '!=', 10)
                ->where('so_status',1)
                ->whereRaw($kondisi)
                ->orderBy('so_mstrs.so_nbr','Desc')
                ->paginate(5);

                return view('do.docreate-view', ['data' => $data]);
            } else if(Session::get('pusat_cabang')==0){
                $data = DB::table('so_mstrs')
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->where('so_site','=',Session::get('site'))
                ->where('so_status',1)
                ->whereRaw($kondisi)
                ->orderBy('so_mstrs.so_nbr','Desc')
                ->paginate(5);

                return view('do.docreate-view', ['data' => $data]);
            }        
        }
      }
    }

    public function dodelete(Request $req) {
    //dd($req->all());
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

    public function inv(Request $req) {
        $item = DB::table('items')
                ->get();
       
        if(Session::get('pusat_cabang')==1){
            $site = DB::table('site_mstrs')
                    ->get();
        } else if(Session::get('pusat_cabang')==0) {
            $site = DB::table('site_mstrs')
                    ->where('site_code','=',Session::get('site'))
                    ->get();
        }

        $loc = DB::table('items')
               ->select('item_location')
               ->distinct('item_location')
               ->get();
//dd($loc);
        if(is_null($req->barang) && is_null($req->site) && is_null($req->loc)) {
            $barang = '1';
        } else {
            $barang = $req->barang;
        }

        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
        $qxReceiver     = '';
        $qxSuppRes      = 'false';
        $qxScopeTrx     = '';
        $qdocName       = '';
        $qdocVersion    = '';
        $dsName         = '';      
        $timeout        = 0;

        // ** Edit here
        $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                        <Body>
                        <stock xmlns="urn:iris.co.id:wsatrain">
                                <inpdomain>DKH</inpdomain>
                                <inpart>'.$barang.'</inpart>
                                <insite>'.$req->site.'</insite>
                                <inloc>'.$req->loc.'</inloc>
                        </stock>
                    </Body>
            </Envelope>';
//dd($qdocRequest);
        $curlOptions = array(CURLOPT_URL => $qxUrl,
                             CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
                             CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
                             CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
                             CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
                             CURLOPT_POST => true,
                             CURLOPT_RETURNTRANSFER => true,
                             CURLOPT_SSL_VERIFYPEER => false,
                             CURLOPT_SSL_VERIFYHOST => false);
                     
        $getInfo = '';
        $httpCode = 0;
        $curlErrno = 0;
        $curlError = '';
        $qdocResponse = '';

        

        $curl = curl_init();
        if ($curl) {
            curl_setopt_array($curl, $curlOptions);
            $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
            $curlErrno    = curl_errno($curl);
            $curlError    = curl_error($curl);
            $first        = true;
        
            foreach (curl_getinfo($curl) as $key=>$value) {
                if (gettype($value) != 'array') {
                    if (! $first) $getInfo .= ", ";
                    $getInfo = $getInfo . $key . '=>' . $value;
                    $first = false;
                    if ($key == 'http_code') $httpCode = $value;
                }
            }
            curl_close($curl);  
        }
          
        $xmlResp = simplexml_load_string($qdocResponse);       
   
        $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain');  
//dd($qdocResponse);
        DB::table('inv_temp')
            ->delete();

        foreach($xmlResp->xpath('//ns1:tempRow') as $data) { 
        
             // ubah format tanggal
            $new_format_date_to = str_replace('/', '-', $data->t_date); 
            $new_date_to = strtotime($new_format_date_to);
            $invdate = date('Y-m-d',$new_date_to);

           DB::table('inv_temp')
                    ->Insert(
                        [   "inv_site"       => $data->t_site,
                            "inv_location"   => $data->t_loc,
                            "inv_item"       => $data->t_part,
                            "inv_item_desc"  => $data->t_desc,
                            "inv_lot"        => $data->t_lot,
                            "inv_um"         => $data->t_um,
                            "inv_qty"        => $data->t_qty,
                            "inv_create"     => $invdate,
                        ]
                    );
        }

        $inv = DB::table('inv_temp')
                ->get();

    return view('do.inv',['item' => $item, 'site' => $site, 'loc' => $loc, 'inv' => $inv ]);
    }

    public function donlod(Request $req) {
        $spb    = $req->s_spbnumber;
        $cust   = $req->s_customer;
        $dfrom  = $req->datefrom;
        $dto    = $req->dateto;
        $status = $req->s_status;
        $site   = $req->s_site;

        return Excel::download(new SpbExport($spb,$cust,$dfrom,$dto,$status,$site), 'Data SPB.xlsx');
    }
}



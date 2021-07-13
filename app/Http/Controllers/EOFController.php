<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Auth;
use File;

use Carbon\Carbon;
use App\Exports\MRPPOExport;
use Maatwebsite\Excel\Facades\Excel;

class EOFController extends Controller
{
    //
    public function index(){
    	$data = DB::table('site_mstrs')
            ->where('site_mstrs.site_code','=',Session::get('site'))
    				->selectRaw("CAST(lasteod as DATE) as 'last_run'")
    				->first();

    	return view('eof',['data' => $data]);
    }

    public function submiteof(Request $req){
    	
    	$users = db::table('site_mstrs')
                    ->where('site_code','=',Session::get('site'))
                    ->where('site_flag','=','N') // Tidak punya cabang
                    ->first();

        if($users){
        	// tidak ada wh
        	$data = DB::table('do_mstr')
        					->join('dod_det','do_mstr.do_nbr','=','dod_det.dod_nbr')
        					->where('dod_flag','=','0') // Belum pernah dimasukin EOD
						      ->where('do_mstr.do_site','=',Session::get('site'))
                  ->where('do_mstr.do_status','!=','3') // itung semua yang tidak closed
        					->where('do_mstr.do_status','!=','2') // itung semua yang tidak confirm
                  ->selectRaw('sum(dod_qty) as "total_qty", dod_part')
        					->groupBy('dod_part')
        					->get();

            //dd($data);

            if($data->isEmpty()){
                  session()->flash('error','There is no new SPB');
                  return back();
            }

            $array_part = array();
            $array_qtypesan = array();

            // WSA
            foreach($data as $datas){

                $barangtype = DB::table('items')
                                ->where('items.itemcode','=',$datas->dod_part)
                                ->where('items.item_type','=','SP') // Khusus Type SP Bkin PO
                                ->first();

                if($barangtype){
                    // Var WSA
                    $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
                    $qxReceiver     = '';
                    $qxSuppRes      = 'false';
                    $qxScopeTrx     = '';
                    $qdocName       = '';
                    $qdocVersion    = '';
                    $dsName         = '';
                    
                    $timeout        = 0;

                    $domain         = 'DKH';
                    $itemcode       = '';

                    // ** Edit here
                    $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                     '<Body>'.
                                     '<sisaQty xmlns="urn:iris.co.id:wsatrain">'.
                                     '<inpdomain>'.$domain.'</inpdomain>'.
                                     '<inpart>'.$datas->dod_part.'</inpart>'.
                                     '<insite>'.Session::get('site').'</insite>'.
                                     '</sisaQty>'.
                                     '</Body>'.
                                     '</Envelope>';

              
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
                    $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];

                    $flag = 0;
                    $item    = '';
                    $qty     = 0;

                    $qtypesan = 0;
                    
                    $sftystok = DB::table('items')
                                    ->where('itemcode','=',$datas->dod_part)
                                    ->first();

                    $dataloop = $xmlResp->xpath('//ns1:tempRow');
                    
                    foreach($xmlResp->xpath('//ns1:t_part') as $data) { 
                        $item = (string) $xmlResp->xpath('//ns1:t_part')[$flag]; // nama item akan kosong jika tidak ketemu di qad.
                        $qty  += (string) $xmlResp->xpath('//ns1:t_qty')[$flag]; // jumlah qty oh

                        $flag += 1;
                    }

        
                    $qtypesan = $sftystok->safety_stock - ($qty - (int)$datas->total_qty); // Qty Pesan = Safety Stok - (Stok - SPB)

                    //dd($item,$sftystok->safety_stock,$qty,(int)$datas->total_qty,$qdocResultx);

                    // Bkin array buat kirim ke Qxtend
                    if($qtypesan > 0){
                      array_push($array_part,$datas->dod_part);
                      array_push($array_qtypesan,$qtypesan);
                    }
                }              	
                
            }


            //dd($array_part,$array_qtypesan);
        	
          	// Var Web
            $datasite = DB::table('site_mstrs')
                        ->where('site_code','=',Session::get('site'))
                        ->first();

            $supplier = DB::table('supp_mstrs')
                        ->where('supp_site','=',Session::get('site'))
                        ->first();

            $suppliercode = '101';
            //$suppliercode = $supplier->supp_code;


            $bulan = substr($datasite->r_nbr_eod, 0, 2);
            $rn    = substr($datasite->r_nbr_eod, 2, 2);

            if($bulan != Carbon::now()->format('m')){
                // Ganti bulan reset bulan & rn
                $bulan = Carbon::now()->format('m');
                $rn    = '01';
            }else{
                $rn += 1;
                $rn = str_pad($rn , 2, '0', STR_PAD_LEFT);
            }
            
            $nopo  = "E".substr(Session::get('site'), 0,2).substr(Carbon::now()->format('Y'),3).$bulan.$rn;

            // Var Qxtend
            $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
            
            $timeout        = 0;
            $line 			    = 1;
            $flg            = 0;

            // XML Qextend
            $qdocHead = '<?xml version="1.0" encoding="UTF-8"?>
                            <soapenv:Envelope xmlns="urn:schemas-qad-com:xml-services"
                              xmlns:qcom="urn:schemas-qad-com:xml-services:common"
                              xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
                              <soapenv:Header>
                                <wsa:Action/>
                                <wsa:To>urn:services-qad-com:QX_DNP</wsa:To>
                                <wsa:MessageID>urn:services-qad-com::QX_DNP</wsa:MessageID>
                                <wsa:ReferenceParameters>
                                  <qcom:suppressResponseDetail>false</qcom:suppressResponseDetail>
                                </wsa:ReferenceParameters>
                                <wsa:ReplyTo>
                                  <wsa:Address>urn:services-qad-com:</wsa:Address>
                                </wsa:ReplyTo>
                              </soapenv:Header>
                              <soapenv:Body>
                                <maintainPurchaseOrder>
                                  <qcom:dsSessionContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>domain</qcom:propertyName>
                                      <qcom:propertyValue>DKH</qcom:propertyValue>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>scopeTransaction</qcom:propertyName>
                                      <qcom:propertyValue>false</qcom:propertyValue>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>version</qcom:propertyName>
                                      <qcom:propertyValue>eB2_3</qcom:propertyValue>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>mnemonicsRaw</qcom:propertyName>
                                      <qcom:propertyValue>false</qcom:propertyValue>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>username</qcom:propertyName>
                                      <qcom:propertyValue>mfg</qcom:propertyValue>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>password</qcom:propertyName>
                                      <qcom:propertyValue>XVytW</qcom:propertyValue>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>action</qcom:propertyName>
                                      <qcom:propertyValue/>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>entity</qcom:propertyName>
                                      <qcom:propertyValue/>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>email</qcom:propertyName>
                                      <qcom:propertyValue/>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>emailLevel</qcom:propertyName>
                                      <qcom:propertyValue/>
                                    </qcom:ttContext>
                                  </qcom:dsSessionContext>';

            // edit povend buat ke dnp
            $qdocbody = '<dsPurchaseOrder>
                            <purchaseOrder>
                              <poNbr>'.$nopo.'</poNbr>
                              <poVend>'.$suppliercode.'</poVend>
                  			      <poShip>'.Session::get('site').'</poShip>
                  			      <poBill>'.Session::get('site').'</poBill>
                              <poSite>'.Session::get('site').'</poSite>
                		   	      <poContract>'.$users->site_flag.'</poContract>
                              <revChange>false</revChange>
                              <poRev>1</poRev>';

                            foreach($array_part as $data){
                              if($array_qtypesan[$flg] > 0){
                                  $qdocbody   .=   '<lineDetail>
                                              <yn>true</yn>
                                              <yn1>true</yn1>
                                              <podLine>'.$line.'</podLine>
                                              <podPart>'.$array_part[$flg].'</podPart>
                                              <podQtyOrd>'.$array_qtypesan[$flg].'</podQtyOrd>
                                            </lineDetail>';

                                            $line += 1;     
                              }
	                                      $flg  += 1;
                      		}
                            
            $qdocbody .= '</purchaseOrder>
                          </dsPurchaseOrder>';


            $qdocfoot = '</maintainPurchaseOrder>
                            </soapenv:Body>
                            </soapenv:Envelope>';

            $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

            //dd($array_part,$array_qtypesan,$qdocRequest);

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
                //
                $curlErrno = curl_errno($curl);
                $curlError = curl_error($curl);
                $first = true;
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
            $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');
            $qdocResult = (string) $xmlResp->xpath('//ns1:result')[0];  

		        //dd($qdocResponse); 

            if($qdocResult=="success" OR $qdocResult=="warning")
            {
                $data = DB::table('do_mstr')
                              ->join('dod_det','do_mstr.do_nbr','=','dod_det.dod_nbr')
                              ->where('dod_flag','=','0') // Belum pernah dimasukin EOD
		                          ->where('do_site','=',Session::get('site'))
                              ->update([
                                      'dod_flag' => '1' // 0 belum pernah 1 udh pernah
                              ]);

            		$users = db::table('site_mstrs')
                                ->where('site_code','=',Session::get('site'))
                                ->where('site_flag','=','N') // Tidak punya cabang
                                ->update([
                          			 'r_nbr_eod' => $bulan.$rn	
                          			]);


                DB::table('site_mstrs')
                    ->where('site_mstrs.site_code','=',Session::get('site'))
                    ->update([
                      'lasteod' => Carbon::now()->toDateString(),
                    ]);

                Log::channel('customlog')->info('PO End Of Day : '.$nopo.' Successfully Created');
                session()->flash('updated','PO End Of Day : '.$nopo.' Successfully Created');
                return back();

            }else{
                Log::channel('customlog')->info('PO End Of Day : '.$nopo.' Failed to be Created');
                session()->flash('error','PO End Of Day : '.$nopo.' Failed to be Created');
                return back();
            }


        }
        else{
          session()->flash('error','Menu khusus site tanpa WH');
          return back();
        }
    }

    public function loadloc(Request $req){
          // Var WSA
          $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
          $qxReceiver     = '';
          $qxSuppRes      = 'false';
          $qxScopeTrx     = '';
          $qdocName       = '';
          $qdocVersion    = '';
          $dsName         = '';
          
          $timeout        = 0;

          $domain         = 'DKH';
          $itemcode       = '';

          // ** Edit here
          $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                            '<loadLoc xmlns="urn:iris.co.id:wsatrain">'.
                            '<inpdomain>'.$domain.'</inpdomain>'.
                            '</loadLoc>'.
                            '</Body>'.
                            '</Envelope>';

    
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

          foreach($xmlResp->xpath('//ns1:tempRow') as $data){
              DB::table('loc_mstrs')
                  ->updateOrInsert([
                      'loc_loc' => $data->t_loc,
                      'loc_site' => $data->t_site,
                   ],[
                      'created_at' => Carbon::now()->toDateTimeString(),
                      'updated_at' => Carbon::now()->toDateTimeString(),
                  ]);
          }


          Session()->flash('updated','Location Successfully Updated');
          return back();   
    }

    public function locmenu(Request $req){
      $data = DB::table('loc_mstrs')
              ->paginate(10);

      if($req->ajax()){

        $sort_by = $req->get('sortby');
        $sort_type = $req->get('sorttype');

        if($req->locsite == '' and $req->locloc == ''){
            $data = DB::table('loc_mstrs')
                ->orderBy($sort_by, $sort_type)
                ->paginate(10);

            return view('setting.table-locmaster',['data' => $data]);
        }else{
            $query = 'id >= 1 ';

            if($req->locsite != ''){
              $query .= 'and loc_site = "'.$req->locsite.'"';
            }
            if($req->locloc != ''){
              $query .= 'and loc_loc = "'.$req->locloc.'"';
            }
            

            $data = DB::table('loc_mstrs')
                    ->whereRaw($query)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

            return view('setting.table-locmaster',['data' => $data]);
        }
        
      }

      return view('setting.locmaster',['data' => $data]);
    }

    public function loadParentC(Request $req){
          // Var WSA
          $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
          $qxReceiver     = '';
          $qxSuppRes      = 'false';
          $qxScopeTrx     = '';
          $qdocName       = '';
          $qdocVersion    = '';
          $dsName         = '';
          
          $timeout        = 0;

          $domain         = 'DKH';
          $itemcode       = '';

          // ** Edit here
          $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                            '<loadParentC xmlns="urn:iris.co.id:wsatrain">'.
                            '<inpdomain></inpdomain>'.
                            '</loadParentC>'.
                            '</Body>'.
                            '</Envelope>';

    
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

          foreach($xmlResp->xpath('//ns1:tempRow') as $data){
              DB::table('cust_relation')
                  ->updateOrInsert([
                      'cust_code_parent' => $data->t_child,
                      'cust_code_child' => $data->t_parent,
                   ],[
                      'created_at' => Carbon::now()->toDateTimeString(),
                      'updated_at' => Carbon::now()->toDateTimeString(),
                  ]);
          }


          Session()->flash('updated','Customer Relation Successfully Updated');
          return back();   
    }

    public function checkqad(Request $req){
      // Var WSA
        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
        $qxReceiver     = '';
        $qxSuppRes      = 'false';
        $qxScopeTrx     = '';
        $qdocName       = '';
        $qdocVersion    = '';
        $dsName         = '';
        
        $timeout        = 0;

        $domain         = 'DKH';
        $itemcode       = '';

        // ** Edit here
        $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                          '<Body>'.
                          '<checkSO xmlns="urn:iris.co.id:wsatrain">'.
                          '<inpdomain>'.$domain.'</inpdomain>'.
                          '</checkSO>'.
                          '</Body>'.
                          '</Envelope>';


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

        
        $flag = 0;
        
        $item    = '';
        $qty     = 0;
        $qty_all = 0;
        $qtypo  = 0;

        $dataloop    = $xmlResp->xpath('//ns1:tempRow');
        $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];

        //dd($qdocResultx,$qdocResponse,$dataloop);
        $flg = 0;
        if($qdocResultx == 'true'){
          $listso = DB::table('so_mstrs')
                        ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
                        ->whereRaw('Date(so_dets.created_at) = "'.Carbon::now()->format('Y-m-d').'"')
                        ->get();
          //dd($listso);
          // create temp table
          Schema::create('temp_table', function($table)
          {
              $table->string('so_nbr');
              $table->integer('so_line');
              $table->string('so_part');
              $table->decimal('so_qty_ord');
              $table->string('flg')->nullable();
              $table->timestamps();
              $table->temporary();
          });

          foreach($listso as $listso){
            DB::table('temp_table')
                    ->insert([
                        'so_nbr' => $listso->so_nbr,
                        'so_line' => $listso->so_line,
                        'so_part' => $listso->so_itemcode,
                        'so_qty_ord' => $listso->so_qty,
                        'flg' => '0',
                    ]);
          }

          foreach($dataloop as $dataloop){
              DB::table('temp_table')
                      ->where('so_nbr','=',$dataloop->t_sonbr)
                      ->where('so_line','=',$dataloop->t_line)
                      ->where('so_part','=',$dataloop->t_part)
                      ->where('so_qty_ord','=',$dataloop->t_qtyord)
                      ->update([
                          'flg' => '1',
                      ]);
          }

          $errorlist = DB::table('temp_table')->where('flg','=','0')->get();

          Schema::drop('temp_table');
        }
        
        if(!$errorlist->isEmpty()){
          Session()->flash('updated','There is no difference between Web & QAD');
          return back();
        }else{
          Session()->flash('error','There are difference(s) between Web & QAD');
          return back();
        }
    }

    public function loadexcel(Request $req){
        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
        $qxReceiver     = '';
        $qxSuppRes      = 'false';
        $qxScopeTrx     = '';
        $qdocName       = '';
        $qdocVersion    = '';
        $dsName         = '';
        
        $timeout        = 0;

        $itemcode       = '';
        $domain         = 'DKH';

        // ** Edit here
        $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                          '<Body>'.
                          '<loadSODKH xmlns="urn:iris.co.id:wsatrain">'.
                          '<inpdomain>'.$domain.'</inpdomain>'.
                          '</loadSODKH>'.
                          '</Body>'.
                          '</Envelope>';


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

        
        $flag = 0;
        
        $item    = '';
        $qty     = 0;
        $qty_all = 0;
        $qtypo  = 0;

        $dataloop    = $xmlResp->xpath('//ns1:tempRow');
        $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];

        if($qdocResultx == 'true'){
          foreach($dataloop as $data){
                // check so_mstr
                $so = DB::table('so_mstrs')
                        ->where('so_mstrs.so_nbr','=',$data->t_sonbr)
                        ->first();

                if(!$so){
                  DB::table('so_mstrs')
                        ->insert([
                              'so_nbr' => $data->t_sonbr,
                              'so_cust' => $data->t_socust,
                              'so_duedate' => $data->t_soduedate,
                              'so_shipto' => $data->t_soshipto,
                              'so_status' => '1',
                              'so_site' => $data->t_sosite,
                              'so_user' => Session::get('username'),
                              'so_po' => $data->t_sopo,
                              'created_at' => $data->t_soorddate,
                              'updated_at' => Carbon::now()->toDateTimeString(),
                        ]);
                }


                // check so_det

                $sod = DB::table('so_mstrs')
                        ->join('so_dets','so_dets.so_nbr','=','so_mstrs.so_nbr')
                        ->where('so_mstrs.so_nbr','=',$data->t_sonbr)
                        ->where('so_dets.so_line','=',$data->t_soline)
                        ->where('so_dets.so_itemcode','=',$data->t_soitemcode)
                        ->first();

                if(!$sod){
                    DB::table('so_dets')
                          ->insert([
                                'so_nbr' => $data->t_sonbr,
                                'so_itemcode' => $data->t_soitemcode,
                                'so_qty' => $data->t_soqty,
                                'so_line' => $data->t_soline,
                                'so_qty_open' => $data->t_soqty,
                                'so_um' => $data->t_soum,
                                'so_status' => '1',
                                'so_harga' => $data->t_soharga,
                                'so_pr_list' => $data->t_pr_list,
                                'so_disc' => $data->t_sodisc,
                                'created_at' => $data->t_soorddate,
                                'updated_at' => Carbon::now()->toDateTimeString(),
                          ]);
                }


              // Var WSA
                $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
                $qxReceiver     = '';
                $qxSuppRes      = 'false';
                $qxScopeTrx     = '';
                $qdocName       = '';
                $qdocVersion    = '';
                $dsName         = '';
                
                $timeout        = 0;

                $domain         = 'DKH';
                $itemcode       = '';

                // ** Edit here
                $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                    '<Body>'.
                    '<cekPriceDiscSO xmlns="urn:iris.co.id:wsatrain">'.
                    '<inpdomain>'.$domain.'</inpdomain>'.
                    '<insonbr>'.$data->t_sonbr.'</insonbr>'.
                    '</cekPriceDiscSO>'.
                    '</Body>'.
                    '</Envelope>';

          
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

                $datadetprice    = $xmlResp->xpath('//ns1:tempRow');
                $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
                $hargahead = 0;
                if ($qdocResultx == 'true')  {
                  foreach($datadetprice as $det){
                    $hargahead += $det->t_qtyord * $det->t_netprice;
                  }
                  DB::table('so_mstrs')
                          ->where('so_nbr','=',$data->t_sonbr)
                          ->update([
                              'so_price' => $hargahead,
                          ]);

                }
          }

          Session()->flash('updated','Load SO Success');
          return back();
        }else{
          Session()->flash('error','WSA Failed');
          return back();
        }
    }

    public function runnbrmenu(Request $req){
        $data = DB::table('site_mstrs')
                        ->get();

        return view('/setting/runnbrmaint', compact('data'));
    }

    public function updatemenurnbr(Request $req){
      
      DB::table('site_mstrs')
              ->where('site_code','=',$req->e_sitecode)
              ->update([
                  'site_desc' => $req->e_sitedesc,
                  'r_nbr_so' => $req->e_rnbrso,
                  'r_nbr_spb' => $req->e_rnbrspb,
                  'r_nbr_cons' => $req->e_rnbrcons,
              ]);

      Session()->flash('updated','Running Number Succesfully Updated');
      return back();   
    }

    public function loadsodkh(Request $req){
       // Open CSV File n Read
        $file = fopen(public_path('50add1.csv'),"r");

        $importData_arr = array();
          $i = 0;

          while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
             $num = count($filedata );
             
             // Skip first row (Remove below comment if you want to skip the first row)
             if($i == 0){
                $i++;
                continue; 
             }
             for ($c=0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata [$c];
             }
             $i++;
          }
          fclose($file);

        //dd($importData_arr);
        //Insert or Update to MySQL database
        
        foreach($importData_arr as $importData){
            
            $header = DB::table('so_mstrs')
                        ->where('so_nbr','=',$importData[0])
                        ->first();

            if(!$header){
              DB::table('so_mstrs')
                      ->insert([
                          'so_nbr' => $importData[0],
                          'so_cust' => $importData[2],
                          'so_duedate' => $importData[11],
                          'so_shipto' => $importData[5],
                          'so_status' => '1',
                          //'so_price' => $importData[2],
                          'so_site' => $importData[9],
                          'so_user' => Session::get('username'),
                          'so_po' => $importData[28],
                          'created_at' => $importData[1],
                          'updated_at' => Carbon::now()->toDateTimeString(),
                      ]);
            }

            $detail = DB::table('so_dets')
                          ->where('so_nbr','=',$importData[0])
                          ->where('so_line','=',$importData[6])
                          ->where('so_itemcode','=',$importData[7])
                          ->first();

            if(!$detail){
              DB::table('so_dets')
                      ->insert([
                          'so_nbr' => $importData[0],
                          'so_itemcode' => $importData[7],
                          'so_qty' => $importData[12],
                          'so_line' => $importData[6],
                          'so_qty_open' => $importData[14],
                          'so_um' => $importData[10],
                          'so_status' => '1',
                          'so_harga' => $importData[18],
                          'so_pr_list' => $importData[16],
                          'so_disc' => $importData[17],
                          'created_at' => $importData[1],
                          'updated_at' => Carbon::now()->toDateTimeString(),
                      ]);
            }

            $hdet = DB::table('so_dets')
                        ->where('so_nbr','=',$importData[0])
                        ->get();
            
            if(!is_null($hdet)){
                $total = 0;
                foreach($hdet as $hdet){
                    $total += $hdet->so_harga * $hdet->so_qty;  
                }

                DB::table('so_mstrs')
                        ->where('so_nbr','=',$importData[0])
                        ->update([
                            'so_price' => $total,
                        ]);

            }

            
        }
    }

    public function menuchecksoqad(Request $req){
          $site = DB::table('site_mstrs')
                      ->get();

          $data = [];

          return view('valqad.menu-valso', ['data' => $data, 'site' => $site]);
    }

    public function checksoqad(Request $req){
        // dd($req->all());

        $date = '';
        $site = '';
        $query = "Date(so_mstrs.created_at) = '".$req->datepick."'";

        if(!is_null($req->site)){
          $query .= ' AND so_site = "'.$req->site.'" ';
        }

        $listso = DB::table('so_mstrs')
                        ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
                        ->whereRaw($query)
                        ->where('so_mstrs.so_status','!=','5')
                        ->selectRaw('*,date(so_mstrs.created_at) as "ord_date"')
                        ->orderBy('so_mstrs.so_nbr')
                        ->get();

        $site = DB::table('site_mstrs')
                      ->get();
        // dd($listso);
        

        Schema::create('temp_table', function($table)
        {
            $table->string('so_nbr');
            $table->integer('so_line');
            $table->string('so_part');
            $table->decimal('so_qty_ord');
            $table->string('so_part_qad');
            $table->decimal('so_qty_ord_qad');
            $table->date('so_due_date');
            $table->date('so_ord_date');
            $table->string('flg')->nullable();
            $table->timestamps();
            $table->temporary();
        });

        foreach($listso as $listso){
          // Var WSA
          $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
          $qxReceiver     = '';
          $qxSuppRes      = 'false';
          $qxScopeTrx     = '';
          $qdocName       = '';
          $qdocVersion    = '';
          $dsName         = '';
          
          $timeout        = 0;

          $domain         = 'DKH';
          $itemcode       = '';

          // ** Edit here
          $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                            '<checkSOWeb xmlns="urn:iris.co.id:wsatrain">'.
                            '<inpdomain>'.$domain.'</inpdomain>'.
                            '<inpsonbr>'.$listso->so_nbr.'</inpsonbr>'.
                            //'<inpsodpart>'.$listso->so_itemcode.'</inpsodpart>'.
                            '<inpsoline>'.$listso->so_line.'</inpsoline>'.
                            //'<inpqtyord>'.$listso->so_qty.'</inpqtyord>'.
                            '</checkSOWeb>'.
                            '</Body>'.
                            '</Envelope>';
          
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

          
          $flag = 0;
          
          $item    = '';
          $qty     = 0;
          $qty_all = 0;
          $qtypo  = 0;

          $dataloop    = $xmlResp->xpath('//ns1:tempRow');
          $qdocResult = (string) $xmlResp->xpath('//ns1:outOK')[0];

          if($qdocResult == 'true'){
            if($listso->so_itemcode != $dataloop[0]->t_part or $listso->so_qty != $dataloop[0]->t_qtyord){
              DB::table('temp_table')
                      ->insert([
                          'so_nbr' => $listso->so_nbr,
                          'so_line' => $listso->so_line,
                          'so_part' => $listso->so_itemcode,
                          'so_qty_ord' => $listso->so_qty,
                          'so_part_qad' => $dataloop[0]->t_part,
                          'so_qty_ord_qad' => $dataloop[0]->t_qtyord,
                          'so_due_date' => $listso->so_duedate,
                          'so_ord_date' => $listso->ord_date,
                          'flg' => '0',
                      ]);
            }
          }else{
            DB::table('temp_table')
                    ->insert([
                        'so_nbr' => $listso->so_nbr,
                        'so_line' => $listso->so_line,
                        'so_part' => $listso->so_itemcode,
                        'so_qty_ord' => $listso->so_qty,
                        'so_due_date' => $listso->so_duedate,
                        'so_ord_date' => $listso->ord_date,
                        'flg' => '0',
                    ]);
          }

        }

        $errorlist = DB::table('temp_table')->orderBy('so_nbr')->get();

        Schema::drop('temp_table');

        // dd($errorlist);
        if(count($errorlist) > 0){
          Session()->forget('updated');
          Session()->flash('error','There are difference(s) between Web & QAD');
        }else{
          Session()->forget('error');
          Session()->flash('updated','There is a no difference between Web & QAD');
        }


          return view('valqad.menu-valso', ['data' => $errorlist, 'site' => $site]);
    }

    public function mrpeod(Request $req){
        $data = DB::table('site_mstrs')
                  ->where('site_mstrs.site_code','=',Session::get('site'))
                  ->first();
                  
        return view('mrp.mrpsend',['data' => $data]);
    }

    public function createeod(Request $req){
      // dd($req->all());

      // $data = DB::table('po_eod')
      //           ->where('site','=',Session::get('site'))
      //           ->first();


      // if($data){
      //   session()->flash('error','Please Generate PO End Of Day from current MRP before submitting a new MRP');
      //   return back();
      // }

      $datasite = DB::table('site_mstrs')
                  ->where('site_code','=',Session::get('site'))
                  ->first();

      if(substr($datasite->r_nbr_eod, 2, 2) >= 3){
        session()->flash('error','You have generated 3 PO today');
        return back();
      }


      // Run MRP
      $content = '';
      $content .= '"'.$req->site.'"'.' '.'"'.$req->site.'"'.' '.'"yes"'.' '.'""'.' '.'""'.PHP_EOL;
      $content .= '"/mrp/mrp"'.PHP_EOL;
      $content .= '"."';

      File::put('cim/xxcimmrp.cim',$content); 


      DB::table('site_mstrs')
              ->where('site_code','=',Session::get('site'))
              ->update([
                  'lasteod' => Carbon::now()->toDateTimeString(),
              ]);

      shell_exec('sh /opt/lampp/htdocs/web_danapaint/public/cim/xxcimmrp.sh');


      // WSA
      $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
      $qxReceiver     = '';
      $qxSuppRes      = 'false';
      $qxScopeTrx     = '';
      $qdocName       = '';
      $qdocVersion    = '';
      $dsName         = '';
      
      $timeout        = 0;

      $domain         = 'DKH';
      $itemcode       = '';

      // ** Edit here
      $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
            '<Body>'.
            '<xxwsapo xmlns="urn:iris.co.id:wsatrain">'.
            '<isite>'.Session::get('site').'</isite>'.
            '</xxwsapo>'.
            '</Body>'.
            '</Envelope>';


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
      $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
      $dataloop = $xmlResp->xpath('//ns1:tempRow');

      $output = [];
      
      if($qdocResultx == 'true'){
        DB::table('po_eod')
            ->where('site','=',Session::get('site'))
            ->delete();

        foreach($dataloop as $data){
          DB::table('po_eod')
              ->insert([
                'item_part' => $data->t_part,
                'item_desc' => $data->t_desc,
                'qty_po' => $data->t_qty,
                'site' => Session::get('site'),
                'item_type' => $data->t_type,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
              ]);
        }

        // $output = DB::table('po_eod')->where('site','=',Session::get('site'))->get();
        session()->flash('updated','File sent to QAD MRP, Please check data before generating PO End of Day');
        return redirect()->route('mrppo'); 
      
      }else{

        Log::channel('customlog')->info('WSA MRP PO returns false, Site : '.Session::get('site'));
        session()->flash('error','There is no new MRP PO');
        return redirect()->route('mrpeod'); 
      }
    }

    public function mrppo(Request $req){
      

      if($req->ajax()){
        if($req->type == 'All'){
          $data = DB::table('po_eod')
            ->where('site','=',Session::get('site'))
            ->orderBy('item_type','ASC')
            ->get();
        }else{
          $data = DB::table('po_eod')
            ->where('site','=',Session::get('site'))
            ->where('item_type','=',$req->type)
            ->orderBy('item_type','ASC')
            ->get();
        }

        

        $output = '';
        $flg = 1;
        if($data->count() > 0){
          foreach($data as $data){
            $output .= '<tr>';
            $output .= '<td>'.$flg.'</td>';
            $output .= '<td>'.$data->item_part.'</td>';
            $output .= '<td>'.$data->item_desc.'</td>';
            $output .= '<td>'.$data->item_type.'</td>';
            $output .= '<td>'.$data->qty_po.'</td>';
            $output .= '</tr>';


            $flg += 1;
          }
        }else{
          $output = '<tr><td colspan="5" style="color:red"><center>No Data Available</center></td></tr>';
        }

        return response($output);

      }else{
        $data = DB::table('po_eod')
            ->where('site','=',Session::get('site'))
            ->orderBy('item_type','ASC')
            ->get();

        return view('mrp.mrppo',['data' => $data]);
      }
    }

    public function createmrppo(Request $req){
    	$data = DB::table('po_eod')
    				->where('site','=',Session::get('site'))
            ->where('item_type','=','SP')
    				->get();

    	if(!$data->isEmpty()){
        // ada data
				// Var Qxtend
            ini_set('max_execution_time', '600');

		        $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
		        
		        $timeout        = 0;

		        $supplier = DB::table('supp_mstrs')
		                      ->where('supp_mstrs.supp_site','=',Session::get('site'))
		                      ->first();

		        $povend = '101';
		        $socust = ''; // taro di pocontract buat jdi customer di so dnp
            $nbrflg = '1';
		        if($supplier){
		            $socust = $supplier->supp_code;
		        }

            // Var Web
            $datasite = DB::table('site_mstrs')
                  ->where('site_code','=',Session::get('site'))
                  ->first();

            $hari = substr($datasite->r_nbr_eod, 0, 2);
            $rn    = substr($datasite->r_nbr_eod, 2, 2);

            $alphabet = array('', 'A', 'B', 'C', 'D', 'E',
                             'F', 'G', 'H', 'I', 'J',
                             'K', 'L', 'M', 'N', 'O',
                             'P', 'Q', 'R', 'S', 'T',
                             'U', 'V', 'W', 'X', 'Y',
                             'Z'
                             );

            if($hari != Carbon::now()->format('d')){
                // Ganti bulan reset bulan & rn
                $hari = Carbon::now()->format('d');
                $rn    = '01';
            }else{
                $rn += 1;
                $rn = str_pad($rn , 2, '0', STR_PAD_LEFT);
            }
            
            $nopo  = substr(Session::get('site'), 0,2).substr(Carbon::now()->format('Y'),3).Carbon::now()->format('m').$hari.$alphabet[intval($rn)];



		        // XML Qextend
		        $qdocHead = '<?xml version="1.0" encoding="UTF-8"?>
		                        <soapenv:Envelope xmlns="urn:schemas-qad-com:xml-services"
		                          xmlns:qcom="urn:schemas-qad-com:xml-services:common"
		                          xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
		                          <soapenv:Header>
		                            <wsa:Action/>
		                            <wsa:To>urn:services-qad-com:QX_DNP</wsa:To>
		                            <wsa:MessageID>urn:services-qad-com::QX_DNP</wsa:MessageID>
		                            <wsa:ReferenceParameters>
		                              <qcom:suppressResponseDetail>false</qcom:suppressResponseDetail>
		                            </wsa:ReferenceParameters>
		                            <wsa:ReplyTo>
		                              <wsa:Address>urn:services-qad-com:</wsa:Address>
		                            </wsa:ReplyTo>
		                          </soapenv:Header>
		                          <soapenv:Body>
		                            <maintainPurchaseOrder>
		                              <qcom:dsSessionContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>domain</qcom:propertyName>
		                                  <qcom:propertyValue>DKH</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>scopeTransaction</qcom:propertyName>
		                                  <qcom:propertyValue>true</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>version</qcom:propertyName>
		                                  <qcom:propertyValue>eB2_3</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>mnemonicsRaw</qcom:propertyName>
		                                  <qcom:propertyValue>false</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>username</qcom:propertyName>
		                                  <qcom:propertyValue>mfg</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>password</qcom:propertyName>
		                                  <qcom:propertyValue>XVytW</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>action</qcom:propertyName>
		                                  <qcom:propertyValue/>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>entity</qcom:propertyName>
		                                  <qcom:propertyValue/>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>email</qcom:propertyName>
		                                  <qcom:propertyValue/>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                  <qcom:propertyName>emailLevel</qcom:propertyName>
		                                  <qcom:propertyValue/>
		                                </qcom:ttContext>
		                              </qcom:dsSessionContext>';

		        // edit povend buat ke dnp **EDIT HERE
		        $qdocbody = '<dsPurchaseOrder>
		                        <purchaseOrder>
		                          <poNbr>'.$nopo.'</poNbr>
		                          <poVend>'.$povend.'</poVend>
		                          <poShip>'.Session::get('site').'</poShip>
		                          <poBill>'.Session::get('site').'</poBill>
		                          <poSite>'.Session::get('site').'</poSite>
		                          <revChange>false</revChange>
		                          <poRev>'.$nbrflg.'</poRev>';

                              $flg = 1;

                              foreach($data as $data){

                                $qdocbody .= '<lineDetail>
                                              <yn>true</yn>
                                              <yn1>true</yn1>
                                              <line>'.$flg.'</line>
                                              <podSite>'.Session::get('site').'</podSite>
                                              <podPart>'.$data->item_part.'</podPart>
                                              <podQtyOrd>'.$data->qty_po.'</podQtyOrd>
                                              </lineDetail>';

                                $flg += 1;
                              }
		                            
		                          

            $qdocbody .=  ' </purchaseOrder>
		                      </dsPurchaseOrder>';

		        $qdocfoot = '</maintainPurchaseOrder>
		                        </soapenv:Body>
		                        </soapenv:Envelope>';

		        $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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
		            //
		            $curlErrno = curl_errno($curl);
		            $curlError = curl_error($curl);
		            $first = true;
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

            $qdocResult = "";

            if($qdocResponse != false){
              $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');
              $qdocResult = (string) $xmlResp->xpath('//ns1:result')[0];             
            }
		        

		        if($qdocResult=="success" OR $qdocResult=="warning" OR $qdocResponse == false)
		        {   
		          //dd('ok');
                DB::table('po_eod')
                      ->where('site','=',Session::get('site'))
                      ->where('item_type','=','SP')
                      ->delete();

                $users = db::table('site_mstrs')
                                ->where('site_code','=',Session::get('site'))
                                ->update([
                                 'r_nbr_eod' => $hari.$rn  
                                ]);

                session()->flash('updated','PO Successfully Created, Nomor PO : '.$nopo);
                return back();
		        }else{
		            Log::channel('customlog')->info('PO EOD Failed - '.Session::get('site'));
		            session()->flash('error','Failed to generate PO');
                return back();
            }
    	}else{
    		session()->flash('error','There is no data item SP to create PO');
    		return back();
    	}
    }

    public function menuspbcheck(Request $req){
      $site = DB::table("site_mstrs")
                  ->get();

      $item = DB::table('items')
                  ->get();
      return view('mrp.spbcheck',['site' => $site, 'item' => $item]);
    }

    public function spbcheck(Request $req){
      $site = $req->site;
      $itemcode = $req->itemcode;
      
      $kondisi = 'dod_det.id > 1';

      if(!is_null($site)){
        $kondisi .= ' and do_mstr.do_site = "'.$site.'"';
      }
      if(!is_null($itemcode)){
        $kondisi .= ' and dod_det.dod_part = "'.$itemcode.'"';
      }

      $data = DB::table('do_mstr')
                  ->join('dod_det','dod_det.dod_nbr','=','do_mstr.do_nbr')
                  ->join('items','items.itemcode','=','dod_det.dod_part')
                  ->where(function($query){
                    $query->where('do_status','=',1)->orWhere('do_status','=',4);
                  })
                  ->whereRaw($kondisi)
                  ->get();

      $output = '';
      $status = '';
      if($data->count() > 0){
          foreach($data as $data){
            if($data->do_status == '1'){
                        $status = 'Created';
                      }else{
                        $status = 'Ready To Ship';
                      }

            $output .= '<tr><td>'.$data->dod_nbr.'</td>'.
                     '<td>'.$data->dod_so.'</td>'.
                     '<td>'.$data->dod_part.'</td>'.
                     '<td>'.$data->itemdesc.'</td>'.
                     '<td>'.$data->dod_qty.'</td>'.
                     '<td>'.$status.'</td></tr>';
          }

          return response($output);
      }else{
        $output = '<tr><td colspan="6" style="color:red;"><center>No Data Available</center></td><tr>';
        return response($output);
      }
    }

    public function exportmrppo(Request $req){
      $namafile = 'PO MRP - '.date('Y-m-d').'.xlsx';

      return Excel::download(new MRPPOExport($req->c_type), $namafile);
    }


    // HTTP Header WSA
    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }
}

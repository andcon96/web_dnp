<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Log;
use PDF;

class SalesOrderSADController extends Controller
{
    public function index(){
      // Status SO -> 1 Created 2 On Hold 3 Hold QAD 4 Hold Both 5 Deleted
      
      if(Session::get('pusat_cabang')==1){
        $data = DB::table('so_mstrs')
        ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
	      ->where('so_status', '!=', 10)
        ->where('so_status','!=',11)
        ->where('so_status','!=',12)
        ->selectRaw('*,so_mstrs.created_at as "so_created"')
        ->orderBy('so_mstrs.so_nbr','Desc')
        ->paginate(10);  
      }
      if(Session::get('pusat_cabang')==0){
        $data = DB::table('so_mstrs')
            ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
            ->where('so_status', '!=', 10)
            ->where('so_status','!=',11)
            ->where('so_status','!=',12)
            ->where('so_site','=',Session::get('site'))
            ->selectRaw('*,so_mstrs.created_at as "so_created"')
            ->orderBy('so_mstrs.so_nbr','Desc')
            ->paginate(10);
      }
    	

    	$customer = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
    				->get();

    	$item = DB::table('items')
    				->get();

      $site = DB::table('site_mstrs')
            ->get();


    	return view('so.sosadbrowse',['data' => $data, 'customer' => $customer, 'item' => $item, 'itemedit' => $item, 'custsearch' => $customer, 'site' => $site]);
    }

    public function detailsales(Request $req){
      // dd('aaaa');
    	if($req->ajax()){
    		$data1 = DB::table('so_mstrs')
    					->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
    					->join('items','items.itemcode','=','so_dets.so_itemcode')
    					->where('so_mstrs.so_nbr','=',$req->sonbr)
              ->get();
              $output = '';

    		if(count($data1) != 0){
	    		foreach($data1 as $dets){
            $qtyship = 0;
            $qtyship = $dets->so_qty - $dets->so_qty_open;
            
            // 11 Januari 2021
            $hargadet = 0;
            $total = $dets->so_qty * $dets->so_harga;

            if(strpos($dets->so_pr_list,".00000") !== false){
                $hargadet = number_format($dets->so_pr_list,2,'.',',');
                $total = number_format(floor($dets->so_harga * $dets->so_qty),0,'.',',');
            }else{
                if(strpos(strrev(rtrim(($dets->so_pr_list), "0")), ".") == 1){
                    $hargadet = number_format($dets->so_pr_list,2,'.',',');
                }else{
                    $hargadet = rtrim(number_format($dets->so_pr_list,5,'.',','), "0");
                }

                if(strpos(strrev(rtrim(($dets->so_harga * $dets->so_qty), "0")), ".") == 1){
                    $total = number_format(floor($dets->so_harga * $dets->so_qty),0,'.',',');
                }else{
                    $total = number_format(floor($dets->so_harga * $dets->so_qty),0,'.',',');
                }

            }

	    			$output .= '<tr class="foottr">'.

	    					   '<td class="foot1" data-label="Item">'.$dets->itemcode.' - '.$dets->itemdesc.'</td>'.
	    					   '<td class="foot2" data-label="Qty" style="text-align:right;">'.$dets->so_qty.'</td>'.
                   '<td class="foot2" data-label="UM">'.$dets->so_um.'</td>'.
                   '<td class="foot2" data-label="Qty Ship" style="text-align:right;">'.
                   number_format($qtyship,2,'.',',').'</td>'.
                   '<td class="foot2" data-label="Price" style="text-align:right;">'. number_format($dets->so_pr_list,0,".",',') .'</td>'.
                   '<td class="foot2" data-label="Disc" style="text-align:right;">'.$dets->so_disc.'</td>'.
                   '<td class="foot2" data-label="Total" style="text-align:right;">'.$total.'</td>'.
                   '<td class="foot2" data-label="Loc">'.$dets->item_location.'</td>'.

	    			           '</tr>';
	    		}

        }
         //dd($data1);
        return response($output);

    	}
	  }

    public function shiptoedit(Request $req){
      if($req->ajax()){
          $data = DB::table('cust_shipto')
                  ->join('customers','customers.cust_code','=','cust_shipto.cust_code')
                  ->join('customers as shipto','shipto.cust_code','=','cust_shipto.shipto')
                  ->where('cust_shipto.cust_code','=',$req->cust)
                  ->selectRaw('*,shipto.cust_desc as namashipto')
                  ->get();

          $shiptoso = DB::table('so_mstrs')
                    ->where("so_nbr",'=',$req->sonbr)
                    ->first();

          $output = '';
      	  if($data->isEmpty()){
      		$nama = DB::table('customers')
      				->where('cust_code','=',$req->cust)
      				->first();

      		$output .= '<option value="'.$req->cust.'">'.$req->cust.' - '.$nama->cust_desc.'</option>';
      	  }

          foreach($data as $data){
              if($data->shipto == $shiptoso->so_shipto){
                $output .= '<option value="'.$data->shipto.'" selected>'.$data->shipto.' - '.$data->namashipto.'</option>';
              }else{
                $output .= '<option value="'.$data->shipto.'">'.$data->shipto.' - '.$data->namashipto.'</option>';
              }
          };

          return response($output);
      }
    }

    public function createsosales(Request $req){
      // Validasi Web

      //DD($req->all());
      
        if($req->shipto == ''){
            session()->flash('error','Data Shipto harus diisi terlebih dahulu lewat QAD');
            return back();
        }else if($req->barang == ''){
            session()->flash('error','Detail Harus diisi minimal 1 item');
            return back();
        }

        $cekapproval = DB::table('approvals')
                              ->where('site_app','=',Session::get('site'))
                              ->first();

        if(!$cekapproval){
           // tidak ada data approval di web
            session()->flash('error','Mohon lengkapi data Approval untuk site '.Session::get('site'));
            return back();
        }

        $barangke = 0;

        $users = db::table('site_mstrs')
                    ->where('site_code','=',Session::get('site'))
                    ->where('site_flag','=','N') // Tidak punya WH
                    ->first();
        //dd($users);
        // ------------------------ WSA -> klo ada wh ga perlu 
        if($users){
            foreach($req->barang as $barang){
                // dd($req->barang[$barangke],$req->jumlah[$barangke]);
                $barangtype = DB::table('items')
                                ->where('items.itemcode','=',$barang)
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
                                       '<inpart>'.$barang.'</inpart>'.
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

                      
                      $flag = 0;
                      
                      $item    = '';
                      $qty     = 0;
                      $qty_all = 0;
                      $qtypo  = 0;

                      $dataloop    = $xmlResp->xpath('//ns1:tempRow');
                      $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
                      //dd($qdocResultx);
                      if ($qdocResultx == 'true')  {
                        foreach($dataloop as $data) {
                            $item = $data->t_part;
                            $qty += $data->t_qty;
                            $qty_all += $data->t_qty_all;
                            $flag += 1;
                        }

                        $qtysisa = $qty - $qty_all;
                        if($qtysisa <= 0){
                          $qtypo = $req->jumlah[$barangke];
                        }else{
                          $qtypo = $req->jumlah[$barangke] + $qty_all - $qty;
                        }

                        //dd($item,$req->jumlah[$barangke],$qtysisa);


                        if($item != '' && $req->jumlah[$barangke] > $qtysisa){
                            // Barang Dipesan > Stok OH --> Bkin PO ke QAD DNP

                            // $nbrflg = DB::table('so_mstrs')
                            //               ->where('so_site','=',Session::get('site'))
                            //               ->whereRaw('Date(created_at) = "'.Carbon::today().'"')
                            //               ->count();

                            // $nbrflg += 1;

                            $nbrflg = 0;

                            $nopo  = "Q".substr(Session::get('site'), 0,2).substr(Carbon::now()->format('Y'),3).Carbon::now()->format('md');


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
                                             '<checkPORev xmlns="urn:iris.co.id:wsatrain">'.
                                             '<inpdomain>'.$domain.'</inpdomain>'.
                                             '<inpponbr>'.$nopo.'</inpponbr>'.
                                             '</checkPORev>'.
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

                            $dataloop    = $xmlResp->xpath('//ns1:tempRow');
                            $qdocResultRev = (string) $xmlResp->xpath('//ns1:outOK')[0];

                            if($qdocResultRev == 'true'){
                              foreach($dataloop as $datarev){
                                $nbrflg = $datarev->t_rev;
                              }
                              $nbrflg += 1;
                            }

                            
                            // Var Qxtend
                            $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
                            
                            $timeout        = 0;

                            $supplier = DB::table('supp_mstrs')
                                          ->where('supp_mstrs.supp_site','=',Session::get('site'))
                                          ->first();

                            $povend = '101';
                            $socust = ''; // taro di pocontract buat jdi customer di so dnp
                            if($supplier){
                                $socust = $supplier->supp_code;
                            }

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
                                              <poContract>'.$socust.'</poContract>
                                              <revChange>false</revChange>
                                              <reopenlines>true</reopenlines>
                                              <poRev>'.$nbrflg.'</poRev>
                                              <lineDetail>
                                                <yn>true</yn>
                                                <yn1>true</yn1>
                                                <podSite>'.Session::get('site').'</podSite>
                                                <podPart>'.$req->barang[$barangke].'</podPart>
                                                <podQtyOrd>'.$qtypo.'</podQtyOrd>
                                              </lineDetail>
                                            </purchaseOrder>
                                          </dsPurchaseOrder>';

                            $qdocfoot = '</maintainPurchaseOrder>
                                            </soapenv:Body>
                                            </soapenv:Envelope>';

                            $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;
                            
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
                            
                            //dd($qdocResponse,$qdocResult);

                            if($qdocResult=="success" OR $qdocResult=="warning")
                            {   
                              //dd('ok');
                                Log::channel('customlog')->info('PO : '.$nopo.' Updated, Item : '.$req->barang[$barangke].', Qty : '.$qtypo.'-'.Session::get('username'));
                            }else{
                                Log::channel('customlog')->info('PO : '.$nopo.' Failed, Item : '.$req->barang[$barangke].', Qty : '.$qtypo.'-'.Session::get('username'));
                            }
                        } 
                      }

                      $barangke += 1;
                }

            }
        }

        
        // ------------------------ Qxtend
            // Variable Web
            $flg = 0;
            $line = 1;
            $data = DB::table('so_mstrs')
                  ->where('so_site','=',Session::get('site'))
                  ->whereRaw('year(created_at) = "'.Carbon::now()->format('Y').'"')
                  ->count();

            $datasite = DB::table('site_mstrs')
                        ->where('site_code','=',Session::get('site'))
                        ->first();

            $site = substr(Session::get('site'),0,2); // Ambil 2 Digit Site Pertama

            //$rn = str_pad($data + 1, 5, '0', STR_PAD_LEFT); // Running Number dari total SO per Site
            $prefix = substr($datasite->r_nbr_so, 0, 2);
            $rn    = substr($datasite->r_nbr_so, 2, 5);
            
            if($prefix != Carbon::now()->format('y')){
                // Ganti bulan reset bulan & rn
                $prefix = Carbon::now()->format('y');
                $rn    = '00001';
            }else{
                $rn += 1;
                $rn = str_pad($rn , 5, '0', STR_PAD_LEFT);
            }


            $year = substr(Carbon::now()->format('Y'),3); // digit terakhir Tahun

            $noso = $site.$year.$rn;


            // Validasi WSA --> 02/19/2021 --> cek apakah di qad sudah ada nomor so tersebut
            $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
            $qxReceiver     = '';
            $qxSuppRes      = 'false';
            $qxScopeTrx     = '';
            $qdocName       = '';
            $qdocVersion    = '';
            $dsName         = '';
            
            $timeout        = 0;

            $domain         = 'DKH';

            // ** Edit here
            $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                              '<Body>'.
                              '<checkExistingSO xmlns="urn:iris.co.id:wsatrain">'.
                              '<inpdomain>'.$domain.'</inpdomain>'.
                              '<inpsonbr>'.$noso.'</inpsonbr>'.
                              '</checkExistingSO>'.
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

            if(strpos($qdocResponse, '<SOAP-ENV:Fault>') !== false){
                session()->flash('error','Terdapat Masalah dengan WSA, Mohon dicoba lagi. ');
                return back();
            }

            $qdocResult1 = (string) $xmlResp->xpath('//ns1:outOK')[0];

            // dd($qdocResult1,$qdocResponse);

            if($qdocResult1 == 'true'){
                session()->flash('error','SO Number '.$noso.' sudah terdapat pada QAD, Mohon dicek ');
                return back();
            }

            
            // Var Qxtend
            $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
            
            $timeout        = 0;

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
                                <maintainSalesOrder>
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
                                      <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

            $qdocbody = '<dsSalesOrder>
                            <salesOrder>
                                <soNbr>'.$noso.'</soNbr>
                                <soCust>'.$req->custcode.'</soCust>
                                <soShip>'.$req->shipto.'</soShip>
                                <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>
                                <soDueDate>'.$req->duedate.'</soDueDate>
                                <soPo>'.$req->po.'</soPo>
                                <soDetailAll>true</soDetailAll>';

                                foreach($req->barang as $barang){
                                        $qdocbody .=    '<salesOrderDetail>'.
                                                                '<line>'.$line.'</line>'.
                                                                '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                                '<sodQtyOrd>'.$req->jumlah[$flg].'</sodQtyOrd>'.
                                                                '<sodQtyAll>'.$req->jumlah[$flg].'</sodQtyAll>'.
                                                                //'<sodUm>'.$req->um[$flg].'</sodUm>'.
                                                                //'<sodDetailAll>true</sodDetailAll>'.
                                                                //'<allocationDetail>'.
                                                                //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                                //    '<ladQtyAll>'.$req->jumlah[$flg].'</ladQtyAll>'.
                                                                //'</allocationDetail>'.                                
                                                        '</salesOrderDetail>';
                                        $flg += 1;
                                        $line += 1;
                                }
                                                                    
                                $qdocbody .=   '</salesOrder>
                                                </dsSalesOrder>';

            $qdocfoot = '</maintainSalesOrder>
                            </soapenv:Body>
                         </soapenv:Envelope>';

            $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

            
            // catet xml pas create
            Log::channel('xmllog')->info($qdocbody);

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
		  
            //dd($qdocResult,$qdocResponse);
		        if($qdocResult=="success" OR $qdocResult=="warning")
            {
                    // update 20112020
                    $qty = '';
                    $price = '';
                    $disc = 0;
                    $discdet = 0;
                    $pricelist = 0;
                    $total = 0;
                    $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
                    
                    // pisahin hasil balikan dari qxtend
                    foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
                        if(str_contains($data,'P: ')){
                          $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
                        }elseif(str_contains($data,'Q: ')){
                          $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
                        }elseif(str_contains($data, 'D: ')){
                          $disc = substr($data, stripos($data, 'D:') + 3). ',';
                        }elseif(str_contains($data, 'C: ')){
                          $discdet .= substr($data, stripos($data, 'C:') + 3). ',';
                        }elseif(str_contains($data, 'X: ')){
                          $pricelist .= substr($data, stripos($data, 'X:') + 3). ',';
                        }
                    }
                    
                    $price = explode(',', substr($price, 0, -1));
                    $qty   = explode(',', substr($qty, 0, -1));
                    $discdet = explode(',', substr($discdet, 0, -1));
                    $pricelist = explode(',', substr($pricelist, 0, -1));
                    $disc  = substr($disc, 0, -1);
                    $flg = 0;


                    

                    if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
                      // Qxtend Tidak terima harga / disc / qty

                      Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from .p for SO Number : '.$noso.'-'.Session::get('username'));

                      // Delete trus bkin ulang.
                      // -- Delete
                        $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
              
                        $timeout        = 0;

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
                                            <maintainSalesOrder>
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
                                                  <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

                        $qdocbody = '<dsSalesOrder>
                                        <salesOrder>
                                            <operation>R</operation>
                                            <soNbr>'.$noso.'</soNbr>       
                                        </salesOrder>
                                    </dsSalesOrder>';

                        $qdocfoot = '</maintainSalesOrder>
                                        </soapenv:Body>
                                     </soapenv:Envelope>';

                        $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

                        if($qdocResult=="success" OR $qdocResult=="warning"){
                          // -- Create Ulang
                          $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
              
                          $timeout        = 0;
                          $flg = 0;
            			        $line = 1;

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
                                              <maintainSalesOrder>
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
                                                    <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

                          $qdocbody = '<dsSalesOrder>
                                          <salesOrder>
                                              <soNbr>'.$noso.'</soNbr>
                                              <soCust>'.$req->custcode.'</soCust>
                                              <soShip>'.$req->shipto.'</soShip>
                                              <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>
                                              <soDueDate>'.$req->duedate.'</soDueDate>
                                              <soPo>'.$req->po.'</soPo>
                                              <soDetailAll>true</soDetailAll>';

                                              foreach($req->barang as $barang){
                                                      $qdocbody .=    '<salesOrderDetail>'.
                                                                              '<line>'.$line.'</line>'.
                                                                              '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                                              '<sodQtyOrd>'.$req->jumlah[$flg].'</sodQtyOrd>'.
                                                                              '<sodQtyAll>'.$req->jumlah[$flg].'</sodQtyAll>'.
                                                                              //'<sodUm>'.$req->um[$flg].'</sodUm>'.
                                                                              //'<sodDetailAll>true</sodDetailAll>'.
                                                                              //'<allocationDetail>'.
                                                                              //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                                              //    '<ladQtyAll>'.$req->jumlah[$flg].'</ladQtyAll>'.
                                                                              //'</allocationDetail>'.                                
                                                                      '</salesOrderDetail>';
                                                      $flg += 1;
                                                      $line += 1;
                                              }
                                                                                  
                                              $qdocbody .=   '</salesOrder>
                                                              </dsSalesOrder>';

                          $qdocfoot = '</maintainSalesOrder>
                                          </soapenv:Body>
                                       </soapenv:Envelope>';

                          $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

                          // catet xml pas create
                          Log::channel('xmllog')->info($qdocbody.' -- Create Ulang --');

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
                          $qdocResultnew = (string) $xmlResp->xpath('//ns1:result')[0];

                          if($qdocResult=="success" OR $qdocResult=="warning"){
                            $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
                    
                            // pisahin hasil balikan dari qxtend
                            $flgtmp = 0;
                            foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
                                if(str_contains($data,'P: ')){
                                  $price[$flgtmp] .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
                                }elseif(str_contains($data,'Q: ')){
                                  $qty[$flgtmp] .= substr($data, stripos($data, 'Q:') + 3). ',';
                                }elseif(str_contains($data, 'D: ')){
                                  $disc = substr($data, stripos($data, 'D:') + 3). ',';
                                }elseif(str_contains($data, 'C: ')){
                                  $discdet[$flgtmp] .= substr($data, stripos($data, 'C:') + 3). ',';
                                }elseif(str_contains($data, 'X: ')){
                                  $pricelist[$flgtmp] .= substr($data, stripos($data, 'X:') + 3). ',';
                                }
                            }

                            $price = explode(',', substr($price[0], 0, -1));
                            $qty   = explode(',', substr($qty[0], 0, -1));
                            $discdet = explode(',', substr($discdet[0], 0, -1));
                            $pricelist = explode(',', substr($pricelist[0], 0, -1));
                            $disc  = substr($disc, 0, -1);

                            Log::channel('solog')->info('Recreate SO Succesfully '.$noso.'-'.Session::get('username'));

                          }

                        }
                    }

                    if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
                      // Masi Error --> Delete Kirim Error
                        $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
              
                        $timeout        = 0;

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
                                            <maintainSalesOrder>
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
                                                  <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

                        $qdocbody = '<dsSalesOrder>
                                        <salesOrder>
                                            <operation>R</operation>
                                            <soNbr>'.$noso.'</soNbr>       
                                        </salesOrder>
                                    </dsSalesOrder>';

                        $qdocfoot = '</maintainSalesOrder>
                                        </soapenv:Body>
                                     </soapenv:Envelope>';

                        $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

                        if($qdocResult=="success" OR $qdocResult=="warning"){
                          Log::channel('customlog')->info('Failed to create SO 2x (.p error) -- Send Error '.Session::get('username'));
                              Session()->flash('error','Terdapat Error, Data Gagal tersimpan');
                              return back();
                          }   
                    }

                    //dd($price,$discdet,$pricelist,$qdocResponse);

                    // Update RN SO
                    
                    db::table('site_mstrs')
                                ->where('site_code','=',Session::get('site'))
                                ->update([
                                 'r_nbr_so' => $prefix.$rn  
                                ]);

                    Log::channel('solog')->info('Prefix SO Succesfully Created '.$noso.'-'.Session::get('username'));

                    // Insert SO QAD ke web
                    DB::table('so_mstrs')
                            ->insert([
                                'so_nbr' => $noso,
                                'so_cust' => $req->custcode,
                                'so_duedate' => $req->duedate,
                                'so_shipto' => $req->shipto,
                                'so_notes' => $req->notes,
                                'so_user' => Session::get('username'),
                                'so_status' => 1, // 1 New SO , 2 On Hold
                                'so_site' => Session::get('site'), // Cek Session User
                                //'so_price' => $total, // Cari Harga update di Qxtend, liat latian hari 3,
                                'so_po' => $req->po,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString(),
                            ]);

                    Log::channel('solog')->info('SO Mstr Succesfully Created '.$noso.'-'.Session::get('username'));

                    $flgweb = 0;
                    $lineweb = 1;
                    foreach($req->barang as $barang){
                        $cekum = DB::table('items')
                                    ->where('items.itemcode','=',$req->barang[$flgweb])
                                    ->first();

                        DB::table('so_dets')
                                ->insert([
                                    'so_nbr' => $noso,
                                    'so_line' => $lineweb,
                                    'so_itemcode' => $req->barang[$flgweb],
                                    'so_qty' => $req->jumlah[$flgweb],
                                    'so_qty_open' => $req->jumlah[$flgweb], // jika 0 bkin statusny closed
                                    //'so_um' => $req->um[$flgweb],
                                    'so_um' => $cekum->item_um,
                                    'so_harga' => trim($price[$flgweb]),
                                    'so_disc' => trim($discdet[$flgweb]),
                                    'so_pr_list' => trim($pricelist[$flgweb]),
                                    'so_status' => 1, // 1 New SO, 2 On Hold, 3 Habis
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);

                        $flgweb += 1;
                        $lineweb += 1;
                    }

                    Log::channel('solog')->info('SO Det Succesfully Created '.$noso.'-'.Session::get('username'));



                    // itung total harga nett line
                    $flgprice = 0;
                    foreach($price as $s){
                      $total += trim($price[$flgprice]) * trim($qty[$flgprice]);
                      $flgprice += 1;
                    }

                    // itung total nett disc master
                    if($disc != 0){
                      $total = $total - $total * $disc / 100;
                    }

                    // Data berhasil terbuat di QAD               
                    $so_nbr = (string) $xmlResp->xpath('//ns1:soNbr')[0];
                    
                    DB::table('so_mstrs')
                            ->where('so_mstrs.so_nbr','=',$noso)
                            ->update([
                                  'so_price' => $total,
                            ]);

                    Log::channel('solog')->info('SO Mstr Price Succesfully Updated '.$noso.'-'.Session::get('username'));


                    // WSA ke qad cek apakah status PO Hold dari QAD
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

                      // ** Edit here
                      $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                        '<Body>'.
                                        '<statusSO xmlns="urn:iris.co.id:wsatrain">'.
                                        '<inpdomain>'.$domain.'</inpdomain>'.
                                        '<insonbr>'.$so_nbr.'</insonbr>'.
                                        '</statusSO>'.
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
                      $status = '';
                      $flag = 0;

                      $dataloop = $xmlResp->xpath('//ns1:tempRow');
                      foreach($dataloop as $data) { 
                          //$status  = (string) $xmlResp->xpath('//ns1:t_status')[$flag]; // jumlah qty oh
                          $status = $data->t_status;
                          $flag += 1;
                      }

                      if($status != ''){
                          /* Bkin tombol confirm
                          $data = DB::table('so_mstrs')
                                      ->join('approvals','so_mstrs.so_site','=','approvals.site_app')
                                      ->where('so_nbr','=',$so_nbr)
                                      ->orderBy('order','asc')
                                      ->get();

                          if(!$data->isEmpty()){
                              foreach($data as $data){
                                DB::Table('approval_tmp')
                                        ->insert([
                                              'so_nbr' => $data->so_nbr,
                                              'approval_approver' => $data->userid,
                                              'approval_seq' => $data->order,
                                              'created_at' => Carbon::now()->toDateTimeString()
                                        ]);
                              }
                          }*/

                          DB::table('so_mstrs')
                            ->where('so_nbr','=',$so_nbr)
                            ->update([
                                'so_status' => '3' // Hold dari QAD
                            ]);
                      }


                    session()->flash('updated','Data Berhasil Disimpan, No SO : '.$so_nbr);
                    return back();
            }else{
                // Error data tidak masuk QAD
                $resultProcess  = false;
                $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
                $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
                $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
                $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


                //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev,$qdocResult);
                Log::channel('customlog')->info('Failed to create SO , error :'.$qdocMsgDesc.'--'.$qdocMsgData.'--'.$qdocMsgSev.'-'.Session::get('username'));
                Session()->flash('error','Terdapat Error, Data Gagal tersimpan');
                return back();
            }
    }

    public function editdetail(Request $req){
      if($req->ajax()){
            $data = DB::table('so_mstrs')
                    ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
                    ->join('items','items.itemcode','=','so_dets.so_itemcode')
                    ->where('so_mstrs.so_nbr','=',$req->sonbr)
                    ->get();

            $output = '';
            $qtyship = 0;
            $qtycan = 0;
            foreach($data as $data){
                $qtyship = DB::table('dod_det')
                            ->where('dod_so','=',$req->sonbr)
                            ->where('dod_part','=',$data->so_itemcode)
                            ->where('dod_line','=',$data->so_line)
                            ->where('dod_status','!=','3')
                            ->sum('dod_qty');
                
                $qtycan = $data->so_qty - $qtyship;

                $output .=  '<tr>'.
                            '<td> <input type="text" class="form-control" value="'.$data->so_itemcode.' - '.$data->itemdesc.'" readonly> </td>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_itemcode.'" name="itemcode[]" readonly>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_line.'" name="line[]" readonly>'.
                            '<td> <input type="number" min="'.$qtyship.'" class="form-control qtyso" value="'.$data->so_qty.'" name="qtyso[]"> </td>'.
                            '<td> <input type="number" class="form-control" value="'.$qtyship.'" name="qtyship[]" readonly> </td>'.
                            '<td> <input type="text" class="form-control" value="'.$data->so_um.'" name="um[]" readonly> </td>'.
                            '<td> <input type="text" class="form-control" value="'.$data->item_location.'" name="loc[]" readonly> </td>';
                if($qtyship > 0){
                  $output .=  '<td style="vertical-align:middle;text-align:center;"> <input type="checkbox" class="qaddel" value="" disabled> </td>';
                }else{
                  $output .=  '<td style="vertical-align:middle;text-align:center;"> <input type="checkbox" class="qaddel" value=""> </td>';
                }
                
                            
                $output .=  '<input type="hidden" name="delLine[]" class="defdel" value="M">'.
                            '<tr>';
            
            }

            return response($output);
      }
    }

    public function editsalesorder(Request $req){
      //dd($req->all());
      // Var Qxtend
            $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
            
            $timeout        = 0;
            $flg            = 0;
            $line           = 1;
            $newline        = intval(array_values(array_slice($req->line, -1))[0]);

            // XML Qxtend
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
                                <maintainSalesOrder>
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
                                      <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

            $qdocbody = '<dsSalesOrder>
                            <salesOrder>
                                <soNbr>'.$req->ed_sonbr.'</soNbr>';
                                if($req->ed_shipto != null){
                                // buat ilangin error
                                $qdocbody .= '<soShip>'.$req->ed_shipto.'</soShip>';
                                }
            $qdocbody .=        '<soDueDate>'.$req->ed_duedate.'</soDueDate>
                                <soPo>'.$req->ed_po.'</soPo>';

                                foreach($req->itemcode as $barang){
                                    if($req->qtyso[$flg] >= $req->qtyship[$flg]){
                                        if($req->delLine[$flg] == 'A'){
                                          $newline += 1;

                                          $qdocbody .=    '<salesOrderDetail>'.
                                                                    '<operation>'.$req->delLine[$flg].'</operation>'.
                                                                    '<line>'. $newline  .'</line>'.
                                                                    '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                                    '<sodQtyOrd>'.$req->qtyso[$flg].'</sodQtyOrd>'.   
                                                                    '<sodQtyAll>'.$req->qtyso[$flg].'</sodQtyAll>'.
                                                                    //'<allocationDetail>'.
                                                                    //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                                    //    '<ladQtyAll>'.$req->qtyso[$flg].'</ladQtyAll>'.
                                                                    //'</allocationDetail>'.    
                                                            '</salesOrderDetail>';

                                        }else{
                                          // ambil total ship
                                          $qtyship = DB::table('dod_det')
                                                        ->where('dod_so','=',$req->ed_sonbr)
                                                        ->where('dod_line','=',$req->line[$flg])
                                                        ->where('dod_part','=',$req->itemcode[$flg])
                                                        ->selectRaw('SUM(dod_qty) as totalship')
                                                        ->groupBy('dod_so')
                                                        ->first();
                                          $qtyall = 0;

                                          if($qtyship){
                                            // ambil qty old
                                            $oldqty = DB::table('so_dets')
                                                      ->where('so_dets.so_nbr','=',$req->ed_sonbr)
                                                      ->where('so_line','=',$req->line[$flg])
                                                      ->where('so_itemcode','=',$req->itemcode[$flg])
                                                      ->first();
                                            if($oldqty){
                                              if($req->qtyso[$flg] > $qtyship->totalship){
                                                $qtyall = $req->qtyso[$flg] - $oldqty->so_qty + $oldqty->so_qty_open;
                                              }
                                            }
                                          }else{
                                            $qtyall = $req->qtyso[$flg];
                                          }

                                          $qtyopen = DB::table('so_dets')
                                                      ->where('so_dets.so_nbr','=',$req->ed_sonbr)
                                                      ->where('so_line','=',$req->line[$flg])
                                                      ->where('so_itemcode','=',$req->itemcode[$flg])
                                                      ->first();

                                          if($qtyopen->so_qty_open != '0'){

                                            $qdocbody .=    '<salesOrderDetail>'.
                                                                      '<operation>'.$req->delLine[$flg].'</operation>'.
                                                                      '<line>'.$req->line[$flg].'</line>'.
                                                                      '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                                      '<sodQtyOrd>'.$req->qtyso[$flg].'</sodQtyOrd>'.  
                                                                      '<sodQtyAll>'.$qtyall.'</sodQtyAll>'.
                                                                      //'<allocationDetail>'.
                                                                      //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                                      //    '<ladQtyAll>'.$req->qtyso[$flg].'</ladQtyAll>'.
                                                                      //'</allocationDetail>'.    
                                                              '</salesOrderDetail>';                                            
                                          }


                                        }
                                    }
                                        $flg += 1;
                                }
                                  
                                  
                                $qdocbody .=   '</salesOrder>
                                                </dsSalesOrder>';

            $qdocfoot = '</maintainSalesOrder>
                            </soapenv:Body>
                         </soapenv:Envelope>';

            $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

            //dd($qdocResponse,$qdocRequest);

            $xmlResp = simplexml_load_string($qdocResponse);
            $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');
            $qdocResult = (string) $xmlResp->xpath('//ns1:result')[0];          

            if($qdocResult=="success" OR $qdocResult=="warning")
            {
              $qty = '';
              $price = '';
              $disc = 0;
              $total = 0;
              $discdet = 0;
              $pricelist = 0;
              $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
              
              // pisahin hasil balikan dari qxtend
              foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
                  if(str_contains($data,'P: ')){
                    $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
                  }elseif(str_contains($data,'Q: ')){
                    $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
                  }elseif(str_contains($data, 'D: ')){
                    $disc = substr($data, stripos($data, 'D:') + 3). ',';
                  }elseif(str_contains($data, 'C: ')){
                    $discdet .= substr($data, stripos($data, 'C:') + 3). ',';
                  }elseif(str_contains($data, 'X: ')){
                    $pricelist .= substr($data, stripos($data, 'X:') + 3). ',';
                  }
              }

              $price     = explode(',', substr($price, 0, -1));
              $qty       = explode(',', substr($qty, 0, -1));
              $discdet   = explode(',', substr($discdet, 0, -1));
              $pricelist   = explode(',', substr($pricelist, 0, -1));
              $disc      = substr($disc, 0, -1);
              $flg       = 0;

              //dd($qdocResponse,$price,$qty,$discdet,$req->all(),$price[0],$qty[0],$discdet[0]);

              if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
	                // Qxtend Tidak terima harga / disc / qty

	                Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from .p for SO Number : '.$req->ed_sonbr.'-'.Session::get('username'));

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
	                    '<insonbr>'.$req->ed_sonbr.'</insonbr>'.
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

	                $dataloop    = $xmlResp->xpath('//ns1:tempRow');
	                $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
	                
	                if ($qdocResultx == 'true')  {
	                  $flgarr = 0;
	                  foreach($dataloop as $data){
	                    $price[$flgarr] = $data->t_netprice;
	                    $discdet[$flgarr] = $data->t_discdet;
	                    $qty[$flgarr] = $data->t_qtyord;
	                    $pricelist[$flgarr] = $data->t_pricelist;
	                    $disc = $data->t_dischead;
	                  
	                    $flgarr += 1;
	                  }
	                }else{
	                  Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from WSA for SO Number : '.$req->ed_sonbr.'-'.Session::get('username'));
	                }
              }

              if($price[0] != '' and $qty[0] != '' and $discdet[0] != ''){
	                // itung total harga nett line
	                foreach($price as $s){
	                  $total += trim($price[$flg]) * trim($qty[$flg]);
	                  $flg += 1;
	                }

	                // itung total nett disc master
	                if($disc != 0){
	                  $total = $total - $total * $disc / 100;
	                }

	                // Insert harga baru ke master
	                if($req->ed_shipto == null){
	                  DB::table('so_mstrs')
	                      ->where('so_nbr','=',$req->ed_sonbr)
	                      ->update([
	                            'so_price' => $total,
	                            'so_duedate' => $req->ed_duedate,
	                            'so_po' => $req->ed_po,
	                      ]);
	                }else{
	                  DB::table('so_mstrs')
	                      ->where('so_nbr','=',$req->ed_sonbr)
	                      ->update([
	                            'so_price' => $total,
	                            'so_shipto' => $req->ed_shipto,
	                            'so_duedate' => $req->ed_duedate,
	                            'so_po' => $req->ed_po,
	                      ]);
	                }
              }

              // update data detail
              $flgweb = 0;
              $flgprog = 0;
              $lineweb = array_values(array_slice($req->line, -1))[0]; // line terakhir web
              foreach($req->itemcode as $barang){

                  if($req->delLine[$flgweb] == 'R'){
	                    // hapus row
	                    DB::table('so_dets')
	                          ->where('so_nbr','=',$req->ed_sonbr)
	                          ->where('so_itemcode','=',$req->itemcode[$flgweb])
	                          ->where('so_line','=',$req->line[$flgweb])
	                          ->delete();

	                    $checkopen = DB::table('so_mstrs')
	                                  ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
	                                  ->where('so_dets.so_qty_open','!=','0')
	                                  ->where('so_mstrs.so_nbr','=',$req->ed_sonbr)
	                                  ->first();
	                    if(!$checkopen){
	                        DB::table('so_mstrs')
	                                ->where('so_nbr','=',$req->ed_sonbr)
	                                ->update([
	                                    'so_status' => '6'
	                                ]);
	                    }

                  }elseif($req->delLine[$flgweb] == 'A'){

                    	$users = db::table('site_mstrs')
                          ->where('site_code','=',Session::get('site'))
                          ->where('site_flag','=','N') // Tidak punya WH
                          ->first();

                        if($users){
                          // Site tidak ada WH
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
                                           '<inpart>'.$req->itemcode[$flgweb].'</inpart>'.
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

                          
                          $flag = 0;
                          
                          $item    = '';
                          $qty     = 0;
                          $qty_all = 0;
                          $qtypo  = 0;

                          $dataloop    = $xmlResp->xpath('//ns1:tempRow');
                          $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
                          //dd($qdocResultx);
                          if ($qdocResultx == 'true')  {
                            foreach($dataloop as $data) {
                                /* 
                                $item = (string) $xmlResp->xpath('//ns1:t_part')[$flag]; // nama item akan kosong jika tidak ketemu di qad.
                                $qty  += (string) $xmlResp->xpath('//ns1:t_qty')[$flag]; // jumlah qty oh
                                $qty_all += (string) $xmlResp->xpath('//ns1:t_qty_all')[$flag];*/

                                $item = $data->t_part;
                                $qty += $data->t_qty;
                                $qty_all += $data->t_qty_all;
                                $flag += 1;
                            }

                            $qtysisa = $qty - $qty_all;
                            if($qtysisa <= 0){
                              $qtypo = $req->qtyso[$flgweb];
                            }else{
                              $qtypo = $req->qtyso[$flgweb] + $qty_all - $qty;
                            }

                            //dd($item,$req->qtyso[$flgweb],$qtysisa);


                            if($item != '' && $req->qtyso[$flgweb] > $qtysisa){
                                // Barang Dipesan > Stok OH --> Bkin PO ke QAD DNP

                                // $nbrflg = DB::table('so_mstrs')
                                //               ->where('so_site','=',Session::get('site'))
                                //               ->whereRaw('Date(created_at) = "'.Carbon::today().'"')
                                //               ->count();

                                // $nbrflg += 1;

                                $nbrflg = 0;

                                $nopo  = "Q".substr(Session::get('site'), 0,2).substr(Carbon::now()->format('Y'),3).Carbon::now()->format('md');

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
                                                 '<checkPORev xmlns="urn:iris.co.id:wsatrain">'.
                                                 '<inpdomain>'.$domain.'</inpdomain>'.
                                                 '<inpponbr>'.$nopo.'</inpponbr>'.
                                                 '</checkPORev>'.
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

                                $dataloop    = $xmlResp->xpath('//ns1:tempRow');
                                $qdocResultRev = (string) $xmlResp->xpath('//ns1:outOK')[0];

                                if($qdocResultRev == 'true'){
                                  foreach($dataloop as $datarev){
                                    $nbrflg = $datarev->t_rev;
                                  }
                                  $nbrflg += 1;
                                }
                                
                                // Var Qxtend
                                $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
                                
                                $timeout        = 0;

                                $supplier = DB::table('supp_mstrs')
                                              ->where('supp_mstrs.supp_site','=',Session::get('site'))
                                              ->first();

                                $povend = '101';
                                $socust = ''; // taro di pocontract buat jdi customer di so dnp
                                if($supplier){
                                    $socust = $supplier->supp_code;
                                }

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
                                                  <poContract>'.$socust.'</poContract>
                                                  <revChange>false</revChange>
                                                  <reopenlines>true</reopenlines>
                                                  <poRev>'.$nbrflg.'</poRev>
                                                  <lineDetail>
                                                    <yn>true</yn>
                                                    <yn1>true</yn1>
                                                    <podSite>'.Session::get('site').'</podSite>
                                                    <podPart>'.$req->itemcode[$flgweb].'</podPart>
                                                    <podQtyOrd>'.$qtypo.'</podQtyOrd>
                                                  </lineDetail>
                                                </purchaseOrder>
                                              </dsPurchaseOrder>';

                                $qdocfoot = '</maintainPurchaseOrder>
                                                </soapenv:Body>
                                                </soapenv:Envelope>';

                                $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;
                                
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
                                
                                //dd($qdocResponse,$qdocResult);

                                if($qdocResult=="success" OR $qdocResult=="warning")
                                {   
                                  //dd('ok');
                                    Log::channel('customlog')->info('PO : '.$nopo.' Updated, Item : '.$req->itemcode[$flgweb].', Qty : '.$qtypo.'-'.Session::get('username'));
                                }else{
                                    Log::channel('customlog')->info('PO : '.$nopo.' Failed, Item : '.$req->itemcode[$flgweb].', Qty : '.$qtypo.'-'.Session::get('username'));
                                }
                            } 
                          }
                        }
                        
                        if($price[0] != '' and $discdet[0] != ''){
                        $cekum = DB::table('items')
                                    ->where('items.itemcode','=',$req->itemcode[$flgweb])
                                    ->first();

                          DB::table('so_dets')
                              ->insert([
                                  'so_nbr' => $req->ed_sonbr,
                                  'so_itemcode' => $req->itemcode[$flgweb],
                                  'so_line' => $lineweb + 1,
                                  'so_qty' => $req->qtyso[$flgweb],
                                  'so_qty_open' => $req->qtyso[$flgweb],
                                  //'so_um' => $req->um[$flgweb],
                                  'so_um' => $cekum->item_um,
                                  'so_harga' => trim($price[$flgprog]), 
                                  'so_disc' => trim($discdet[$flgprog]),
                                  'so_pr_list' => trim($pricelist[$flgprog]),
                                  'so_status' => '1',
                                  'created_at' => Carbon::now()->toDateTimeString(),
                                  'updated_at' => Carbon::now()->toDateTimeString()
                              ]);

                          $lineweb += 1;
                          $flgprog += 1;

                          DB::table('so_mstrs')
                                  ->where('so_nbr','=',$req->ed_sonbr)
                                  ->update([
                                      'so_status' => '1'
                                  ]);
                        }

                  }else{
                  		if($req->qtyso[$flgweb] >= $req->qtyship[$flgweb]){

	                        $users = db::table('site_mstrs')
	                              ->where('site_code','=',Session::get('site'))
	                              ->where('site_flag','=','N') // Tidak punya WH
	                              ->first();

	                        if($users){

	                          	$data = DB::table('so_dets')
	                                    ->where('so_nbr','=',$req->ed_sonbr)
	                                    ->where('so_itemcode','=',$req->itemcode[$flgweb])
	                                    ->where('so_line','=',$req->line[$flgweb])
	                                    ->first();
	                            if($data){
	                              // ada datanya
	                              if($data->so_qty < $req->qtyso[$flgweb]){
	                                
	                                // $nbrflg = DB::table('so_mstrs')
	                                //                 ->where('so_site','=',Session::get('site'))
	                                //                 ->whereRaw('Date(created_at) = "'.Carbon::today().'"')
	                                //                 ->count();

	                                // $nbrflg += 1;

	                                $nbrflg = 0;

	                                $nopo  = "Q".substr(Session::get('site'), 0,2).substr(Carbon::now()->format('Y'),3).Carbon::now()->format('md');

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
	                                                 '<checkPORev xmlns="urn:iris.co.id:wsatrain">'.
	                                                 '<inpdomain>'.$domain.'</inpdomain>'.
	                                                 '<inpponbr>'.$nopo.'</inpponbr>'.
	                                                 '</checkPORev>'.
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

	                                $dataloop    = $xmlResp->xpath('//ns1:tempRow');
	                                $qdocResultRev = (string) $xmlResp->xpath('//ns1:outOK')[0];

	                                if($qdocResultRev == 'true'){
	                                  foreach($dataloop as $datarev){
	                                    $nbrflg = $datarev->t_rev;
	                                  }
	                                  $nbrflg += 1;
	                                }

	                                $qtypesan = $req->qtyso[$flgweb] - $data->so_qty;
	                                // Var Qxtend
	                                $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
	                                
	                                $timeout        = 0;

	                                $supplier = DB::table('supp_mstrs')
	                                              ->where('supp_mstrs.supp_site','=',Session::get('site'))
	                                              ->first();

	                                $povend = '101';
	                                $socust = ''; // taro di pocontract buat jdi customer di so dnp
	                                if($supplier){
	                                    $socust = $supplier->supp_code;
	                                }

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
	                                                  <poContract>'.$socust.'</poContract>
	                                                  <revChange>false</revChange>
	                                                  <reopenlines>true</reopenlines>
	                                                  <poRev>'.$nbrflg.'</poRev>
	                                                  <lineDetail>
	                                                    <yn>true</yn>
	                                                    <yn1>true</yn1>
	                                                    <podSite>'.Session::get('site').'</podSite>
	                                                    <podPart>'.$req->itemcode[$flgweb].'</podPart>
	                                                    <podQtyOrd>'.$qtypesan.'</podQtyOrd>
	                                                  </lineDetail>
	                                                </purchaseOrder>
	                                              </dsPurchaseOrder>';

	                                $qdocfoot = '</maintainPurchaseOrder>
	                                                </soapenv:Body>
	                                                </soapenv:Envelope>';

	                                $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;
	                                
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

	                                //dd($nbrflg,$qdocResponse,$qdocRequest);

	                                $xmlResp = simplexml_load_string($qdocResponse);
	                                $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');
	                                $qdocResult = (string) $xmlResp->xpath('//ns1:result')[0];           
	                                
	                                //dd($qdocResponse,$qdocResult);

	                                if($qdocResult=="success" OR $qdocResult=="warning")
	                                {   
	                                    Log::channel('customlog')->info('PO : '.$nopo.' Updated, Item : '.$req->itemcode[$flgweb].', Qty : '.$qtypesan.'-'.Session::get('username'));
	                                    //dd('ok');
	                                }else{
	                                    Log::channel('customlog')->info('PO : '.$nopo.' Failed, Item : '.$req->itemcode[$flgweb].', Qty : '.$qtypesan.'-'.Session::get('username'));
	                                    //dd('notok');
	                                }

	                              }
	                            }
	                        }

	                        // Qty Open Baru = Qty Baru - Qty Lama + Qty Open
	                        $qtybaru = 0;
	                        $datalama = DB::table('so_dets')
	                                      ->where('so_nbr','=',$req->ed_sonbr)
	                                      ->where('so_itemcode','=',$req->itemcode[$flgweb])
	                                      ->where('so_line','=',$req->line[$flgweb])
	                                      ->first();
	                        $qtybaru = $req->qtyso[$flgweb] - $datalama->so_qty + $datalama->so_qty_open;

	                        if($price[0] != '' and $qty[0] != '' and $discdet[0] != ''){
                              if($datalama->so_qty_open != 0){
                                DB::table('so_dets')
                                      ->where('so_nbr','=',$req->ed_sonbr)
                                      ->where('so_itemcode','=',$req->itemcode[$flgweb])
                                      ->where('so_line','=',$req->line[$flgweb])
                                      ->update([
                                        'so_qty' => $req->qtyso[$flgweb],
                                        'so_qty_open' => $qtybaru,
                                        'so_harga' => trim($price[$flgprog]),
                                        'so_disc' => trim($discdet[$flgprog]),
                                        'so_pr_list' => trim($pricelist[$flgprog]),
                                        'updated_at' => Carbon::now()->toDateTimeString()
                                      ]);
                                $flgprog += 1;
                              }
	                          

	                          $checkopen = DB::table('so_mstrs')
	                                        ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
	                                        ->where('so_dets.so_qty_open','!=','0')
	                                        ->where('so_mstrs.so_nbr','=',$req->ed_sonbr)
	                                        ->first();
	                          //dd($checkopen);
	                          if(!$checkopen){
	                              DB::table('so_mstrs')
	                                      ->where('so_nbr','=',$req->ed_sonbr)
	                                      ->update([
	                                          'so_status' => '6'
	                                      ]);
	                          }
	                        }

	                    }
                  
                  }

                  
                  $flgweb += 1;
              }


              // WSA ke qad cek apakah status PO Hold dari QAD
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

                    // ** Edit here
                    $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                      '<Body>'.
                                      '<statusSO xmlns="urn:iris.co.id:wsatrain">'.
                                      '<inpdomain>'.$domain.'</inpdomain>'.
                                      '<insonbr>'.$req->ed_sonbr.'</insonbr>'.
                                      '</statusSO>'.
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
                    $status = '';
                    $flag = 0;

                    $dataloop = $xmlResp->xpath('//ns1:tempRow'); 
                    foreach($dataloop as $data) { 
                        //$status  = (string) $xmlResp->xpath('//ns1:t_status')[$flag]; // jumlah qty oh
                        $status = $data->t_status;
                        $flag += 1;
                    }

                    $so = DB::table('so_mstrs')
                                ->where('so_nbr','=',$req->ed_sonbr)
                                ->first();
                    if($so->so_status != '6'){
                      if($status != ''){
                        // Status di QAD Hold
                        DB::table('so_mstrs')
                          ->where('so_nbr','=',$req->ed_sonbr)
                          ->update([
                              'so_status' => '3' // Hold dari QAD
                          ]);
                      }else{
                        DB::table('so_mstrs')
                            ->where('so_nbr','=',$req->ed_sonbr)
                            ->update([
                                'so_status' => '1' // Lepas Hold jdi Created di web
                            ]);
                      }
                    }            
              
              Log::channel('customlog')->info('SO Number :'.$req->ed_sonbr.' updated Succesfully -'.Session::get('username'));
              session()->flash('updated','SO updated in QAD');
              return back();
            }else{

                $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
                $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
                $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
                $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


                Log::channel('customlog')->info('SO Number :'.$req->ed_sonbr.' updated failed, Error Qxtend :'.$qdocMsgDesc.'-'.Session::get('username'));
                //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev);
              session()->flash('error','SO Cannot be updated, Please check QAD System');
              return back();
            }
    }

    public function deletesalesorder(Request $req){
      //dd($req->all());
      // Var Qxtend
            $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
            
            $timeout        = 0;

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
                                <maintainSalesOrder>
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
                                      <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

            $qdocbody = '<dsSalesOrder>
                            <salesOrder>
                                <operation>R</operation>
                                <soNbr>'.$req->de_sonbr.'</soNbr>       
                            </salesOrder>
                        </dsSalesOrder>';

            $qdocfoot = '</maintainSalesOrder>
                            </soapenv:Body>
                         </soapenv:Envelope>';

            $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

            dd($qdocResult);
           
            DB::table('so_mstrs')
                    ->where('so_nbr','=',$req->de_sonbr)
                    ->update([
                        'so_status' => '5' // Delete from QAD
                    ]);

            session()->flash('updated','SO deleted in QAD');
            return back();

            /*
            if($qdocResult=="success" OR $qdocResult=="warning")
            {
              DB::table('so_mstrs')
                    ->where('so_nbr','=',$req->de_sonbr)
                    ->update([
                        'so_status' => '5' // Delete from QAD
                    ]);

              session()->flash('updated','SO deleted in QAD, Please check QAD System');
              return back();
            }else{
              session()->flash('error','SO Cannot be deleted, Please check QAD System');
              return back();
            }*/
    }

    public function getumitem(Request $req){
      // dd($req->all());
      if($req->ajax()){
        $data =     DB::table('items')
                        ->where('itemcode','=',$req->item)
                        ->first();

        return response($data->item_um.'||'.$data->item_location);
      }
    }

    public function confirmso(Request $req){
        //dd($req->all());
        $data = DB::table('so_mstrs')
                    ->join('approvals','so_mstrs.so_site','=','approvals.site_app')
                    ->where('so_nbr','=',$req->ec_sonbr)
                    ->orderBy('order','asc')
                    ->get();

        if(!$data->isEmpty()){
            foreach($data as $data){
              DB::Table('approval_tmp')
                      ->insert([
                            'so_nbr' => $data->so_nbr,
                            'approval_approver' => $data->userid,
                            'approval_seq' => $data->order,
                            'created_at' => Carbon::now()->toDateTimeString()
                      ]);
            }
          
          DB::table('so_mstrs')
                    ->where('so_nbr','=',$req->ec_sonbr)
                    ->update([
                            'so_status' => 7 // 1 New 234 On Hold 5 Deleted 6 Rejected 7 Waiting for Approval
                    ]);    

            session()->flash('updated','Data Successfully sent to approval');
            return back();  
        }else{
            session()->flash('error','Mohon lengkapi data Approval untuk site '.Session::get('site'));
            return back();
        }
    }

    public function checkspb(Request $req){
      if($req->ajax()){
          $data = DB::table('do_mstr')
                    ->join('dod_det','do_mstr.do_nbr','=','dod_det.dod_nbr')
                    ->where('dod_det.dod_so','=',$req->sonbr)
                    ->where(function($query) use ($req) {
                          $query->where('do_status','=','1')
                                ->orwhere('do_status','=','4');
                    })
                    ->first();
          
          $output = '';
          if($data){
              $output = $data->dod_status;
          }

          echo $output;
          
      }
    }

    public function alamatcust(Request $req){
      if($req->ajax()){
        $data = DB::table('customers')
                ->where('cust_code','=',$req->cust)
                ->first();

        $output = '';
        if($data){
            $output = $data->cust_alamat;
        }

        return response($output);
      }
    }

    public function checkallspb(Request $req){
      if($req->ajax()){
        $data = DB::table('do_mstr')
                    ->join('dod_det','do_mstr.do_nbr','=','dod_det.dod_nbr')
                    ->where('dod_det.dod_so','=',$req->sonbr)
                    ->first();
          
          $output = '';
          if($data){
              $output = $data->dod_status;
          }

          echo $output;
      }
    }

    public function brelnamesearch(Request $req){
      if($req->ajax()){
        $data = DB::table('customers')
                ->where('cust_code','=',$req->cust)
                ->first();

        $output = '';
        if($data){
            $output = $data->cust_desc;
        }

        return response($output);
      }
    }

    // Retur QAD ** Not Used

    public function retur(){

    	return view('so.soretur');
    }

    public function detailretur(Request $req){
    	if($req->ajax()){
    		$data = DB::table('so_mstrs')
    					->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
    					->join('items','items.itemcode','=','so_dets.so_itemcode')
    					->where('so_mstrs.so_nbr','=',$req->sonbr)
    					->get();

            $output = '';   
    		
            if(!is_null($data)){
                foreach($data as $data){
                    $output .= '<tr>'.

                               '<td data-label="Item">'.$data->itemcode.' - '.$data->itemdesc.'</td>'.
                               '<td data-label="Qty">'.$data->so_qty.'</td>'.
                               '<td data-label="UM">'.$data->so_um.'</td>'.
                               '<td data-label="Qty Retur">
                                    <input type="number" min="0" class="form-control" max="'.$data->so_qty.'" value="0" name="qtyretur[]" required> </input>
                                    <input type="hidden" min="0" class="form-control" name="qtyso[]" value="'.$data->so_qty.'"> </input>
                                    <input type="hidden" min="0" class="form-control" name="itemcode[]" value="'.$data->itemcode.'"> </input>
                                    <input type="hidden" min="0" class="form-control" name="location[]" value="'.$data->item_location.'"> </input>
                               </td>'.


                               '</tr>';
                }

                return response($output);
    		}

    	}
    }

    public function alamatretur(Request $req){
    	if($req->ajax()){
    		// Data Search subquery
    		$data = DB::select("SELECT x.so_cust as 'cust', x.cust_desc as 'custdesc', x.so_shipto as 'shipto', customers.cust_desc as 'shiptodesc', customers.cust_alamat as'alamatshipto' FROM (select so_cust, cust_desc,so_shipto,so_nbr from so_mstrs join customers on so_cust = cust_code)x LEFT JOIN customers on x.so_shipto = customers.cust_code where so_nbr = '".$req->sonbr."'");
    			
			$output = '';

    		if($data){
    			foreach($data as $data){
    				$output .= $data->cust.' - '.$data->custdesc.'||'.$data->shipto.' - '.$data->shiptodesc.'||'.$data->alamatshipto.'||'.$data->cust.'||'.$data->shipto;
    			}
    		}

    		return response($output);
    	}
    }

    public function returqad(Request $req){
      //dd($req->all());
    	$flg = 0;

    	foreach($req->itemcode as $data){
    		if((int)$req->qtyretur[$flg] > (int)$req->qtyso[$flg]){
    			//dd('error'.$req->itemcode[$flg]);
    			Session()->flash('error','Qty Retur Item: '.$req->itemcode[$flg].' Max Retur : '.$req->qtyso[$flg].', Input : '.$req->qtyretur[$flg]);
    			return back();
    		}
    		$flg += 1;
    	}

      $qty = 0;
      foreach($req->qtyretur as $data){
          if($data > 0){
            $qty += 1;
          }
      }

      if($qty > 0){
          // ada data yang retur diatas 0 -> Lanjut Qxtend
          // Variable Web
            $flg = 0;
            $line = 1;

            // Var Qxtend
            $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
            
            $timeout        = 0;

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
                                <maintainSalesOrder>
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
                                      <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

            $qdocbody = '<dsSalesOrder>
                            <salesOrder>

                                <soCust>'.$req->j_custcode.'</soCust>
                                <soShip>'.$req->j_shipto.'</soShip>
                                <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>';

                                foreach($req->qtyretur as $data){
                                    if ($data > 0) {
                                        $qdocbody .=  '<salesOrderDetail>'.
                                                              '<line>'.$line.'</line>'.
                                                              '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                              '<sodQtyOrd> -'.$req->qtyretur[$flg].'</sodQtyOrd>'.                                
                                                      '</salesOrderDetail>';
                                      $line += 1;
                                    }
                                      $flg += 1;
                                }                                  
                                  
                                $qdocbody .=   '</salesOrder>
                                                </dsSalesOrder>';

            $qdocfoot = '</maintainSalesOrder>
                            </soapenv:Body>
                         </soapenv:Envelope>';

            $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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
                  $qty = '';
                  $price = '';
                  $disc = 0;
                  $total = 0;
                  $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
                  
                  // pisahin hasil balikan dari qxtend
                  foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
                      if(str_contains($data,'P: ')){
                        $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
                      }elseif(str_contains($data,'Q: ')){
                        $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
                      }elseif(str_contains($data, 'D: ')){
                        $disc = substr($data, stripos($data, 'D:') + 3). ',';
                      }
                  }

                  $price = explode(',', substr($price, 0, -1));
                  $qty   = explode(',', substr($qty, 0, -1));
                  $disc  = substr($disc, 0, -1);
                  $flg = 0;

                  // itung total harga nett line
                  foreach($price as $s){
                    $total += trim($price[$flg]) * trim($qty[$flg]);
                    $flg += 1;
                  }

                  // itung total nett disc master
                  if($disc != 0){
                    $total = $total - $total * $disc / 100;
                  }
   

                  // Data berhasil terbuat di QAD               
                  $so_nbr = (string) $xmlResp->xpath('//ns1:soNbr')[0];
                  $flgweb = 0;
                  $lineweb = 1;

                  // Insert SO QAD ke web
                  DB::table('retur_mstrs')
                          ->insert([
                              'so_nbr' => $so_nbr,
                              'so_cust' => $req->j_custcode,
                              'so_shipto' => $req->j_shipto,
                              'so_so_awal' => $req->sonbr,
                              'so_remarks' => $req->notes,
                              'so_status' => 1, // 1 New Retur
                              'so_site' => Session::get('site'), // Cek Session User
                              'so_price' => $total, // Cari Harga update di Qxtend, liat latian hari 3,
                              'created_at' => Carbon::now()->toDateTimeString(),
                              'updated_at' => Carbon::now()->toDateTimeString(),
                          ]);


                  foreach($req->qtyretur as $barang){
                      if ($barang > 0) {
                          DB::table('retur_dets')
                              ->insert([
                                  'so_nbr' => $so_nbr,
                                  'so_line' => $lineweb,
                                  'so_itemcode' => $req->itemcode[$flgweb],
                                  'so_qty' => $req->qtyretur[$flgweb],
                                  'so_status' => 1, // 1 New Retur
                                  'created_at' => Carbon::now()->toDateTimeString(),
                                  'updated_at' => Carbon::now()->toDateTimeString(),
                              ]);
                          $lineweb += 1;
                      }
                      $flgweb += 1;
                  }

                  Log::channel('customlog')->info('SO Retur : '.$so_nbr.' from SO Number : '.$req->so_nbr.' Successfully Created '.Session::get('username'));
                  session()->flash('updated','Retur Succesfully Created with SO Number : '.$so_nbr);
                  return back();
            }else{
                  Log::channel('customlog')->info('SO Retur from SO Number : '.$req->so_nbr.' Failed to send to QAD '.Session::get('username'));
                  session()->flash('error','Retur Failed to send to QAD');
                  return back();
            }
      }
    }


    // Retur QAD ** Used

    public function returbrowse(Request $req){
      $data = DB::table('retur_mstrs')
                  ->join('customers','customers.cust_code','=','retur_mstrs.so_cust')
                  ->leftjoin('cust_shipto as cust_ship','cust_ship.cust_code','=','retur_mstrs.so_shipto')
                  ->selectRaw('so_nbr,so_cust,so_shipto,so_so_awal,so_site,cust_ship.custname as "shipto_nama",customers.cust_desc,so_remarks,retur_mstrs.so_status, retur_mstrs.price_date,Date(retur_mstrs.created_at) as "sodate",
                    customers.cust_alt_name')
                  ->where('so_site','=',Session::get('site'))
        		      ->orderBy('retur_mstrs.created_at','Desc')
                  ->paginate(10);

      $customer = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
                  ->get();

      $shipto   = DB::table('cust_shipto')
          		  ->leftjoin('customers','customers.cust_code','=','cust_shipto.shipto')
          		  ->get();	
	  

      $item = DB::table('items')
            ->get();

      $location = DB::table('loc_mstrs')
                    ->where('loc_mstrs.loc_site',Session::get('site'))
                    ->whereRaw('(loc_loc = "FG" or loc_loc = "REJECT")')
                    ->get();


      if($req->ajax()){
        return view('so.table-retur',['data' => $data, 'customer' => $customer, 'item' => $item, 'custsearch' => $customer, 'shipsearch' => $shipto,'cship' => $customer,'itemedit' => $item, 'location' => $location, 'loced' => $location ]);
      }
      //dd($location);
      return view('so.soreturbrowse',['data' => $data, 'customer' => $customer, 'item' => $item, 'custsearch' => $customer, 'shipsearch' => $shipto,'cship' => $customer,'itemedit' => $item, 'location' => $location, 'loced' => $location  ]);
    }

    public function detailreturbrowse(Request $req){
      if($req->ajax()){
         $data = DB::table('retur_mstrs')
                      ->join('retur_dets','retur_mstrs.so_nbr','=','retur_dets.so_nbr')
                      ->join('items','retur_dets.so_itemcode','=','items.itemcode')
                      ->where('retur_dets.so_nbr','=',$req->sonbr)
                      ->get();

          $output = '';
          foreach($data as $data){
              $output .= '<tr>'.
                         '<td>'.$data->so_line.'</td>'.
                         '<td>'.$data->so_itemcode.' - '.$data->itemdesc.'</td>'.
                         '<td>'.$data->so_qty.'</td>'.
                         '<td>'.$data->so_loc.'</td>'.
                         '</tr>';
          }

          return response($output);

      }
    }

    public function createsoreturweb(Request $req){
        //dd($req->all());
        $data = DB::table('retur_mstrs')
                    ->where('so_site','=',Session::get('site'))
                    ->whereRaw('year(created_at) = "'.Carbon::now()->format('Y').'"')
                    ->count();


        $datasite = DB::table('site_mstrs')
                    ->where('site_code','=',Session::get('site'))
                    ->first();

        //$rn   = $data + 1;
        //$rn = str_pad($rn , 4, '0', STR_PAD_LEFT);
        $prefix = substr($datasite->r_nbr_retur, 0, 2);
        $rn    = substr($datasite->r_nbr_retur, 2, 4);
        
        if($prefix != Carbon::now()->format('y')){
            // Ganti bulan reset bulan & rn
            $prefix = Carbon::now()->format('y');
            $rn    = '0001';
        }else{
            $rn += 1;
            $rn = str_pad($rn , 4, '0', STR_PAD_LEFT);
        }

        $noso = "R".substr(Session::get('site'), 0,2).substr(Carbon::now()->format('Y'),3).$rn;

        //dd($noso,$prefix,$rn);
        // Insert SO QAD ke web
        DB::table('retur_mstrs')
                ->insert([
                    'so_nbr' => $noso,
                    'so_cust' => $req->custcode,
                    'so_shipto' => $req->shipto,
                    'so_remarks' => $req->remarks,
                    'price_date' => $req->pricedate,
                    'so_status' => 1, // 1 New Retur
                    'so_site' => Session::get('site'), // Cek Session User
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]);

        $flgweb = 0;
        $lineweb = 1;

        foreach($req->barang as $barang){
                DB::table('retur_dets')
                    ->insert([
                        'so_nbr' => $noso,
                        'so_line' => $lineweb,
                        'so_itemcode' => $req->barang[$flgweb],
                        'so_qty' => $req->jumlah[$flgweb],
                        'so_loc' => $req->loc[$flgweb],
                        'so_status' => 1, // 1 New Retur
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]);
                $lineweb += 1;
                $flgweb += 1;
        }
        // Update RN SO
        db::table('site_mstrs')
              ->where('site_code','=',Session::get('site'))
              ->update([
               'r_nbr_retur' => $prefix.$rn  
              ]);

        session()->flash('updated','Retur Succesfully Created with Retur Number : '.$noso);
        return back();
    }

    public function editdetailretur(Request $req){
      if($req->ajax()){
            $data = DB::table('retur_mstrs')
                    ->join('retur_dets','retur_mstrs.so_nbr','=','retur_dets.so_nbr')
                    ->join('items','items.itemcode','=','retur_dets.so_itemcode')
                    ->where('retur_mstrs.so_nbr','=',$req->sonbr)
                    ->get();

            $loc = DB::table('loc_mstrs')
                      ->where('loc_mstrs.loc_site','=',Session::get('site'))
                      ->whereRaw('(loc_loc = "FG" or loc_loc = "REJECT")')
                      ->get();



            $output = '';
            $qtyship = 0;
            $qtycan = 0;
            foreach($data as $data){
                $output .=  '<tr>'.
                            '<td> <input type="text" class="form-control" value="'.$data->so_itemcode.' - '.$data->itemdesc.'" readonly> </td>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_itemcode.'" name="itemcode[]" readonly>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_line.'" name="line[]" readonly>'.
                            '<td> <input type="number" class="form-control" value="'.$data->so_qty.'" name="qtyretur[]" readonly> </td>'.
                            '<td> <select id="loc" class="form-control loc selectpicker" data-live-search="true" name="loc[]" required autofocus> <option value = "">  Select Data </option>'; 
                              foreach($loc as $locs){
                                  if($locs->loc_loc == $data->so_loc){
                                    $output .=  '<option value="'.$locs->loc_loc.'" selected="selected">'.$locs->loc_loc.'</option>';
                                  }else{
                                    $output .=  '<option value="'.$locs->loc_loc.'">'.$locs->loc_loc.'</option>';
                                  }
                              } 
                $output .=  '</select></td>'.
                            '<td> <input type="number" min="'.$qtyship.'" class="form-control qtyso" value="'.$data->so_qty.'" name="qtyso[]"> </td>'.
                            '<td style="vertical-align:middle;text-align:center;"> <input type="checkbox" class="qaddel" value=""> </td>'.
                            '<input type="hidden" name="delLine[]" class="defdel" value="M">'.
                            '<tr>';
            
            }

            return response($output);
      }
    }

    public function editsoreturweb(Request $req){
      //dd($req->all());
              DB::table('retur_mstrs')
                        ->where('so_nbr','=',$req->edw_sonbr)
                        ->update([
                              'so_remarks' => $req->edw_remarks,
                              'updated_at' => Carbon::now()->toDateTimeString(),
                              'price_date' => $req->ed_pricedate
                        ]);

            // update data detail
              $flgweb = 0;
              $lineweb = array_values(array_slice($req->line, -1))[0]; // line terakhir web
              foreach($req->itemcode as $barang){

                  if($req->delLine[$flgweb] == 'R'){
                    // hapus row
                    DB::table('retur_dets')
                          ->where('so_nbr','=',$req->edw_sonbr)
                          ->where('so_itemcode','=',$req->itemcode[$flgweb])
                          ->where('so_line','=',$req->line[$flgweb])
                          ->delete();

                  }elseif($req->delLine[$flgweb] == 'A'){
                          DB::table('retur_dets')
                              ->insert([
                                  'so_nbr' => $req->edw_sonbr,
                                  'so_itemcode' => $req->itemcode[$flgweb],
                                  'so_line' => $lineweb + 1,
                                  'so_qty' => $req->qtyso[$flgweb],
                                  'so_loc' => $req->loc[$flgweb],
                                  'so_status' => '1',
                                  'created_at' => Carbon::now()->toDateTimeString(),
                                  'updated_at' => Carbon::now()->toDateTimeString()
                              ]);

                          $lineweb += 1;
                  }else{
                          DB::table('retur_dets')
                                ->where('so_nbr','=',$req->edw_sonbr)
                                ->where('so_itemcode','=',$req->itemcode[$flgweb])
                                ->where('so_line','=',$req->line[$flgweb])
                                ->update([
                                  'so_qty' => $req->qtyso[$flgweb],
                                  'so_loc' => $req->loc[$flgweb],
                                  'updated_at' => Carbon::now()->toDateTimeString()
                                ]);
                          //dd('1233');
                  }

                  
                      $flgweb += 1;
              }
          
            session()->flash('updated','Retur Succesfully Updated. SO Number : '.$req->edw_sonbr);
            return back();
    }

    public function createsoretur(Request $req){
      //dd($req->all());
      
      // Variable Web
        $flg = 0;
        $line = 1;

        // Var Qxtend
        $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
        $slsacct1       = '4000'; // Khusus Demo Internal
        $sub            = 'mech'; // Khusus Demo Internal
        $timeout        = 0;
        $newline        = intval(array_values(array_slice($req->line, -1))[0]);

        // XML Qextend -- Pending Invoice
        
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
                          <maintainPendingInvoice>
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
                                <qcom:propertyValue>eB2_2</qcom:propertyValue>
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

        $qdocbody = '<dsPendingInvoice>
                          <pendingInvoice>
                            <soNbr>'.$req->ed_sonbr.'</soNbr>
                            <soCust>'.$req->eds_custcode.'</soCust>
                            <soShip>'.$req->ed_shipto.'</soShip>
                            <soRmks><![CDATA["'.$req->ed_remarks.'"]]></soRmks>';
                            //<soPricingDt>'.$req->con_pricedate.'</soPricingDt>; Ga Yakin jadi pke default hari ini.

                            foreach($req->itemcode as $data){
                              if($req->delLine[$flg] == 'A'){
                                    $newline += 1;
                                    
                                    $qdocbody .=  '<salesLine>'.
                                                          '<operation>'.$req->delLine[$flg].'</operation>'.
                                                          '<line>'.$newline.'</line>'.
                                                          '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                          '<sodQtyChg> -'.$req->qtyso[$flg].'</sodQtyChg>'.
                                                          '<sodLoc>'.$req->loc[$flg].'</sodLoc>'.
                                                          //'<sodAcct>'.$slsacct1.'</sodAcct>'.
                                                          //'<sodSub>'.$sub.'</sodSub>'.
                                                  '</salesLine>';
                                }else{
                                    $qdocbody .=  '<salesLine>'.
                                                          '<operation>'.$req->delLine[$flg].'</operation>'.
                                                          '<line>'.$req->line[$flg].'</line>'.
                                                          '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                          '<sodQtyChg> -'.$req->qtyso[$flg].'</sodQtyChg>'.  
                                                          '<sodLoc>'.$req->loc[$flg].'</sodLoc>'.
                                                          //'<sodAcct>'.$slsacct1.'</sodAcct>'.
                                                          //'<sodSub>'.$sub.'</sodSub>'.
                                                  '</salesLine>';
                                }
                                  
                                $flg += 1;
                            }                                  
                              
                            $qdocbody .=   '</pendingInvoice>
                                              </dsPendingInvoice>';

        $qdocfoot = '</maintainPendingInvoice>
                     </soapenv:Body>
                     </soapenv:Envelope>';

        $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

        if($qdocResult=="success" OR $qdocResult=="warning")
        {
            $so_nbr = (string) $xmlResp->xpath('//ns1:soNbr')[0];

              DB::table('retur_mstrs')
                        ->where('so_nbr','=',$req->ed_sonbr)
                        ->update([
                              'so_status' => 2, // 1 Created 2 Confirm
                              'so_remarks' => $req->ed_remarks,
                              'updated_at' => Carbon::now()->toDateTimeString(),
                              'price_date' => $req->con_pricedate
                        ]);

            // update data detail
              $flgweb = 0;
              $lineweb = array_values(array_slice($req->line, -1))[0]; // line terakhir web
              foreach($req->itemcode as $barang){

                  if($req->delLine[$flgweb] == 'R'){
                    // hapus row
                    DB::table('retur_dets')
                          ->where('so_nbr','=',$req->ed_sonbr)
                          ->where('so_itemcode','=',$req->itemcode[$flgweb])
                          ->where('so_line','=',$req->line[$flgweb])
                          ->delete();

                  }elseif($req->delLine[$flgweb] == 'A'){
                          DB::table('retur_dets')
                              ->insert([
                                  'so_nbr' => $req->ed_sonbr,
                                  'so_itemcode' => $req->itemcode[$flgweb],
                                  'so_line' => $lineweb + 1,
                                  'so_qty' => $req->qtyso[$flgweb],
                                  'so_loc' => $req->loc[$flgweb],
                                  'so_status' => '1',
                                  'created_at' => Carbon::now()->toDateTimeString(),
                                  'updated_at' => Carbon::now()->toDateTimeString()
                              ]);

                          $lineweb += 1;
                  }else{
                          DB::table('retur_dets')
                                ->where('so_nbr','=',$req->ed_sonbr)
                                ->where('so_itemcode','=',$req->itemcode[$flgweb])
                                ->where('so_line','=',$req->line[$flgweb])
                                ->update([
                                  'so_qty' => $req->qtyso[$flgweb],
                                  'so_loc' => $req->loc[$flgweb],
                                  'updated_at' => Carbon::now()->toDateTimeString()
                                ]);
                          //dd('1233');
                  }

                  
                      $flgweb += 1;
              }
          
            Log::channel('customlog')->info('SO Retur : '.$so_nbr.' from SO Number : '.$req->so_nbr.' Successfully Created '.Session::get('username'));
            session()->flash('updated','Retur Succesfully Created with SO Number : '.$so_nbr);
            return back();
        }else{
              Log::channel('customlog')->info('SO Retur from SO Number : '.$req->so_nbr.' Failed to send to QAD '.Session::get('username'));
              session()->flash('error','Retur Failed to send to QAD');
              return back();
        }
    }

    public function deleteretur(Request $req){
      //dd($req->all());

      DB::table('retur_mstrs')
            ->where('retur_mstrs.so_nbr','=',$req->text_sonbr)
            ->update([
                  'so_status' => 3 // 1 New 2 Confirm 3 Delete
            ]);

      DB::table('retur_dets')
            ->where('so_nbr','=',$req->text_sonbr)
            ->update([
                  'so_status' => 2 // 1 New 2 Delete
            ]);

      Session()->flash('updated','Retur Web Succesfully Deleted');
      return back();
    }

    public function editsoreturwebdetail(Request $req){
      if($req->ajax()){
            $data = DB::table('retur_mstrs')
                    ->join('retur_dets','retur_mstrs.so_nbr','=','retur_dets.so_nbr')
                    ->join('items','items.itemcode','=','retur_dets.so_itemcode')
                    ->where('retur_mstrs.so_nbr','=',$req->sonbr)
                    ->get();

            $loc = DB::table('loc_mstrs')
                      ->where('loc_mstrs.loc_site','=',Session::get('site'))
                      ->whereRaw('(loc_loc = "FG" or loc_loc = "REJECT")')
                      ->get();


            $output = '';
            $qtyship = 0;
            $qtycan = 0;
            foreach($data as $data){
                $output .=  '<tr>'.
                            '<td> <input type="text" class="form-control" value="'.$data->so_itemcode.' - '.$data->itemdesc.'" readonly> </td>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_itemcode.'" name="itemcode[]" readonly>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_line.'" name="line[]" readonly>'.
                            '<td> <input type="number" min="'.$qtyship.'" class="form-control qtyso" value="'.$data->so_qty.'" name="qtyso[]"> </td>'.
                            '<td> <select id="loc" class="form-control loc selectpicker" data-live-search="true" name="loc[]" required autofocus> <option value = "">  Select Data </option>'; 
                              foreach($loc as $locs){
                                  if($locs->loc_loc == $data->so_loc){
                                    $output .=  '<option value="'.$locs->loc_loc.'" selected="selected">'.$locs->loc_loc.'</option>';
                                  }else{
                                    $output .=  '<option value="'.$locs->loc_loc.'">'.$locs->loc_loc.'</option>';
                                  }
                              } 
                $output .=  '</select></td>'.
                            '<td style="vertical-align:middle;text-align:center;"> <input type="checkbox" class="qaddel" value=""> </td>'.
                            '<input type="hidden" name="delLine[]" class="defdel" value="M">'.
                            '<tr>';
            
            }

            return response($output);
      }
    }

    public function getlocretur(Request $req){
      if($req->ajax()){
        $data =   DB::table('loc_mstrs')
                    ->where('loc_site','=',Session::get('site'))
                    ->get();

        $output = '';
        foreach($data as $data){
            $output .= '<option value="'.$data->loc_loc.'">'.$data->loc_loc.'</option>';
        }

        return response($output);
      }
    }

    public function returpdf(Request $req){
        $data = DB::table('retur_mstrs')
                      ->join('retur_dets','retur_mstrs.so_nbr','=','retur_dets.so_nbr')
                      ->leftjoin('items','retur_dets.so_itemcode','=','items.itemcode')
                      ->where('retur_mstrs.so_nbr','=',$req->sonbr)
                      ->orderBy('so_itemcode','Asc')
                      ->get();

        $header = DB::table('retur_mstrs')
                      ->leftJoin('customers','customers.cust_code','=','retur_mstrs.so_cust')
                      ->where('retur_mstrs.so_nbr','=',$req->sonbr)
                      ->selectRaw('so_nbr,DATE(retur_mstrs.created_at) as sodate,cust_desc,cust_alamat,cust_alt_name')
                      ->first();

        $alamat = DB::table('site_mstrs')
                      ->where('site_code','=',Session::get('site'))
                      ->first();



        //dd($header);

        $pdf = PDF::loadview('so.print-retur',['data'=>$data,'header'=>$header,'alamat'=>$alamat]);
        
        return $pdf->stream();

        return view('so.print-retur',['data'=>$data,'header'=>$header,'alamat'=>$alamat]);
    }

    public function returpdftest(Request $req){
        $data = DB::table('retur_mstrs')
                      ->join('retur_dets','retur_mstrs.so_nbr','=','retur_dets.so_nbr')
                      ->leftjoin('items','retur_dets.so_itemcode','=','items.itemcode')
                      ->where('retur_mstrs.so_nbr','=',$req->sonbr)
                      ->orderBy('so_itemcode','Asc')
                      ->get();

        $header = DB::table('retur_mstrs')
                      ->leftJoin('customers','customers.cust_code','=','retur_mstrs.so_cust')
                      ->where('retur_mstrs.so_nbr','=',$req->sonbr)
                      ->selectRaw('so_nbr,DATE(retur_mstrs.created_at) as sodate,cust_desc,cust_alamat,cust_alt_name')
                      ->first();

        $alamat = DB::table('site_mstrs')
                      ->where('site_code','=',Session::get('site'))
                      ->first();



        //dd($header);

        $pdf = PDF::loadview('so.print-retur-mixhead',['data'=>$data,'header'=>$header,'alamat'=>$alamat]);
        
        return $pdf->stream();

        return view('so.print-retur-mixhead',['data'=>$data,'header'=>$header,'alamat'=>$alamat]);
    }




    // ----------------- SO Sales OH

    public function sosalesoh(Request $req){

      if(Session::get('pusat_cabang')==1){
        $data = db::table('so_mstrs')
                ->join('customers','customers.cust_code','=','so_mstrs.so_cust')
                ->join('approval_tmp','approval_tmp.so_nbr','=','so_mstrs.so_nbr')
                ->join('users','approval_tmp.approval_approver','=','users.username')
                //->whereBetween('so_mstrs.so_status',['2', '4']) // status hold
                ->where('so_mstrs.so_status','=','7') // Needs Approval
                ->whereNull('approval_by') // blom diapprove / reject
                ->groupBy('approval_tmp.so_nbr')
                ->orderBy('approval_seq','asc')
                ->orderBy('so_mstrs.created_at','Desc')
                ->paginate(10);
      }else{
        $data = db::table('so_mstrs')
                ->join('customers','customers.cust_code','=','so_mstrs.so_cust')
                ->join('approval_tmp','approval_tmp.so_nbr','=','so_mstrs.so_nbr')
                ->join('users','approval_tmp.approval_approver','=','users.username')
                //->whereBetween('so_mstrs.so_status',['2', '4']) // status hold
                ->where('so_mstrs.so_status','=','7') // Needs Approval
                ->whereNull('approval_by') // blom diapprove / reject
                ->where('so_site','=',Session::get('site'))
                ->groupBy('approval_tmp.so_nbr')
                ->orderBy('approval_seq','asc')
                ->orderBy('so_mstrs.created_at','Desc')
                ->paginate(10);
      }
      

      $customer = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
                    ->get();

      if($req->ajax()){
            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $sonbr = $req->get('sonbr');
            $cust = $req->get('cust');
            $datefrom = $req->get('datefrom');
            $dateto = $req->get('dateto');


            if ($sonbr == '' and $cust == '' and $datefrom == '' and $dateto == '') {
                if(Session::get('pusat_cabang')==1){
                  $data = db::table('so_mstrs')
                          ->join('customers','customers.cust_code','=','so_mstrs.so_cust')
                          ->join('approval_tmp','approval_tmp.so_nbr','=','so_mstrs.so_nbr')
                          ->join('users','approval_tmp.approval_approver','=','users.username')
                          //->whereBetween('so_mstrs.so_status',['2', '4']) // status hold
                          ->where('so_mstrs.so_status','=','7') // Needs Approval
                          ->whereNull('approval_by') // blom diapprove / reject
                          ->groupBy('approval_tmp.so_nbr')
                          ->orderBy('approval_seq','asc')
                          ->orderBy('so_mstrs.created_at','Desc')
                          ->paginate(10);
                }else{
                  $data = db::table('so_mstrs')
                          ->join('customers','customers.cust_code','=','so_mstrs.so_cust')
                          ->join('approval_tmp','approval_tmp.so_nbr','=','so_mstrs.so_nbr')
                          ->join('users','approval_tmp.approval_approver','=','users.username')
                          //->whereBetween('so_mstrs.so_status',['2', '4']) // status hold
                          ->where('so_mstrs.so_status','=','7') // Needs Approval
                          ->whereNull('approval_by') // blom diapprove / reject
                          ->where('so_site','=',Session::get('site'))
                          ->groupBy('approval_tmp.so_nbr')
                          ->orderBy('approval_seq','asc')
                          ->orderBy('so_mstrs.created_at','Desc')
                          ->paginate(10);
                }

                return view('/so/table-sohold', ['data' => $data]);
            }else{
                if($datefrom == ''){
                   $datefrom = '2000-01-01';
                }
                if($dateto == ''){
                   $dateto = '3000-01-01';
                }

                $kondisi = "so_mstrs.so_duedate between '".$datefrom."' and '".$dateto."'";

                if ($sonbr != '') {
                    $kondisi .= ' and so_mstrs.so_nbr = "' . $sonbr . '"';
                }
                if ($cust != '') {
                    $kondisi .= ' and customers.cust_desc LIKE "' . $cust . '%"';
                }

                if(Session::get('pusat_cabang')==1){
                  $data = db::table('so_mstrs')
                          ->join('customers','customers.cust_code','=','so_mstrs.so_cust')
                          ->join('approval_tmp','approval_tmp.so_nbr','=','so_mstrs.so_nbr')
                          ->join('users','approval_tmp.approval_approver','=','users.username')
                          //->whereBetween('so_mstrs.so_status',['2', '4']) // status hold
                          ->where('so_mstrs.so_status','=','7') // Needs Approval
                          ->whereNull('approval_by') // blom diapprove / reject
                          ->whereRaw($kondisi)
                          ->groupBy('approval_tmp.so_nbr')
                          ->orderBy('approval_seq','asc')
                          ->orderBy('so_mstrs.created_at','Desc')
                          ->paginate(10);
                }else{
                  $data = db::table('so_mstrs')
                          ->join('customers','customers.cust_code','=','so_mstrs.so_cust')
                          ->join('approval_tmp','approval_tmp.so_nbr','=','so_mstrs.so_nbr')
                          ->join('users','approval_tmp.approval_approver','=','users.username')
                          //->whereBetween('so_mstrs.so_status',['2', '4']) // status hold
                          ->where('so_mstrs.so_status','=','7') // Needs Approval
                          ->whereNull('approval_by') // blom diapprove / reject
                          ->where('so_site','=',Session::get('site'))
                          ->whereRaw($kondisi)
                          ->groupBy('approval_tmp.so_nbr')
                          ->orderBy('approval_seq','asc')
                          ->orderBy('so_mstrs.created_at','Desc')
                          ->paginate(10);
                }

                return view('/so/table-sohold',['data' => $data]);
            }

            if($sonbr != '' or $cust != '' or $datefrom != '' or $dateto != ''){

                
            }
      }
            
      return view('/so/sooh',['data' => $data, 'custsearch' => $customer]);
    }

    public function approvehold(Request $req){
      //dd($req->all());
      switch ($req->input('action')){
          case 'reject' :
              DB::table('approval_tmp')
                    ->where('so_nbr','=',$req->e_sonbr)
                    ->where('approval_approver','=',$req->nextapp)
                    ->where('approval_seq','=',$req->nextorder)
                    ->update([
                        'approval_date' => Carbon::now()->toDateString(),
                        'approval_status' => '2', // 1 Approve 2 Reject
                        'approval_by' => Session::get('username'),
                        'approval_reason' => $req->e_reason
                    ]);

              $data = DB::table('approval_tmp')
                          ->where('so_nbr','=',$req->e_sonbr)
                          ->whereNotNull('approval_by')
                          ->get();

              foreach($data as $data){
                  DB::table('approval_hist')
                          ->insert([
                                'so_nbr' => $data->so_nbr,
                                'approval_approver' => $data->approval_approver,
                                'approval_alt_approver' => $data->approval_alt_approver,
                                'approval_seq' => $data->approval_seq,
                                'approval_date' => $data->approval_date,
                                'approval_reason' => $data->approval_reason,
                                'approval_status' => $data->approval_status,
                                'approval_by' => $data->approval_by,
                                'created_at' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString()
                          ]);
              }

              DB::table('approval_tmp')
                      ->where('so_nbr','=',$req->e_sonbr)
                      ->delete();

              DB::table('so_mstrs')
                        ->where('so_nbr','=',$req->e_sonbr)
                        ->update([
                                'so_status' => '8', // 1 Aktif 234 Hold 5 Delete 6 Closed 7 Waiting 4 app 8 Rejected
                                'updated_at' => Carbon::now()->toDateTimeString()
                        ]);

              session()->flash('updated','SO Successfully rejected in Web');
              return back();

          break;

          case 'confirm' :

              DB::table('approval_tmp')
                        ->where('so_nbr','=',$req->e_sonbr)
                        ->where('approval_approver','=',$req->nextapp)
                        ->where('approval_seq','=',$req->nextorder)
                        ->update([
                            'approval_date' => Carbon::now()->toDateString(),
                            'approval_status' => '1', // 1 Approve 2 Reject
                            'approval_by' => Session::get('username'),
                            'approval_reason' => $req->e_reason
                        ]);

              $data = DB::table('approval_tmp')
                          ->where('so_nbr','=',$req->e_sonbr)
                          ->whereNull('approval_by')
                          ->get();

              //dd($data);
              if(!$data->isEmpty()){
                session()->flash('updated','SO Number : '.$req->e_sonbr.' is Approved, Sent to next approver');
                return back();
              }else{
                // tidak ada next approver
                $listapp = DB::table('approval_tmp')
                          ->where('so_nbr','=',$req->e_sonbr)
                          ->get();

                foreach($listapp as $data){
                    DB::table('approval_hist')
                            ->insert([
                                  'so_nbr' => $data->so_nbr,
                                  'approval_approver' => $data->approval_approver,
                                  'approval_alt_approver' => $data->approval_alt_approver,
                                  'approval_seq' => $data->approval_seq,
                                  'approval_date' => $data->approval_date,
                                  'approval_reason' => $data->approval_reason,
                                  'approval_status' => $data->approval_status,
                                  'approval_by' => $data->approval_by,
                                  'created_at' => Carbon::now()->toDateTimeString(),
                                  'updated_at' => Carbon::now()->toDateTimeString()
                            ]);
                }

                DB::table('approval_tmp')
                        ->where('so_nbr','=',$req->e_sonbr)
                        ->delete();

                // Qxtend ilangin action status
                $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
                      
                $timeout        = 0;

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
                                    <qcom:suppressResponseDetail>true</qcom:suppressResponseDetail>
                                  </wsa:ReferenceParameters>
                                  <wsa:ReplyTo>
                                    <wsa:Address>urn:services-qad-com:</wsa:Address>
                                  </wsa:ReplyTo>
                                </soapenv:Header>
                                <soapenv:Body>
                                  <maintainSalesOrderCredit>
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
                                        <qcom:propertyValue>eB_2</qcom:propertyValue>
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

                $qdocbody = '       <dsSalesOrderCredit>
                                      <salesOrderCredit>
                                        <soNbr>'.$req->e_sonbr.'</soNbr>
                                        <soStat></soStat>
                                      </salesOrderCredit>
                                    </dsSalesOrderCredit>';

                $qdocfoot = '</maintainSalesOrderCredit>
                              </soapenv:Body>
                             </soapenv:Envelope>';

                $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

                //dd($qdocRequest,$qdocResult,$qdocResponse);          

                if($qdocResult=="success" OR $qdocResult=="warning")
                {
                    DB::table('so_mstrs')
                          ->where('so_nbr','=',$req->e_sonbr)
                          ->update([
                                  'so_status' => '1',
                                  'updated_at' => Carbon::now()->toDateTimeString()
                          ]);

                    Log::channel('customlog')->info('SO : '.$req->e_sonbr.' Successfully Released from Hold '.Session::get('username'));
                    session()->flash('updated','SO Number : '.$req->e_sonbr.' is Approved, Updated in qad');
                    return back();
                }else{
                    Log::channel('customlog')->info('SO : '.$req->e_sonbr.' Failed to Release from Hold '.Session::get('username'));
                }


                
              }
          break;
      }
    }

    public function testxml(Request $req){
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

        //dd($errorlist);
        
        if(!$errorlist->isEmpty()){
          Session()->flash('updated','There is a no difference between Web & QAD');
          return back();
        }else{
          Session()->flash('error','There are difference(s) between Web & QAD');
          return back();
        }
        
    }



    // ----------------- SO Consignment

    public function socons(Request $req){
        if(Session::get('pusat_cabang')==1){
		        $data = DB::table('so_mstrs')
		         ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
		         ->whereBetween('so_mstrs.so_status',['10', '12']) // So Consignment
             ->selectRaw('*,so_mstrs.created_at as "so_created"')
		         ->orderBy('so_mstrs.created_at','Desc')
		         ->paginate(10);

	      }
        else if (Session::get('pusat_cabang')==0){

    		$data = DB::table('so_mstrs')
                        	->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                			     ->where('so_site','=',Session::get('site'))
                           ->whereBetween('so_mstrs.so_status',['10', '12']) // So Consignment
                          ->selectRaw('*,so_mstrs.created_at as "so_created"')
                        	->orderBy('so_mstrs.created_at','Desc')
                        	->paginate(10);
      	}

            $customer = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
                  ->get();

            $item = DB::table('items')
                  ->get();


        return view('so.socons',['data' => $data, 'customer' => $customer, 'item' => $item, 'itemedit' => $item, 'custsearch' => $customer]);
    }

    public function createsocons(Request $req){
      //dd($req->all());
          // Variable Web
      $flg = 0;
      $line = 1;
      $data = DB::table('so_mstrs')
                ->where('so_site','=',Session::get('site'))
                ->whereRaw('year(created_at) = "'.Carbon::now()->format('Y').'"')
                ->count();

      $datasite = DB::table('site_mstrs')
            ->where('site_code','=',Session::get('site'))
            ->first();

      $site = substr(Session::get('site'),0,2); // Ambil 2 Digit Site Pertama

      //$rn = str_pad($data + 1, 5, '0', STR_PAD_LEFT); // Running Number dari total SO per Site
      $prefix = substr($datasite->r_nbr_so, 0, 2);
      $rn    = substr($datasite->r_nbr_so, 2, 5);
      
      if($prefix != Carbon::now()->format('y')){
          // Ganti bulan reset bulan & rn
          $prefix = Carbon::now()->format('y');
          $rn    = '00001';
      }else{
          $rn += 1;
          $rn = str_pad($rn , 5, '0', STR_PAD_LEFT);
      }
      
      $year = substr(Carbon::now()->format('Y'),3); // digit terakhir Tahun

      $noso = $site.$year.$rn;

      // Validasi WSA --> 02/19/2021 --> cek apakah di qad sudah ada nomor so tersebut
      $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
      $qxReceiver     = '';
      $qxSuppRes      = 'false';
      $qxScopeTrx     = '';
      $qdocName       = '';
      $qdocVersion    = '';
      $dsName         = '';
      
      $timeout        = 0;

      $domain         = 'DKH';

      // ** Edit here
      $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                        '<Body>'.
                        '<checkExistingSO xmlns="urn:iris.co.id:wsatrain">'.
                        '<inpdomain>'.$domain.'</inpdomain>'.
                        '<inpsonbr>'.$noso.'</inpsonbr>'.
                        '</checkExistingSO>'.
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
      
      $qdocResult1 = (string) $xmlResp->xpath('//ns1:outOK')[0];

       // dd($qdocResult1);

      if($qdocResult1 == 'true'){
          session()->flash('error','SO Number '.$noso.' sudah terdapat pada QAD, Mohon dicek ');
          return back();
      }

      // Var Qxtend
      $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
      $slsacct1       = '4000'; // Khusus Demo Internal
      $sub            = 'mech'; // Khusus Demo Internal
      $timeout        = 0;

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
                        <maintainPendingInvoice>
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
                              <qcom:propertyValue>eB2_2</qcom:propertyValue>
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

      $qdocbody = '<dsPendingInvoice>
                        <pendingInvoice>
                          <soNbr>'.$noso.'</soNbr>
                          <soCust>'.$req->custcode.'</soCust>
                          <soShip>'.$req->shipto.'</soShip>
                          <soDueDate>'.$req->duedate.'</soDueDate>';

                          $data = DB::table('cust_shipto')
                                      ->where('cust_code','=',$req->custcode)
                                      ->first();
                          
                          if($data){
                              // Loc = Shipto
                              foreach($req->barang as $data){
                                      $qdocbody .=  '<salesLine>'.
                                                            '<line>'.$line.'</line>'.
                                                            '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                            '<sodQtyChg>'.$req->jumlah[$flg].'</sodQtyChg>'.
                                                            '<sodUm>'.$req->um[$flg].'</sodUm>'.  
                                                            '<sodLoc>'.$req->shipto.'</sodLoc>'.                     
                                                            //'<sodAcct>'.$slsacct1.'</sodAcct>'.
                                                            //'<sodSub>'.$sub.'</sodSub>'.
                                                    '</salesLine>';
                                    $line += 1;
                                    $flg += 1;
                              }
                          }else{
                              // Loc = Cust code tampa -
                              foreach($req->barang as $data){
                                        $qdocbody .=  '<salesLine>'.
                                                              '<line>'.$line.'</line>'.
                                                              '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                              '<sodQtyChg>'.$req->jumlah[$flg].'</sodQtyChg>'.
                                                              '<sodUm>'.$req->um[$flg].'</sodUm>'.  
                                                              '<sodLoc>'.str_replace('-', '', $req->custcode).'</sodLoc>'.
                                                              //'<sodAcct>'.$slsacct1.'</sodAcct>'.
                                                              //'<sodSub>'.$sub.'</sodSub>'.
                                                      '</salesLine>';
                                      $line += 1;
                                      $flg += 1;
                              }
                          }

                                                            
                            
                          $qdocbody .=   '</pendingInvoice>
                                            </dsPendingInvoice>';

      $qdocfoot = '</maintainPendingInvoice>
                   </soapenv:Body>
                   </soapenv:Envelope>';

      $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

      //dd($qdocResponse,$qdocResult);

      if($qdocResult=="success" OR $qdocResult=="warning")
      {
          $so_nbr = (string) $xmlResp->xpath('//ns1:soNbr')[0];
          $qty = '';
          $price = '';
          $disc = 0;
          $total = 0;
          $discdet = 0;
          $lineweb = 1;
          $flgweb = 0;
          $pricelist = 0;
          $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
          
          // pisahin hasil balikan dari qxtend
          foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
              if(str_contains($data,'P: ')){
                $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
              }elseif(str_contains($data,'Q: ')){
                $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
              }elseif(str_contains($data, 'D: ')){
                $disc = substr($data, stripos($data, 'D:') + 3). ',';
              }elseif(str_contains($data, 'C: ')){
                $discdet .= substr($data, stripos($data, 'C:') + 3). ',';
              }elseif(str_contains($data, 'X: ')){
                $pricelist .= substr($data, stripos($data, 'C:') + 3). ',';
              }
          }

          $price = explode(',', substr($price, 0, -1));
          $qty   = explode(',', substr($qty, 0, -1));
          $discdet = explode(',', substr($discdet, 0, -1));
          $pricelist = explode(',', substr($pricelist, 0, -1));
          $disc  = substr($disc, 0, -1);
          $flg = 0;
          // dd($price);

          if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
            // Qxtend Tidak terima harga / disc / qty

            Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from .p for SO Number : '.$noso.'-'.Session::get('username'));

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
                '<insonbr>'.$noso.'</insonbr>'.
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

            $dataloop    = $xmlResp->xpath('//ns1:tempRow');
            $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
            
            if ($qdocResultx == 'true')  {
              $flgarr = 0;
              foreach($dataloop as $data){
                $price[$flgarr] = $data->t_netprice;
                $discdet[$flgarr] = $data->t_discdet;
                $qty[$flgarr] = $data->t_qtyord;
                $pricelist[$flgarr] = $data->t_pricelist;
                $disc = $data->t_dischead;
              
                $flgarr += 1;
              }
            }else{
              Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from WSA for SO Number : '.$noso.'-'.Session::get('username'));
            }
          }


          // itung total harga nett line
          if($price != ''){
            foreach($price as $s){
              $total += trim($price[$flg]) * trim($qty[$flg]);
              $flg += 1;
            }

            // itung total nett disc master
            if($disc != 0){
              $total = $total - $total * $disc / 100;
            }
          }
          

          // Insert SO QAD ke web
          DB::table('so_mstrs')
                  ->insert([
                      'so_nbr' => $so_nbr,
                      'so_cust' => $req->custcode,
                      'so_shipto' => $req->shipto,
                      'so_duedate' => $req->duedate,
                      'so_status' => 10, // 10 Consignment
                      'so_price' => $total,
                      'so_site' => Session::get('site'), // Cek Session User
                      'so_user' => Session::get('username'),
                      'created_at' => Carbon::now()->toDateTimeString(),
                      'updated_at' => Carbon::now()->toDateTimeString(),
                  ]);


          foreach($req->barang as $barang){
                  DB::table('so_dets')
                      ->insert([
                          'so_nbr' => $so_nbr,
                          'so_line' => $lineweb,
                          'so_itemcode' => $req->barang[$flgweb],
                          'so_qty' => $req->jumlah[$flgweb],
                          'so_um' => $req->um[$flgweb],
                          'so_harga' => trim($price[$flgweb]),
                          'so_disc' => trim($discdet[$flgweb]),
                          'so_pr_list' => trim($pricelist[$flgweb]),
                          'so_status' => 1, // 1 New Retur
                          'created_at' => Carbon::now()->toDateTimeString(),
                          'updated_at' => Carbon::now()->toDateTimeString(),
                      ]);
                  $lineweb += 1;
                  $flgweb += 1;
          }

          // Update RN SO
          
          db::table('site_mstrs')
                      ->where('site_code','=',Session::get('site'))
                      ->update([
                       'r_nbr_so' => $prefix.$rn  
                      ]);

          Log::channel('customlog')->info('SO Consignment : '.$so_nbr.' Successfully Created '.Session::get('username'));
          session()->flash('updated','SO Consignment Succesfully Created with Number : '.$so_nbr);
          return back();
      }else{
            $resultProcess  = false;
            $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
            $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
            $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
            $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


            //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev,$qdocResult);
            Log::channel('customlog')->info('SO Consignment Failed to send to QAD , error :'.$qdocMsgDesc.'--'.$qdocMsgData.'--'.$qdocMsgSev,'-'.Session::get('username'));
            session()->flash('error','SO Consignment Failed to send to QAD');
            return back();
      }
    }

    public function getlistum(Request $req){
      if($req->ajax()){
        $item = DB::table('items')
                    ->where('itemcode','=',$req->item)
                    ->first();

        $data = DB::table('item_konversi')
                    ->where('item_code','=',$req->item)
                    ->where('um_1','=',$item->item_um)
                    ->get();

        $list = array($item->item_um);

        if(!$data->isEmpty()){
            foreach($data as $data){
                array_push($list, $data->um_2);
            }
        }

        $output = '';
        foreach($list as $list){
            $output .= '<option value="'.$list.'">'.$list.'</option>';
        }

        return response($output);

      }
    }

    public function soconssearch(Request $req){
      
      if ($req->ajax()) {
      $sonumber = $req->get('sonumber');
      $customer = $req->get('customer');
      $totalstart = $req->get('totalstart');
      $totalto = $req->get('totalto');
      $datefrom = $req->get('duedatefrom');
      $dateto = $req->get('duedateto');
      $sort_by = $req->get('sortby');
      $sort_type = $req->get('sorttype');


      if ($sonumber == '' and $customer == '' and $totalstart == '' and $totalto == '' and $datefrom == '' and $dateto == ''){
          // dd('aaaa');
          if(Session::get('pusat_cabang')==1){
            $data = DB::table('so_mstrs')
                  ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                  ->whereBetween('so_mstrs.so_status',['10', '12']) // So Consignment
             ->selectRaw('*,so_mstrs.created_at as "so_created"')
                  ->orderBy('so_mstrs.created_at','Desc')
                  ->paginate(10);
          }
          else if (Session::get('pusat_cabang')==0){

        $data = DB::table('so_mstrs')
                        ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                        ->where('so_site','=',Session::get('site'))
                        ->whereBetween('so_mstrs.so_status',['10', '12']) // So Consignment
             ->selectRaw('*,so_mstrs.created_at as "so_created"')
                        ->orderBy('so_mstrs.created_at','Desc')
                        ->paginate(10);
          }

            $cust = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
                    ->get();

                  $item = DB::table('items')
                    ->get();
            
              return view('so.table-socons', ['data' => $data, 'customer' => $cust, 'item' => $item, 'itemedit' => $item]);
      } else {
          if($datefrom == null){
            $datefrom = '2000-01-01';
          }
          if($dateto == null){
            $dateto = '3000-12-31';
          }
          if($totalstart == null){
            $totalstart = 0;
          }
          if($totalto == null){
            $totalto = 999999999999;
          }

          // dd($dateto);

          $kondisi = "so_duedate BETWEEN '".$datefrom."' and '".$dateto."' and so_price BETWEEN '".$totalstart."' and '".$totalto."' ";

          if ($sonumber != '') {
              $kondisi .= ' and so_nbr = "' . $sonumber . '"';
              // dd($kondisi);
          }
          if ($customer != '') {
              $kondisi .= ' and cust_code = "' . $customer . '"';
          }
          
          // dd($kondisi);
          if(Session::get('pusat_cabang')==1){

        $data = DB::table('so_mstrs')
                  ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                  ->whereRaw($kondisi)
                  ->whereBetween('so_mstrs.so_status',['10', '12']) // So Consignment
             ->selectRaw('*,so_mstrs.created_at as "so_created"')
                  ->orderBy('so_mstrs.created_at','Desc')
                  ->paginate(10);
            }
            else if (Session::get('pusat_cabang')==0){

        $data = DB::table('so_mstrs')
                  ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                  ->whereRaw($kondisi)
                  ->whereBetween('so_mstrs.so_status',['10', '12']) // So Consignment
             ->selectRaw('*,so_mstrs.created_at as "so_created"')
                  ->where('so_site','=',Session::get('site'))
                  ->orderBy('so_mstrs.created_at','Desc')
                  ->paginate(10);
            }
          

          $cust = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
                    ->get();

                $item = DB::table('items')
                    ->get();

          //dd($data);
          
          return view('so.table-socons', ['data' => $data, 'customer' => $cust, 'item' => $item, 'itemedit' => $item]);
      }
          }
    }

    public function createsoconsweb(Request $req){
      // dd($req->all());
      $data = DB::table('so_mstrs')
                ->where('so_site','=',Session::get('site'))
                ->whereRaw('year(created_at) = "'.Carbon::now()->format('Y').'"')
                ->count();

      $datasite = DB::table('site_mstrs')
                  ->where('site_code','=',Session::get('site'))
                  ->first();

      $site = substr(Session::get('site'),0,2); // Ambil 2 Digit Site Pertama

      //$rn = str_pad($data + 1, 5, '0', STR_PAD_LEFT); // Running Number dari total SO per Site
      $prefix = substr($datasite->r_nbr_cons, 0, 2);
      $rn    = substr($datasite->r_nbr_cons, 2, 4);
      
      if($prefix != Carbon::now()->format('y')){
          // Ganti bulan reset bulan & rn
          $prefix = Carbon::now()->format('y');
          $rn    = '0001';
      }else{
          $rn += 1;
          $rn = str_pad($rn , 4, '0', STR_PAD_LEFT);
      }

      $year = substr(Carbon::now()->format('Y'),3); // digit terakhir Tahun

      $noso = 'K'.$site.$year.$rn;

      // Var Qxtend
      $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
      
      $timeout        = 0;

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
                          <maintainSalesOrder>
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
                                <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

      $qdocbody = '<dsSalesOrder>
                      <salesOrder>
                          <soNbr>'.$noso.'</soNbr>
                          <soCust>'.$req->custcode.'</soCust>
                          <soShip>'.$req->shipto.'</soShip>
                          <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>
                          <soDueDate>'.$req->duedate.'</soDueDate>
                          <soPo>'.$req->po.'</soPo>
                          <confirm>false</confirm>';
                          // <soDetailAll>true</soDetailAll>';

                          $data = DB::table('cust_shipto')
                                      ->where('cust_code','=',$req->custcode)
                                      ->first();
                          $line = 1;
                          $flg  = 0;
                          
                          if($data){
                              // Loc = Shipto
                              foreach($req->barang as $data){
                                      $qdocbody .=  '<salesOrderDetail>'.
                                                            '<line>'.$line.'</line>'.
                                                            '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                            '<sodQtyOrd>'.$req->jumlah[$flg].'</sodQtyOrd>'.
                                                            // '<sodQtyAll>'.$req->jumlah[$flg].'</sodQtyAll>'.
                                                            '<sodUm>'.$req->um[$flg].'</sodUm>'.  
                                                            '<sodLoc>'.$req->shipto.'</sodLoc>'.                     
                                                            //'<sodAcct>'.$slsacct1.'</sodAcct>'.
                                                            //'<sodSub>'.$sub.'</sodSub>'.
                                                    '</salesOrderDetail>';
                                    $line += 1;
                                    $flg += 1;
                              }
                          }else{
                              // Loc = Cust code tampa -
                              foreach($req->barang as $data){
                                        $qdocbody .=  '<salesOrderDetail>'.
                                                              '<line>'.$line.'</line>'.
                                                              '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                              '<sodQtyOrd>'.$req->jumlah[$flg].'</sodQtyOrd>'.
                                                              // '<sodQtyAll>'.$req->jumlah[$flg].'</sodQtyAll>'.
                                                              '<sodUm>'.$req->um[$flg].'</sodUm>'.  
                                                              '<sodLoc>'.str_replace('-', '', $req->custcode).'</sodLoc>'.
                                                              //'<sodAcct>'.$slsacct1.'</sodAcct>'.
                                                              //'<sodSub>'.$sub.'</sodSub>'.
                                                      '</salesOrderDetail>';
                                      $line += 1;
                                      $flg += 1;
                              }
                          }
                            
                            
                          $qdocbody .=   '</salesOrder>
                                          </dsSalesOrder>';

      $qdocfoot = '</maintainSalesOrder>
                      </soapenv:Body>
                   </soapenv:Envelope>';

      $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

      // dd($qdocRequest);

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

      // dd($qdocResult,$qdocResponse,$qdocRequest);

      if($qdocResult=="success" OR $qdocResult=="warning")
      {
        $flgweb = 0;
        $lineweb = 1;

        // update 20112020
        $qty = '';
        $price = '';
        $disc = 0;
        $discdet = 0;
        $pricelist = 0;
        $total = 0;
        $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
        
        // pisahin hasil balikan dari qxtend
        foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
            if(str_contains($data,'P: ')){
              $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
            }elseif(str_contains($data,'Q: ')){
              $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
            }elseif(str_contains($data, 'D: ')){
              $disc = substr($data, stripos($data, 'D:') + 3). ',';
            }elseif(str_contains($data, 'C: ')){
              $discdet .= substr($data, stripos($data, 'C:') + 3). ',';
            }elseif(str_contains($data, 'X: ')){
              $pricelist .= substr($data, stripos($data, 'X:') + 3). ',';
            }
        }
        
        
        $price = explode(',', substr($price, 0, -1));
        $qty   = explode(',', substr($qty, 0, -1));
        $discdet = explode(',', substr($discdet, 0, -1));
        $pricelist = explode(',', substr($pricelist, 0, -1));
        $disc  = substr($disc, 0, -1);
        $flg = 0;

        if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
          // Qxtend Tidak terima harga / disc / qty

          Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from .p for SO Number : '.$noso.'-'.Session::get('username'));

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
              '<insonbr>'.$noso.'</insonbr>'.
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

          $dataloop    = $xmlResp->xpath('//ns1:tempRow');
          $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
          
          if ($qdocResultx == 'true')  {
            $flgarr = 0;
            foreach($dataloop as $data){
              $price[$flgarr] = $data->t_netprice;
              $discdet[$flgarr] = $data->t_discdet;
              $qty[$flgarr] = $data->t_qtyord;
              $pricelist[$flgarr] = $data->t_pricelist;
              $disc = $data->t_dischead;
            
              $flgarr += 1;
            }
          }else{
            Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from WSA for SO Number : '.$noso.'-'.Session::get('username'));
          }
        }


        //dd($price,$qty,$disc,$discdet,$qdocResponse,$xmlResp->xpath('//ns3:tt_msg_desc'));

        // itung total harga nett line
        foreach($price as $s){
          $total += trim($price[$flg]) * trim($qty[$flg]);
          $flg += 1;
        }

        // itung total nett disc master
        if($disc != 0){
          $total = $total - $total * $disc / 100;
        }

        // Data berhasil terbuat di QAD               
        $so_nbr = (string) $xmlResp->xpath('//ns1:soNbr')[0];

        //dd($pricelist,$price,$discdet);

        // Insert SO QAD ke web
        DB::table('so_mstrs')
                ->insert([
                    'so_nbr' => $noso,
                    'so_cust' => $req->custcode,
                    'so_shipto' => $req->shipto,
                    'so_duedate' => $req->duedate,
                    'so_status' => 10, // 10 Consignment
                    'so_site' => Session::get('site'), // Cek Session User
                    'so_price' => $total,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]);


        foreach($req->barang as $barang){
                DB::table('so_dets')
                    ->insert([
                        'so_nbr' => $noso,
                        'so_line' => $lineweb,
                        'so_itemcode' => $req->barang[$flgweb],
                        'so_qty' => $req->jumlah[$flgweb],
                        'so_um' => $req->um[$flgweb],
                        'so_harga' => trim($price[$flgweb]),
                        'so_disc' => trim($discdet[$flgweb]),
                        'so_status' => 1, // 1 New Retur
                        'so_pr_list' => trim($pricelist[$flgweb]),
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ]);
                $lineweb += 1;
                $flgweb += 1;
        }

        // Update RN SO
                    
        db::table('site_mstrs')
                    ->where('site_code','=',Session::get('site'))
                    ->update([
                     'r_nbr_cons' => $prefix.$rn  
                    ]);

        session()->flash('updated','SO Consignment Succesfully Created with Number : '.$noso);
        return back();
      }else{
        $resultProcess  = false;
        $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
        $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
        $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
        $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


        //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev,$qdocResult);
        Log::channel('customlog')->info('Create SO Consignment Failed to send to QAD , error :'.$qdocMsgDesc.'--'.$qdocMsgData.'--'.$qdocMsgSev.'-'.Session::get('username'));
        session()->flash('error','SO Consignment Failed');
        return back();
      }
    }

    public function detaileditcons(Request $req){
      if($req->ajax()){
          $data = DB::table('so_mstrs')
                    ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
                    ->join('items','items.itemcode','=','so_dets.so_itemcode')
                    ->where('so_mstrs.so_nbr','=',$req->sonbr)
                    ->get();



            $output = '';
            $qtyship = 0;
            $qtycan = 0;
            foreach($data as $data){
                $item = DB::table('items')
                    ->where('itemcode','=',$data->so_itemcode)
                    ->first();

                $konv = DB::table('item_konversi')
                            ->where('item_code','=',$data->so_itemcode)
                            ->where('um_1','=',$item->item_um)
                            ->get();

                $list = array($item->item_um);

                if(!$konv->isEmpty()){
                    foreach($konv as $konv){
                        array_push($list, $konv->um_2);
                    }
                }

                $output .=  '<tr>'.
                            '<td> <input type="text" class="form-control" value="'.$data->so_itemcode.' - '.$data->itemdesc.'" readonly> </td>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_itemcode.'" name="itemcode[]" readonly>'.
                            '<input type="hidden" class="form-control" value="'.$data->so_line.'" name="line[]" readonly>'.
                            '<td> <input type="number" min="'.$qtyship.'" step="0.01" class="form-control qtyso" value="'.$data->so_qty.'" name="qtyso[]"> </td>'.
                            '<td>'.
                            '<select id="um" class="form-control um" name="um[]" required autofocus>';
                            foreach($list as $list):
                            if($list == $data->so_um):
                            $output .= '<option value="'.$list.'" selected>'.$list.'</option>';
                            else:
                            $output .= '<option value="'.$list.'">'.$list.'</option>';
                            endif;
                            endforeach;
                            $output .= '</select>'.
                            '</td>'.
                            '<td style="vertical-align:middle;text-align:center;"> <input type="checkbox" class="qaddel" value=""> </td>'.
                            '<input type="hidden" name="delLine[]" class="defdel" value="M">'.
                            '<tr>';
            
            }

            return response($output);

      }
    }

    public function editsoconsweb(Request $req){
      //dd($req->all());
      // Var Qxtend
      $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
      
      $timeout        = 0;

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
                          <maintainSalesOrder>
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
                                <qcom:propertyValue>ERP3_2</qcom:propertyValue>
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

      $qdocbody = '<dsSalesOrder>
                      <salesOrder>
                          <soNbr>'.$req->ed_sonbr.'</soNbr>
                          <soCust>10-01458</soCust>
                          <soShip>'.$req->ed_shipto.'</soShip>
                          <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>
                          <soDueDate>'.$req->ed_duedate.'</soDueDate>';
                          // <soDetailAll>true</soDetailAll>';

                          $data = DB::table('cust_shipto')
                                      ->where('cust_code','=',$req->custcode)
                                      ->first();
                          $line = 1;
                          $flg  = 0;

                          $newline = array_values(array_slice($req->line, -1))[0]; // line terakhir web
                          
                          if($data){
                              // Loc = Shipto
                              foreach($req->itemcode as $data){
                                if($req->delLine[$flg] == 'A'){
                                  $newline += 1;

                                  $qdocbody .=    '<salesOrderDetail>'.
                                                            '<operation>'.$req->delLine[$flg].'</operation>'.
                                                            '<line>'. $newline  .'</line>'.
                                                            '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                            '<sodQtyOrd>'.$req->qtyso[$flg].'</sodQtyOrd>'.   
                                                            // '<sodQtyAll>'.$req->qtyso[$flg].'</sodQtyAll>'.
                                                            '<sodUm>'.$req->um[$flg].'</sodUm>'.  
                                                            '<sodLoc>'.$req->shipto.'</sodLoc>'.
                                                            //'<allocationDetail>'.
                                                            //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                            //    '<ladQtyAll>'.$req->qtyso[$flg].'</ladQtyAll>'.
                                                            //'</allocationDetail>'.    
                                                    '</salesOrderDetail>';
                                }else{
                                  $qdocbody .=    '<salesOrderDetail>'.
                                                            '<operation>'.$req->delLine[$flg].'</operation>'.
                                                            '<line>'.$req->line[$flg].'</line>'.
                                                            '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                            '<sodQtyOrd>'.$req->qtyso[$flg].'</sodQtyOrd>'.  
                                                            // '<sodQtyAll>'.$req->qtyso[$flg].'</sodQtyAll>'.
                                                            '<sodUm>'.$req->um[$flg].'</sodUm>'.  
                                                            '<sodLoc>'.$req->shipto.'</sodLoc>'.
                                                            //'<allocationDetail>'.
                                                            //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                            //    '<ladQtyAll>'.$req->qtyso[$flg].'</ladQtyAll>'.
                                                            //'</allocationDetail>'.    
                                                    '</salesOrderDetail>';
                                }
                                $flg += 1;
                              }
                          }else{
                              // Loc = Cust code tampa -
                              foreach($req->itemcode as $data){
                                if($req->delLine[$flg] == 'A'){
                                  $newline += 1;

                                  $qdocbody .=    '<salesOrderDetail>'.
                                                            '<operation>'.$req->delLine[$flg].'</operation>'.
                                                            '<line>'. $newline  .'</line>'.
                                                            '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                            '<sodQtyOrd>'.$req->qtyso[$flg].'</sodQtyOrd>'.   
                                                            // '<sodQtyAll>'.$req->qtyso[$flg].'</sodQtyAll>'.
                                                            '<sodUm>'.$req->um[$flg].'</sodUm>'.   
                                                            '<sodLoc>'.str_replace('-', '', $req->custcode).'</sodLoc>'.
                                                            //'<allocationDetail>'.
                                                            //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                            //    '<ladQtyAll>'.$req->qtyso[$flg].'</ladQtyAll>'.
                                                            //'</allocationDetail>'.    
                                                    '</salesOrderDetail>';
                                }else{
                                  $qdocbody .=    '<salesOrderDetail>'.
                                                            '<operation>'.$req->delLine[$flg].'</operation>'.
                                                            '<line>'.$req->line[$flg].'</line>'.
                                                            '<sodPart>'.$req->itemcode[$flg].'</sodPart>'.
                                                            '<sodQtyOrd>'.$req->qtyso[$flg].'</sodQtyOrd>'.  
                                                            // '<sodQtyAll>'.$req->qtyso[$flg].'</sodQtyAll>'.
                                                            '<sodUm>'.$req->um[$flg].'</sodUm>'.   
                                                            '<sodLoc>'.str_replace('-', '', $req->custcode).'</sodLoc>'.
                                                            //'<allocationDetail>'.
                                                            //    '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                            //    '<ladQtyAll>'.$req->qtyso[$flg].'</ladQtyAll>'.
                                                            //'</allocationDetail>'.    
                                                    '</salesOrderDetail>';
                                }
                                $flg += 1;
                              }
                          }
                            
                            
                          $qdocbody .=   '</salesOrder>
                                          </dsSalesOrder>';

      $qdocfoot = '</maintainSalesOrder>
                      </soapenv:Body>
                   </soapenv:Envelope>';

      $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

      // dd($qdocResult,$qdocResponse,$qdocRequest);

      if($qdocResult=="success" OR $qdocResult=="warning")
      {
        $qty = '';
        $price = '';
        $disc = 0;
        $discdet = 0;
        $pricelist = 0;
        $total = 0;
        $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
        
        // pisahin hasil balikan dari qxtend
        foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
            if(str_contains($data,'P: ')){
              $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
            }elseif(str_contains($data,'Q: ')){
              $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
            }elseif(str_contains($data, 'D: ')){
              $disc = substr($data, stripos($data, 'D:') + 3). ',';
            }elseif(str_contains($data, 'C: ')){
              $discdet .= substr($data, stripos($data, 'C:') + 3). ',';
            }elseif(str_contains($data, 'X: ')){
              $pricelist .= substr($data, stripos($data, 'X:') + 3). ',';
            }
        }
        
        
        $price = explode(',', substr($price, 0, -1));
        $qty   = explode(',', substr($qty, 0, -1));
        $discdet = explode(',', substr($discdet, 0, -1));
        $pricelist = explode(',', substr($pricelist, 0, -1));
        $disc  = substr($disc, 0, -1);
        $flg = 0;

        if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
          // Qxtend Tidak terima harga / disc / qty

          Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from .p for SO Number : '.$req->ed_sonbr.'-'.Session::get('username'));

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
              '<insonbr>'.$req->ed_sonbr.'</insonbr>'.
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

          $dataloop    = $xmlResp->xpath('//ns1:tempRow');
          $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
          
          if ($qdocResultx == 'true')  {
            $flgarr = 0;
            foreach($dataloop as $data){
              $price[$flgarr] = $data->t_netprice;
              $discdet[$flgarr] = $data->t_discdet;
              $qty[$flgarr] = $data->t_qtyord;
              $pricelist[$flgarr] = $data->t_pricelist;
              $disc = $data->t_dischead;
            
              $flgarr += 1;
            }
          }else{
            Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from WSA for SO Number : '.$req->ed_sonbr.'-'.Session::get('username'));
          }
        }

        // itung total harga nett line
        foreach($price as $s){
          $total += trim($price[$flg]) * trim($qty[$flg]);
          $flg += 1;
        }

        // itung total nett disc master
        if($disc != 0){
          $total = $total - $total * $disc / 100;
        }

        DB::table('so_mstrs')
                    ->where('so_nbr','=',$req->ed_sonbr)
                    ->update([
                          'so_duedate' => $req->ed_duedate,
                          'so_price' => $total,
                          'updated_at' => Carbon::now()->toDateTimeString()
                    ]);

        // update data detail
          $flgweb = 0;
          $flghrga = 0;
          $lineweb = array_values(array_slice($req->line, -1))[0]; // line terakhir web
          foreach($req->itemcode as $barang){

              if($req->delLine[$flgweb] == 'R'){
                // hapus row
                DB::table('so_dets')
                      ->where('so_nbr','=',$req->ed_sonbr)
                      ->where('so_itemcode','=',$req->itemcode[$flgweb])
                      ->where('so_line','=',$req->line[$flgweb])
                      ->delete();

              }elseif($req->delLine[$flgweb] == 'A'){
                      DB::table('so_dets')
                          ->insert([
                              'so_nbr' => $req->ed_sonbr,
                              'so_itemcode' => $req->itemcode[$flgweb],
                              'so_line' => $lineweb + 1,
                              'so_qty' => $req->qtyso[$flgweb],
                              'so_status' => '1',
                              'so_um' => $req->um[$flgweb],
                              'so_harga' => trim($price[$flghrga]),
                              'so_disc' => trim($discdet[$flghrga]),
                              'so_pr_list' => trim($pricelist[$flghrga]),
                              'created_at' => Carbon::now()->toDateTimeString(),
                              'updated_at' => Carbon::now()->toDateTimeString()
                          ]);

                      $lineweb += 1;
                      $flghrga += 1;
              }else{
                      DB::table('so_dets')
                            ->where('so_nbr','=',$req->ed_sonbr)
                            ->where('so_itemcode','=',$req->itemcode[$flgweb])
                            ->where('so_line','=',$req->line[$flgweb])
                            ->update([
                              'so_qty' => $req->qtyso[$flgweb],
                              'so_um' => $req->um[$flgweb],
                              'so_harga' => trim($price[$flghrga]),
                              'so_disc' => trim($discdet[$flghrga]),
                              'so_pr_list' => trim($pricelist[$flghrga]),
                              'updated_at' => Carbon::now()->toDateTimeString()
                            ]);
                      //dd('1233');
                      $flghrga += 1;
              }

              
                  $flgweb += 1;
          }
      
        session()->flash('updated','SO Consignment Succesfully Updated. SO Number : '.$req->ed_sonbr);
        return back();
      }else{
        $resultProcess  = false;
        $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
        $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
        $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
        $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


        //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev,$qdocResult);
        Log::channel('customlog')->info('Edit SO Consignment Failed to send to QAD , error :'.$qdocMsgDesc.'--'.$qdocMsgData.'--'.$qdocMsgSev.'-'.Session::get('username'));
        session()->flash('error','SO Consignment Failed Updated. SO Number : '.$req->ed_sonbr);
        return back();
      }
    }

    public function confirmsocons(Request $req){

    	// dd($req->all());
	   /*Cek Wsa*/
	  	$datadetail = DB::table('so_mstrs')
	  						->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
	  						->where('so_dets.so_nbr','=',$req->h_edw_sonbr)
	  						->get();

	  	// dd($datadetail);
	  	$jmlerr = 0;
	  	$item = "";

	  	foreach($datadetail as $tmp){
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
			$qdocRequest =   	'<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
								'<Body>'.
								'<consstock xmlns="urn:iris.co.id:wsatrain">'.
								'<inpdomain>'.$domain.'</inpdomain>'.
								'<inppart>'.$tmp->so_itemcode.'</inppart>'.
								'<inploc>'.str_replace('-', '', $tmp->so_shipto).'</inploc>'.
                '<inpum>'.$tmp->so_um.'</inpum>'.
								'</consstock>'.
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


			$dataloop    = $xmlResp->xpath('//ns1:tempRow');
			$qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];

      // dd($dataloop, $qdocResponse, $tmp);

			if(empty($dataloop)){
				$jmlerr += 1;
				$item .= $tmp->so_itemcode.', ';
			}else{
				if($dataloop[0]->t_qty < $tmp->so_qty * $dataloop[0]->t_konv){
					$jmlerr += 1;
					$item .= $tmp->so_itemcode.', ';
				}
			}
			

	  	}

	  	// dd($jmlerr);

	  	if($jmlerr > 0){
          session()->flash('error','Terdapat Stok yang kurang di QAD untuk item : '.substr($item, 0, -2));
          return back();
	  	}


      // sosoco.p --> Bkin jadi confirm = yes
        $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
              
        $timeout        = 0;

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
                              <qcom:suppressResponseDetail>true</qcom:suppressResponseDetail>
                            </wsa:ReferenceParameters>
                            <wsa:ReplyTo>
                              <wsa:Address>urn:services-qad-com:</wsa:Address>
                            </wsa:ReplyTo>
                          </soapenv:Header>
                          <soapenv:Body>
                            <dkhsoconfx>
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
                                  <qcom:propertyValue>cust_1</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                  <qcom:propertyName>mnemonicsRaw</qcom:propertyName>
                                  <qcom:propertyValue>false</qcom:propertyValue>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                  <qcom:propertyName>admin</qcom:propertyName>
                                  <qcom:propertyValue/>
                                </qcom:ttContext>
                                <qcom:ttContext>
                                  <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                  <qcom:propertyName>XVytW</qcom:propertyName>
                                  <qcom:propertyValue/>
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

        $qdocbody = '       <dsDkhsoconf>
                              <dkhsoconf>
                                <sonbr>'.$req->h_edw_sonbr.'</sonbr>
                                <sonbr1>'.$req->h_edw_sonbr.'</sonbr1>
                                <soallocate>true</soallocate>
                                <soatpwarn>false</soatpwarn>
                                <soatperr>false</soatperr>
                                <sopromdate>false</sopromdate>
                                <dev>aa</dev>
                              </dkhsoconf>
                            </dsDkhsoconf>';

        $qdocfoot = '</dkhsoconfx>
                      </soapenv:Body>
                    </soapenv:Envelope>';

        $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

        // dd($qdocResponse);


      // Qxtend ilangin action status --> Jaga"
	      $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
	            
	      $timeout        = 0;

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
	                          <qcom:suppressResponseDetail>true</qcom:suppressResponseDetail>
	                        </wsa:ReferenceParameters>
	                        <wsa:ReplyTo>
	                          <wsa:Address>urn:services-qad-com:</wsa:Address>
	                        </wsa:ReplyTo>
	                      </soapenv:Header>
	                      <soapenv:Body>
	                        <maintainSalesOrderCredit>
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
	                              <qcom:propertyValue>eB_2</qcom:propertyValue>
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

	      $qdocbody = '       <dsSalesOrderCredit>
	                            <salesOrderCredit>
	                              <soNbr>'.$req->h_edw_sonbr.'</soNbr>
	                              <soStat></soStat>
	                            </salesOrderCredit>
	                          </dsSalesOrderCredit>';

	      $qdocfoot = '</maintainSalesOrderCredit>
	                    </soapenv:Body>
	                   </soapenv:Envelope>';

	      $qdocRequest = $qdocHead.$qdocbody.$qdocfoot;

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

	     /*SO Shipment*/
      $datadet = DB::table('so_mstrs')
                  ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
                  ->where('so_mstrs.so_nbr','=',$req->h_edw_sonbr)
                  ->get();

      $datahead = DB::table('so_mstrs')
                  ->where('so_mstrs.so_nbr','=',$req->h_edw_sonbr)
                  ->first();

      // Var Qxtend
      $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
      $slsacct1       = '4000'; // Khusus Demo Internal
      $sub            = 'mech'; // Khusus Demo Internal
      $timeout        = 0;
      $line           = 1;

      // XML Qxtend

      $qdocHeader = '<?xml version="1.0" encoding="UTF-8"?>
                         <soapenv:Envelope xmlns="urn:schemas-qad-com:xml-services"
                          xmlns:qcom="urn:schemas-qad-com:xml-services:common"
                          xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsa="http://www.w3.org/2005/08/addressing">
                          <soapenv:Header>
                          <wsa:Action/>
                          <wsa:To>urn:services-qad-com:QX_DNP</wsa:To>
                          <wsa:MessageID>urn:services-qad-com::QX_DNP</wsa:MessageID>
                          <wsa:ReferenceParameters>
                            <qcom:suppressResponseDetail>true</qcom:suppressResponseDetail>
                          </wsa:ReferenceParameters>
                          <wsa:ReplyTo>
                            <wsa:Address>urn:services-qad-com:</wsa:Address>
                          </wsa:ReplyTo>
                          </soapenv:Header>
                          <soapenv:Body>
                          <shipSalesOrder>
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
                                      <qcom:propertyValue>ERP3_2</qcom:propertyValue>
                                  </qcom:ttContext>
                                  <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>mnemonicsRaw</qcom:propertyName>
                                      <qcom:propertyValue>false</qcom:propertyValue>
                                  </qcom:ttContext>
                                  <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>username</qcom:propertyName>
                                      <qcom:propertyValue>Admin</qcom:propertyValue>
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

      $qdocBody = '<dsSalesOrderShipment>
                <SalesOrderShipment>
                    <soNbr>'.$req->h_edw_sonbr.'</soNbr>';

                    $data = DB::table('cust_shipto')
                                      ->where('cust_code','=',$datahead->so_cust)
                                      ->first();

                    if($data){
                        // Loc = Shipto
                        foreach($datadet as $data){

                        		$cek = DB::table('itemchilds')
                        				->where('item_code','=',$data->so_itemcode)
                        				->get();

                        		if($cek->count() > 0){
                        				$qdocBody .=  '<lineDetail>'.
                                                      '<line>'.$data->so_line.'</line>'.
                                                      '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
                                                      '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
    												  '<pickLogic>false</pickLogic>'.
                                                      '<yn>true</yn>'.
                                                      '<yn1>true</yn1>';
                                            foreach($cek as $cek){
	        								$qdocBody .=	'<itemNumber>'.   
	        													'<pkPart>'.$cek->item_child.'</pkPart>'.
													              '<itemDetail>'.
                													'<part>'.$cek->item_child.'</part>'.
													                // '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
													                '<site>'.Session::get('site').'</site>'.
													                '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
													              '</itemDetail>'.
	        												'</itemNumber>'; 
                        					}

                                        $qdocBody .=  '</lineDetail>';
                          				$line += 1;
                        		}else{
                        				$qdocBody .=  '<lineDetail>'.
                                                      '<line>'.$data->so_line.'</line>'.
                                                      '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
                                                      '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
    												  '<pickLogic>false</pickLogic>'.
                                                      '<yn>true</yn>'.
                                                      '<yn1>true</yn1>'. 
	        												'<itemNumber>'.   
	        													'<pkPart>'.$data->so_itemcode.'</pkPart>'.
													              '<itemDetail>'.
                													'<part>'.$data->so_itemcode.'</part>'.
													                // '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
													                '<site>'.Session::get('site').'</site>'.
													                '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
													              '</itemDetail>'.
	        												'</itemNumber>'.    
                                              '</lineDetail>';
                          				$line += 1;
                        		}
                                
                        }
                    }else{
                        foreach($datadet as $data){

                        		$cek = DB::table('itemchilds')
                        				->where('item_code','=',$data->so_itemcode)
                        				->get();

                        		if($cek->count() > 0){
                        				$qdocBody .=  '<lineDetail>'.
                                                      '<line>'.$data->so_line.'</line>'.
                                                      '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
                                                      '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
    												  '<pickLogic>false</pickLogic>'.
                                                      '<yn>true</yn>'.
                                                      '<yn1>true</yn1>';
                                            foreach($cek as $cek){
	        								$qdocBody .=	'<itemNumber>'.   
	        													'<pkPart>'.$cek->item_child.'</pkPart>'.
													              '<itemDetail>'.
                													'<part>'.$cek->item_child.'</part>'.
													                // '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
													                '<site>'.Session::get('site').'</site>'.
													                '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
													              '</itemDetail>'.
	        												'</itemNumber>'; 
                        					}

                                        $qdocBody .=  '</lineDetail>';
                          				$line += 1;
                        		}else{
                        				$qdocBody .=  '<lineDetail>'.
                                                      '<line>'.$data->so_line.'</line>'.
                                                      '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
                                                      '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
    												  '<pickLogic>false</pickLogic>'.
                                                      '<yn>true</yn>'.
                                                      '<yn1>true</yn1>'. 
	        												'<itemNumber>'.   
	        													'<pkPart>'.$data->so_itemcode.'</pkPart>'.
													              '<itemDetail>'.
                													'<part>'.$data->so_itemcode.'</part>'.
													                // '<lotserialQty>'.$data->so_qty.'</lotserialQty>'.
													                '<site>'.Session::get('site').'</site>'.
													                '<location>'.str_replace('-', '', $data->so_shipto).'</location>'.
													              '</itemDetail>'.
	        												'</itemNumber>'.    
                                              '</lineDetail>';
                          				$line += 1;
                        		}
                                
                        }
                                
                    }
                    
      $qdocfooter =   '</SalesOrderShipment> 
                      </dsSalesOrderShipment>
                              </shipSalesOrder>
                              </soapenv:Body>
                              </soapenv:Envelope>';


      $qdocRequest = $qdocHeader.$qdocBody.$qdocfooter;

      // dd($qdocRequest,$data,$datadet);

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

      // dd($qdocResponse,$qdocResult,$qdocRequest);


      if($qdocResult=="success" OR $qdocResult=="warning")
      {
          $qty = '';
          $price = '';
          $disc = 0;
          $discdet = 0;
          $total = 0;
          $lineweb = 1;
          $pricelist = 0;
          $flgweb = 0;
          $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
          
          // pisahin hasil balikan dari qxtend
          foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
              if(str_contains($data,'P: ')){
                $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
              }elseif(str_contains($data,'Q: ')){
                $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
              }elseif(str_contains($data, 'D: ')){
                $disc = substr($data, stripos($data, 'D:') + 3). ',';
              }elseif(str_contains($data, 'C: ')){
                $discdet .= substr($data, stripos($data, 'C:') + 3). ',';
              }elseif(str_contains($data, 'X: ')){
                $pricelist .= substr($data, stripos($data, 'X:') + 3). ',';
              }
          }

          $price = explode(',', substr($price, 0, -1));
          $qty   = explode(',', substr($qty, 0, -1));
          $discdet   = explode(',', substr($discdet, 0, -1));
          $pricelist   = explode(',', substr($pricelist, 0, -1));
          $disc  = substr($disc, 0, -1);
          $flg = 0;
          $flg1 = 0;
          //dd($price, $discdet, $qty, $disc,$qdocResponse);

          if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
            // Qxtend Tidak terima harga / disc / qty

            Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from .p for SO Number : '.$req->h_edw_sonbr.'-'.Session::get('username'));

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
                '<insonbr>'.$req->h_edw_sonbr.'</insonbr>'.
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

            $dataloop    = $xmlResp->xpath('//ns1:tempRow');
            $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
            
            if ($qdocResultx == 'true')  {
              $flgarr = 0;
              foreach($dataloop as $data){
                $price[$flgarr] = $data->t_netprice;
                $discdet[$flgarr] = $data->t_discdet;
                $qty[$flgarr] = $data->t_qtyord;
                $pricelist[$flgarr] = $data->t_pricelist;
                $disc = $data->t_dischead;
              
                $flgarr += 1;
              }
            }else{
              Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from WSA for SO Number : '.$req->h_edw_sonbr.'-'.Session::get('username'));
            }
          }

          // itung total harga nett line
          if($price != ''){
            foreach($price as $s){
              $total += trim($price[$flg]) * trim($qty[$flg]);
              $flg += 1;
              DB::table('so_dets')
                    ->where('so_dets.so_nbr','=',$req->h_edw_sonbr)
                    ->where('so_dets.so_line','=',$flg)
                    ->update([
                        'so_harga' => trim($price[$flg1]),
                        'so_disc' => trim($discdet[$flg1]),
                        'so_pr_list' => trim($pricelist[$flg1]),
                    ]);
              $flg1 += 1;
            }

            // itung total nett disc master
            if($disc != 0){
              $total = $total - $total * $disc / 100;
            }
          }
          

          // Insert SO QAD ke web
          DB::table('so_mstrs')
                  ->where('so_mstrs.so_nbr','=',$req->h_edw_sonbr)
                  ->update([
                      'so_status' => 11, // 10 Consignment 11 Confirm
                      'so_price' => $total,
                      'updated_at' => Carbon::now()->toDateTimeString(),
                  ]);


          Log::channel('customlog')->info('SO Consignment : '.$req->h_edw_sonbr.' Successfully Created '.Session::get('username'));
          session()->flash('updated','SO Consignment Succesfully Created with Number : '.$req->h_edw_sonbr);
          return back();
      }else{
            $resultProcess  = false;
            $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
            $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
            $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
            $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


            //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev,$qdocResult);
            Log::channel('customlog')->info('Confirm SO Consignment Failed to send to QAD , error :'.$qdocMsgDesc.'--'.$qdocMsgData.'--'.$qdocMsgSev.'-'.Session::get('username'));

            Log::channel('customlog')->info('SO Consignment Failed to send to QAD '.Session::get('username'));
            session()->flash('error','SO Consignment Failed to send to QAD');
            return back();
      }
    }

    public function detailsalescons(Request $req){
      
      if($req->ajax()){
        $data = DB::table('so_mstrs')
              ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
              ->join('items','items.itemcode','=','so_dets.so_itemcode')
              ->where('so_mstrs.so_nbr','=',$req->sonbr)
              ->get();

        if(!$data->isEmpty()){
          $output = '';
          foreach($data as $data){

            // 11 Januari 2021
            $hargadet = 0;
            $total = 0;

            if(strpos($data->so_harga,".00000") !== false){
                $hargadet = number_format($data->so_harga,2,'.',',');
                $total = number_format(floor($data->so_harga * $data->so_qty),0,'.',',');
            }else{
                if(strpos(strrev(rtrim(($data->so_harga), "0")), ".") == 1){
                    $hargadet = number_format($data->so_harga,2,'.',',');
                }else{
                    $hargadet = rtrim(number_format($data->so_harga,5,'.',','), "0");
                }

                if(strpos(strrev(rtrim(($data->so_harga * $data->so_qty), "0")), ".") == 1){
                    $total = number_format(floor($data->so_harga * $data->so_qty),0,'.',',');
                }else{
                    $total = rtrim(number_format(floor($data->so_harga * $data->so_qty),0,'.',','), "0");
                }
            }



            $output .= '<tr class="foottr">'.

                   '<td class="foot1" data-label="Item">'.$data->itemcode.' - '.$data->itemdesc.'</td>'.
                   '<td class="foot2" data-label="Qty">'.$data->so_qty.'</td>'.
                   '<td class="foot2" data-label="Um">'.$data->so_um.'</td>'.
                   '<td class="foot2" data-label="Price">'.number_format($data->so_pr_list,0,'.',',').
                   '<td class="foot2" data-label="Qty">'.$data->so_disc.'</td>'.
                   '<td class="foot2" data-label="Total">'.$total.
                   '</td>'.


                       '</tr>';
          }

          return response($output);
        }

      }
    }

    public function deletesocons(Request $req){
      //dd($req->all());
      DB::table('so_mstrs')
              ->where('so_nbr','=',$req->de_sonbr)
              ->update([
                  'so_status' => 12 // 10 Consignment 11 Confirm 12 Deleted
              ]);

      Session()->flash('updated','SO Succesfully Deleted');
      return back();
    }


    // ----------------- Paging Search

    public function sadpagination(Request $req){
      // dd($req->all());
      if ($req->ajax()) {
        $sonumber = $req->get('sonumber');
        $customer = $req->get('customer');
        $totalstart = $req->get('totalstart');
        $totalto = $req->get('totalto');
        $status = $req->get('status');
        $datefrom = $req->get('duedatefrom');
        $dateto = $req->get('duedateto');
        $sort_by = $req->get('sortby');
        $sort_type = $req->get('sorttype');
        $site = $req->get('site');


        if ($sonumber == '' and $customer == '' and $totalstart == '' and $totalto == '' and $status == '' and $datefrom == '' and $dateto == '' and $site == ''){
            // dd('aaaa');
            if(Session::get('pusat_cabang')==1){
            $data = DB::table('so_mstrs')
              ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
	            ->where('so_status', '!=', 10)
              ->where('so_status','!=',11)
              ->where('so_status','!=',12)
              ->selectRaw('*,so_mstrs.created_at as "so_created"')
              ->orderBy('so_mstrs.so_nbr','Desc')
              ->paginate(10);
            }
            else if (Session::get('pusat_cabang')==0){
              $data = DB::table('so_mstrs')
              ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
	            ->where('so_status', '!=', 10)
              ->where('so_status','!=',11)
              ->where('so_status','!=',12)
              ->selectRaw('*,so_mstrs.created_at as "so_created"')
              ->where('so_site','=',Session::get('site'))
              ->orderBy('so_mstrs.so_nbr','Desc')
              ->paginate(10);
            }

              $cust = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
    				    ->get();

    	        $item = DB::table('items')
    				    ->get();
              
                return view('so.table-sosad', ['data' => $data, 'customer' => $cust, 'item' => $item, 'itemedit' => $item]);
        } else {
            if($datefrom == null){
              $datefrom = '2000-01-01';
            }
            if($dateto == null){
              $dateto = '3000-12-31';
            }
            if($totalstart == null){
              $totalstart = 0;
            }
            if($totalto == null){
              $totalto = 999999999999;
            }

            // dd($dateto);

            $kondisi = "so_duedate BETWEEN '".$datefrom."' and '".$dateto."' and so_price BETWEEN '".$totalstart."' and '".$totalto."' ";

            if ($sonumber != '') {
                $kondisi .= ' and so_nbr = "' . $sonumber . '"';
                // dd($kondisi);
            }
            if ($customer != '') {
                $kondisi .= ' and customers.cust_code = "' . $customer . '"';
            }
            if ($site != '') {
               $kondisi .= ' and so_mstrs.so_site = "'.$site.'"';
            }

            if ($status != '') {
                if($status == '2'){
                  $kondisi .= ' and (so_status = "2" or so_status = "3" or so_status = "4")';
                }else{
                  $kondisi .= ' and so_status = "' . $status . '"';
                }
                
            }

            // dd($kondisi);
            if(Session::get('pusat_cabang')==1){
              $data = DB::table('so_mstrs')
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->whereRaw($kondisi)
                ->where('so_status', '!=', 10)
                ->where('so_status','!=',11)
                ->where('so_status','!=',12)
                ->selectRaw('*,so_mstrs.created_at as "so_created"')
                ->orderBy('so_mstrs.so_nbr','Desc')
                ->paginate(10);
              }
              else if (Session::get('pusat_cabang')==0){
                $data = DB::table('so_mstrs')
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->whereRaw($kondisi)
		            ->where('so_status', '!=', 10)
                ->where('so_status','!=',11)
                ->where('so_status','!=',12)
                ->selectRaw('*,so_mstrs.created_at as "so_created"')
                ->where('so_site','=',Session::get('site'))
                ->orderBy('so_mstrs.so_nbr','Desc')
                ->paginate(10);
              }
            

            $cust = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
    				    ->get();

    	      $item = DB::table('items')
    				    ->get();

            //dd($data);
            
            return view('so.table-sosad', ['data' => $data, 'customer' => $cust, 'item' => $item, 'itemedit' => $item]);
        }
      }
    }

  	public function retursearching(Request $req){
  	      $returnbr = $req->get('returnumber');
  	      
  	      $customer = $req->get('customer');
  	      $site = $req->get('site');
  	      $shipto = $req->get('shipto');
  		
  	      if ($customer == '' and $returnbr == '' and $site == '' and $shipto == ''){
      		// dd('aaaa');
      		$data = DB::table('retur_mstrs')
                  ->join('customers','customers.cust_code','=','retur_mstrs.so_cust')
                  ->leftjoin('cust_shipto as cust_ship','cust_ship.cust_code','=','retur_mstrs.so_shipto')
                  ->selectRaw('so_nbr,so_cust,so_shipto,so_so_awal,so_site,cust_ship.custname as "shipto_nama",customers.cust_desc,so_remarks,retur_mstrs.so_status,retur_mstrs.price_date')
                  ->where('so_site','=',Session::get('site'))
                  ->orderBy('retur_mstrs.created_at','Desc')
                  ->paginate(10);
      		 //dd($data);
      		    return view('so.table-retur', ['data' => $data]);
      	      } else {
      		//dd('aaaa');

      		$kondisi = "so_nbr != ''";

      		if ($customer != '') {
      		    $kondisi .= ' and retur_mstrs.so_cust = "' . $customer . '"';
      		}
      		if ($returnbr != '') {
      		    $kondisi .= ' and so_nbr = "' . $returnbr . '"';
      		}
      		if ($shipto != '') {
      		  $kondisi .= ' and  retur_mstrs.so_shipto = "' . $shipto . '"';
      		}
      		if ($site != '') {
      		  $kondisi .= ' and  retur_mstrs.so_site = "' . $site . '"';
      		}

          $data = DB::table('retur_mstrs')
                  ->join('customers','customers.cust_code','=','retur_mstrs.so_cust')
                  ->leftjoin('cust_shipto as cust_ship','cust_ship.cust_code','=','retur_mstrs.so_shipto')
                  ->selectRaw('so_nbr,so_cust,so_shipto,so_so_awal,so_site,cust_ship.custname as "shipto_nama",customers.cust_desc,so_remarks,retur_mstrs.so_status,retur_mstrs.price_date')
                  ->where('so_site','=',Session::get('site'))
                  ->whereRaw($kondisi)
                  ->orderBy('retur_mstrs.created_at','Desc')
                  ->paginate(10);

      		//dd($data);
      		
      		return view('so.table-retur', ['data' => $data]);
  	      }
    }





    // HTTP Header WSA
    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',// jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

}

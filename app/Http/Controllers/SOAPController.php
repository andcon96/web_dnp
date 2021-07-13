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
use Artisaninweb\SoapWrapper\SoapWrapper;

class SOAPController extends Controller
{
    //
    /*
    public function testing(Request $req){
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
                                    <!--
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>username</qcom:propertyName>
                                      <qcom:propertyValue/>
                                    </qcom:ttContext>
                                    <qcom:ttContext>
                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                      <qcom:propertyName>password</qcom:propertyName>
                                      <qcom:propertyValue/>
                                    </qcom:ttContext>
                                    -->
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
                                <soNbr>testnew</soNbr>
                                <soCust>80-00452</soCust>
                                <soBill>80-00452</soBill>
                                <soShip>80-00452</soShip>
                                <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>
                                <soDueDate>2020-12-31</soDueDate>
                                <soPo>123</soPo>
                                <soDetailAll>true</soDetailAll>';

						    $qdocbody .= '<salesOrderDetail>'.
					                            '<line>1</line>'.
					                            '<sodPart>010-2515-1LT</sodPart>'.
					                            '<sodQtyOrd>1</sodQtyOrd>'.
					                            '<sodQtyAll>1</sodQtyAll>'.             
					                    '</salesOrderDetail>';
                                  
                                  
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
		
		    //dd($qdocResponse,$qdocRequest,$qdocResult);  

            if($qdocResult=="success" OR $qdocResult=="warning")
            {
                    // update 20112020
                    $qty = '';
                    $price = '';
                    $disc = 0;
                    $discdet = 0;
                    $total = 0;
                    $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
                    
                    // pisahin hasil balikan dari qxtend
                    foreach($xmlResp->xpath('//ns3:tt_msg_desc') as $data){
                        if(str_contains($data,'S: ')){
                          $price .= substr($data, stripos($data, 'P:') + 3). ','; // +3 karena 'P: ' 
                        }elseif(str_contains($data,'Q: ')){
                          $qty .= substr($data, stripos($data, 'Q:') + 3). ',';
                        }elseif(str_contains($data, 'D: ')){
                          $disc = substr($data, stripos($data, 'D:') + 3). ',';
                        }elseif(str_contains($data, 'C: ')){
                          $discdet .= substr($data, stripos($data, 'C:') + 3). ',';
                        }
                    }
                    
                    
                    $price = explode(',', substr($price, 0, -1));
                    $qty   = explode(',', substr($qty, 0, -1));
                    $discdet = explode(',', substr($discdet, 0, -1));
                    $disc  = substr($disc, 0, -1);
                    $flg = 0;
                    
                    if($price[0] == '' or $qty[0] == '' or $discdet[0] == ''){
                    	// Qxtend Tidak terima harga / disc / qty
                    	$nopo = 'testnew';
                    	Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from .p for SO Number : '.$nopo);

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
      										'<insonbr>'.$nopo.'</insonbr>'.
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
  		                		$disc = $data->t_dischead;
  		                	
  		                		$flgarr += 1;
  		                	}
  		                }else{
  		                	Log::channel('customlog')->info('Could not find Detail Price/Disc/Qty from WSA for SO Number : '.$nopo);
  		                }

                    }
                    dd($price,$qty,$discdet,$qdocResponse,$xmlResp->xpath('//ns3:tt_msg_desc'),count($price));

            }
    }*/

    public function testing(Request $req){

        $listso = DB::table('so_mstrs')
                        ->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
                        //->whereRaw('Date(so_dets.created_at) = "'.Carbon::now()->format('Y-m-d').'"')
                        //->whereRaw('Date(so_dets.created_at) = "2021-01-06"')
                        //->where('so_mstrs.so_status','!=','6') // Closed
                        //->where('so_mstrs.so_status','!=','5') // Delete
                        //->where('so_mstrs.so_nbr','=','28100006')
                        ->orderBy('so_mstrs.so_nbr')
                        ->get();

        //dd($listso);

        Schema::create('temp_table', function($table)
        {
            $table->string('so_nbr');
            $table->integer('so_line');
            $table->string('so_part');
            $table->decimal('so_qty_ord');
            $table->string('so_part_qad');
            $table->decimal('so_qty_ord_qad');
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
                            '<checkSO xmlns="urn:iris.co.id:wsatrain">'.
                            '<inpdomain>'.$domain.'</inpdomain>'.
                            '<inpsonbr>'.$listso->so_nbr.'</inpsonbr>'.
                            //'<inpsodpart>'.$listso->so_itemcode.'</inpsodpart>'.
                            '<inpsoline>'.$listso->so_line.'</inpsoline>'.
                            //'<inpqtyord>'.$listso->so_qty.'</inpqtyord>'.
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
                        'flg' => '0',
                    ]);
          }

        }

        $errorlist = DB::table('temp_table')->orderBy('so_nbr')->get();

        Schema::drop('temp_table');

        
        if(count($errorlist) > 0){
          Session()->flash('error','There are difference(s) between Web & QAD');
          return view('valqad.menu-valso', ['data' => $errorlist]);
        }else{
          Session()->flash('updated','There is a no difference between Web & QAD');
          return view('valqad.menu-valso', ['data' => $errorlist]);
        }
        
    }

    public function test(Request $req){

        $query = "Date(so_mstrs.created_at) = '2021-02-17' and so_site = '10'";

        $listso = DB::table('do_mstr')
                        ->join('dod_det','do_mstr.do_nbr','=','dod_det.dod_nbr')
                        ->join('so_mstrs','dod_det.dod_so','=','so_mstrs.so_nbr')
                        ->whereRaw($query)
                        ->where(function($query){
                          $query->where('do_status','=',1)->orWhere('do_status','=',4);
                        })
                        ->selectRaw('*,date(so_mstrs.created_at) as "ord_date", sum(dod_qty) as total_ship')
                        ->orderBy('so_mstrs.so_nbr')
                        ->groupBy('dod_det.dod_so')
                        ->groupBy('dod_det.dod_line')
                        ->get();

        dd($listso);

        // WSA --> Shipment
          // Validasi WSA --> Gantiin .bat

          // $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
          // $qxReceiver     = '';
          // $qxSuppRes      = 'false';
          // $qxScopeTrx     = '';
          // $qdocName       = '';
          // $qdocVersion    = '';
          // $dsName         = '';
          
          // $timeout        = 0;

          // $domain         = 'DKH';
          // $itemcode       = '';

          // // ** Edit here
          // $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
          //                 '<Body>'.
          //                 '<SchedLoadShip xmlns="urn:iris.co.id:wsatrain">'.
          //                 '<inpdomain>'.$domain.'</inpdomain>'.
          //                 '</SchedLoadShip>'.
          //                 '</Body>'.
          //                 '</Envelope>';


          // $curlOptions = array(CURLOPT_URL => $qxUrl,
          //                      CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
          //                      CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
          //                      CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
          //                      CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
          //                      CURLOPT_POST => true,
          //                      CURLOPT_RETURNTRANSFER => true,
          //                      CURLOPT_SSL_VERIFYPEER => false,
          //                      CURLOPT_SSL_VERIFYHOST => false);
                       
          // $getInfo = '';
          // $httpCode = 0;
          // $curlErrno = 0;
          // $curlError = '';
          // $qdocResponse = '';

          // $curl = curl_init();
          // if ($curl) {
          //     curl_setopt_array($curl, $curlOptions);
          //     $qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
          //     $curlErrno    = curl_errno($curl);
          //     $curlError    = curl_error($curl);
          //     $first        = true;
          
          //     foreach (curl_getinfo($curl) as $key=>$value) {
          //         if (gettype($value) != 'array') {
          //             if (! $first) $getInfo .= ", ";
          //             $getInfo = $getInfo . $key . '=>' . $value;
          //             $first = false;
          //             if ($key == 'http_code') $httpCode = $value;
          //         }
          //     }
          //     curl_close($curl);                   
          // }
                     
          // $xmlResp = simplexml_load_string($qdocResponse);        

          // $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain');  
          // //dd($qdocResponse);

          // $dataloop    = $xmlResp->xpath('//ns1:tempRow');
          // $qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];  
          // //dd($qdocResultx,$qdocResponse,$dataloop);
          // if ($qdocResultx == 'true')  {
          //     $validate = DB::table('transaksi_hist')
          //                 ->whereRaw('CAST(created_at as Date) = "'.Carbon::now()->toDateString().'"')
          //                 ->count();

          //     if($validate == 0){
          //         // belum pernah load hari itu.
      
          //         // Save Data to DB
          //         foreach($dataloop as $data){
          //             // Save qty ship ke contract mstr
          //             DB::table('transaksi_hist')
          //                         ->insert([
          //                             'tr_nbr' => $data->t_trnbr,
          //                             'item_code' => $data->t_trpart,
          //                             'cust_code' => $data->t_socust,
          //                             'date_trans' => $data->t_vdate,
          //                             'qty' => $data->t_qty, // Decimal jadiin integer
          //                             'total' => $data->t_trprice * $data->t_qty, // Decimal jadiin integer
          //                             'created_at' => Carbon::now()->toDateTimeString(),
          //                             'updated_at' => Carbon::now()->toDateTimeString(),
          //                             'site' => substr($data->t_socust, 0, 2)
          //                             //'remark' => $contract->contract_id,
          //                             //'brand_code' => $data->t_brand
          //                         ]);
                          
          //             $datatr = DB::table('transaksi_sum')
          //                         ->where('item_code','=',$data->t_trpart)
          //                         ->where('cust_code','=',$data->t_socust)
          //                         ->whereRaw('year(date_trans) = year("'.$data->t_vdate.'") and month(date_trans) = month("'.$data->t_vdate.'")')
          //                         ->first();

          //             if(is_null($datatr)){
          //                 // tidak ada data, insert baru
          //                 DB::table('transaksi_sum')
          //                         ->insert([
          //                             'item_code' => $data->t_trpart,
          //                             'cust_code' => $data->t_socust,
          //                             //'brand_code' => $data->t_brand,
          //                             'date_trans' => $data->t_vdate,
          //                             'qty' => $data->t_qty, // Decimal jadiin integer
          //                             'total' => $data->t_qty * $data->t_trprice, // Decimal jadiin integer
          //                             'site' => substr($data->t_socust, 0, 2),
          //                             'created_at' => Carbon::now()->toDateTimeString(),
          //                             'updated_at' => Carbon::now()->toDateTimeString()
          //                         ]);
          //             }else{
          //                 // ada data update

          //                 DB::table('transaksi_sum')
          //                         ->where('item_code','=',$data->t_trpart)
          //                         ->where('cust_code','=',$data->t_socust)
          //                         ->whereRaw('year(date_trans) = year("'.$data->t_vdate.'") and month(date_trans) = month("'.$data->t_vdate.'")')
          //                         ->update([
          //                             'qty' => $datatr->qty + $data->t_qty, // Decimal jadiin integer
          //                             'total' => $datatr->total + $data->t_qty * $data->t_trprice, // Decimal jadiin integer
          //                             'updated_at' => Carbon::now()->toDateTimeString()
          //                         ]);
          //             }
          //         }

          //     }else{
          //         Log::channel('shippay')->info('Data Shipment '.Carbon::now()->toDateString().' sudah diload');
          //         session()->flash("error", "Error Shipment Hari ini sudah diload");
          //         return back();
          //     }

          //     //dd('ew');
          // }else{
          //     Log::channel('shippay')->info('Error WSA returns false Tanggal : '.Carbon::now()->toDateString());
          //     session()->flash("error", "WSA Shipment return False");
          //     return back();
          // }

         // WSA --> Payment
          // Validasi WSA --> Gantiin .bat

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
                          '<SchedLoadPay xmlns="urn:iris.co.id:wsatrain">'.
                          '<inpdomain>'.$domain.'</inpdomain>'.
                          '</SchedLoadPay>'.
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
            $validate = DB::table('transaksi_sum')
                        ->whereRaw('CAST(pay_updated_at as Date) = "'.Carbon::now()->toDateString().'"')
                        ->count();

            if($validate == 0){

                // Save Data to DB
                foreach($dataloop as $data){
                    $datahist = DB::table('transaksi_hist')
                                    ->where('tr_nbr','=',$data->t_idhnbr)
                                    ->where('item_code','=',$data->t_idhpart)
                                    ->first();
                    

                    if($datahist){
                        //$this->output->write("Ada isi",false); Hanya update yang sudah ada no rf di ship
                    
                        $trsum = DB::table('transaksi_sum')
                                    ->where('item_code','=',$data->t_idhpart)
                                    ->where('cust_code','=',$data->t_debtorcode)
                                    ->whereRaw('year(date_trans) = year("'.$datahist->date_trans.'") and month(date_trans) = month("'.$datahist->date_trans.'")')
                                    ->first();

                    
                        DB::table('transaksi_hist')
                                ->where('tr_nbr','=',$data->t_idhnbr)
                                ->where('item_code','=',$data->t_idhpart)
                                ->update([
                                    'qty_paid' => $datahist->qty_paid + $data->t_idhqtyinv, // Decimal jadiin integer
                                    'total_paid' => ( $datahist->qty_paid + $data->t_idhqtyinv ) / $datahist->qty * $datahist->total,
                                    'pay_updated_at' => Carbon::now()->toDateTimeString()
                                ]);

                                
                        DB::table('transaksi_sum')
                            ->where('item_code','=',$data->t_idhpart)
                            ->where('cust_code','=',$data->t_debtorcode)
                            ->whereRaw('year(date_trans) = year("'.$datahist->date_trans.'") and month(date_trans) = month("'.$datahist->date_trans.'")')
                            ->update([
                                'qty_paid' => $trsum->qty_paid + $data->t_idhqtyinv, // Decimal jadiin integer
                                'total_paid' => ( $trsum->qty_paid + $data->t_idhqtyinv ) / $trsum->qty * $trsum->total,
                                'pay_updated_at' => Carbon::now()->toDateTimeString()
                            ]);
                
                    }    
                }
                
            }else{
                Log::channel('shippay')->info('Data Payment '.Carbon::now()->toDateString().' sudah diload');
                session()->flash("error", "Error Payment Hari ini sudah diload");
                return back();
            }
          }else{
              Log::channel('shippay')->info('Error WSA Payment returns false Tanggal : '.Carbon::now()->toDateString());
              session()->flash("error", "WSA Payment return False");
              return back();
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

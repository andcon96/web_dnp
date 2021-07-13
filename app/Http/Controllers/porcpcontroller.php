<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Log;

class porcpcontroller extends Controller
{
public function update(Request $req){
  $ponbr = $req->input('e_nbr');
  $line = $req->input('e_line');
  $qty  = $req->input('e_qty');
  $ord  = $req->input('e_ord');
  $ship  = $req->input('e_ship');
  $open = $ord - $ship - $qty;
  $qdocResult = "";
   


  $data1 = array(
                    'xpod_qty_rcvd'=>$qty,
                   // 'xpod_qty_open'=>$open,                    
                                    
                );   

      DB::table('xpod_dets')->where('xpod_nbr',$ponbr)
         ->where('xpod_line',$line)->update($data1);

   $data = DB::table("xpod_dets")
                ->where('xpod_nbr', $ponbr)
                ->orderBy('xpod_dets.xpod_line')
                ->get();

            $date = Carbon::now()->format('ymd');
   return view('/po/poreceipt', compact('date','data','ponbr','qdocResult'));

}

 
         public function porcp1(Request $req){
        
          $ponbr  = $req->ponbr;
          $qdocResult = "";

          DB::table('xpod_dets')      
      ->delete();

          if ($ponbr != ""){
         
           $qxUrl     = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
    $qxReceiver   = '';
    $qxSuppRes    = 'false';
    $qxScopeTrx   = '';
    $qdocName   = '';
    $qdocVersion  = '';
    $dsName     = '';
    $timeout    = 0;
      // menangkap data pencarian
      
      $qdocRequest = '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                     '<Body>'.
                     '<xxporcp xmlns="urn:iris.co.id:wsatrain">'.
                     '<inpdomain>DKH</inpdomain>'.
                     '<inpvend>'.$ponbr.'</inpvend>'.
                     '</xxporcp>'.
                     '</Body>'.
                     '</Envelope>';
                      
    //dd($qdocRequest);              
    $curlOptions = array(CURLOPT_URL => $qxUrl,
                 CURLOPT_CONNECTTIMEOUT => $timeout,    // in seconds, 0 = unlimited / wait indefinitely.
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
        $qdocResponse = curl_exec($curl);     // sending qdocRequest here, the result is qdocResponse.
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
      
//dd($qdocResponse);
      
      $xmlResp = simplexml_load_string($qdocResponse);
      
      $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain');

$qdocResultx = (string) $xmlResp->xpath('//ns1:outOK')[0];
                if ($qdocResultx == 'false')  {
               session()->flash("updated","PO Number Tidak Terdaftar"); 
                    }
                 

             $flag = 0;  
         foreach($xmlResp->xpath('//ns1:t_nbr') as $data) { 
                          $qdocResult = (string) $xmlResp->xpath('//ns1:t_sts')[$flag];  
                      $datax1 = array(
                       
                                'xpod_nbr'=> (string)$xmlResp->xpath('//ns1:t_nbr')[$flag],
                                'xpod_line'=> (string)$xmlResp->xpath('//ns1:t_line')[$flag],
                                'xpod_vend'=> (string)$xmlResp->xpath('//ns1:t_vend')[$flag],
                                'xpod_part'=> (string)$xmlResp->xpath('//ns1:t_part')[$flag],
                                'xpod_qty_ord'=> (string)$xmlResp->xpath('//ns1:t_qty')[$flag],
                                'xpod_qty_open'=> (string)$xmlResp->xpath('//ns1:t_qty')[$flag] - (string)$xmlResp->xpath('//ns1:t_rcvd')[$flag],
        'xpod_qty_rcvd'=> (string)$xmlResp->xpath('//ns1:t_qty')[$flag] - (string)$xmlResp->xpath('//ns1:t_rcvd')[$flag],
        'xpod_qty_ship'=> (string)$xmlResp->xpath('//ns1:t_rcvd')[$flag], 
                                'xpod_desc'=> (string)$xmlResp->xpath('//ns1:t_desc')[$flag],
                                'xpod_um'=> (string)$xmlResp->xpath('//ns1:t_um')[$flag],                                       
                            );     

    
    if ($qdocResult == 'c') {
       session()->flash("updated","PO Number Sudah Close"); 
    }
    {
                            DB::table('xpod_dets')->insert($datax1);  
                }
             $flag = $flag + 1;
            } 
            
           } 
            
          $data = DB::table("xpod_dets")
                ->where('xpod_nbr', $ponbr)
                ->orderBy('xpod_dets.xpod_line')
                ->get();

            $date = Carbon::now()->format('ymd');
          
           
            //return view('/po/poreceipt',['users'=>$users]);
            return view('/po/poreceipt', compact('date','data','ponbr','qdocResult'));
    }



    private function httpHeader($req) {
         return array('Content-type: text/xml;charset="utf-8"',
               'Accept: text/xml',
               'Cache-Control: no-cache',
               'Pragma: no-cache', 
         
         
               'SOAPAction: ""',    // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
               'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
         }

        public function porcpok(Request $req){
          $ponbr  = $req->nbr;
          $qdocResult = "";
          
$dataxx = DB::table("xpod_dets")
                ->where('xpod_nbr', $ponbr)
                ->where('xpod_qty_rcvd', '!=', 0 )
                ->orderBy('xpod_dets.xpod_line')
                ->get();


          $qdocRequest = '<?xml version="1.0" encoding="UTF-8"?>
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
    <receivePurchaseOrder>
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
          <qcom:propertyValue>eB_2</qcom:propertyValue>
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
      </qcom:dsSessionContext>
      <dsPurchaseOrderReceive>
        <purchaseOrderReceive>'.          
          '<ordernum>'.$ponbr.'</ordernum>'.         
          '<fillAll>false</fillAll>          
          <yn>true</yn>
          <yn1>true</yn1>';
            
            foreach($dataxx as $show){
  $qdocRequest .= '<lineDetail>            
            <line>'.$show->xpod_line.'</line>'.
            '<lotserialQty>'.$show->xpod_qty_rcvd.'</lotserialQty>'.
            '<yn>true</yn>
            <yn1>true</yn1>                                        
          </lineDetail>';    
   }  

     
   $qdocRequest .=    '</purchaseOrderReceive>
      </dsPurchaseOrderReceive>
    </receivePurchaseOrder>
  </soapenv:Body>
</soapenv:Envelope>
'; 
          
       



          $timeout    = 120;
          $qxUrl = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
          $curlOptions = array(CURLOPT_URL => $qxUrl,
                 CURLOPT_CONNECTTIMEOUT => $timeout,    // in seconds, 0 = unlimited / wait indefinitely.
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
        $qdocResponse = curl_exec($curl);     // sending qdocRequest here, the result is qdocResponse.
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
//dd($qdocResponse);

        $xmlResp = simplexml_load_string($qdocResponse);
              
               $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');
        $qdocResult = (string) $xmlResp->xpath('//ns1:result')[0];  
           

                if($qdocResult == 'error'){
                     session()->flash("updated","Data Tidak Berhasil Terupdate");
                }
                else{
                     session()->flash("updated","Data Berhasil Diupdate ke QAD");
                }
                
foreach($dataxx as $show){
$ship = $show->xpod_qty_ship;
$rcp   = $show->xpod_qty_rcvd;
$op = $show->xpod_qty_open;
$qtyrcp = $ship + $rcp;
$open = $op - $rcp;
$inp = 0;
$data1 = array(
         'xpod_qty_ship'=>$qtyrcp,
         'xpod_qty_open'=>$open,  
         'xpod_qty_rcvd'=>$open,                  
                                    
                );   

  DB::table('xpod_dets')
                 ->where('xpod_nbr',$show->xpod_nbr)
                 ->where('xpod_line',$show->xpod_line)
         		 ->update($data1);
		}
                

               $data = DB::table("xpod_dets")
                ->where('xpod_nbr', $ponbr)
                ->orderBy('xpod_dets.xpod_line')
                ->get();

            $date = Carbon::now()->format('ymd');     
               


          return view('/po/poreceipt', compact('date','data','ponbr','qdocResult'));
     }
}



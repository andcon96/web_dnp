<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Log;

class SalesOrderSalesController extends Controller
{
	//
	// HTTP Header WSA
    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }

    public function index(Request $req){
      
    	// $data = DB::table('so_mstrs')
    	// 		->join('customers','so_mstrs.so_cust','=','customers.cust_code')
      // 		->paginate(5);
      if(Session::get('pusat_cabang')==1){
      $data = DB::table('so_mstrs')
    	    ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
            ->where('so_status', '!=', 10)
            ->selectRaw('*,so_mstrs.created_at as "so_created"')
            ->orderBy('so_mstrs.created_at','Desc')
            ->Simplepaginate();
      }
      else if(Session::get('pusat_cabang')==0){

    	if(Session::get('salesman') == 'Y'){
    	$data = DB::table('so_mstrs')
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->where('so_user', '=', Session::get('username')) 
                ->selectRaw('*,so_mstrs.created_at as "so_created"')
                ->orderBy('so_mstrs.created_at','Desc')
                ->Simplepaginate();
    	}else{
    	$data = DB::table('so_mstrs')
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->join('users','so_mstrs.so_user','=','users.username')
                ->join('roles','users.role_user','=','roles.role_code')
                ->where('so_site','=',Session::get('site'))
                ->whereRaw('roles.salesman = "Y"')
                ->selectRaw('*,so_mstrs.created_at as "so_created"')
                ->orderBy('so_mstrs.created_at','Desc')
                ->Simplepaginate();
    	}
        
      }

    	$item = DB::table('items')
    			->get();

    	$customer = DB::table('customers')
            ->whereRaw('cust_code like "'.Session::get('site').'%" ')
    			->get();

    	if($req->ajax()){
    		return view('so.table-sosales',['data' => $data, 'item' => $item, 'customer' => $customer]);
    	}

    	return view('so.sosalesbrowse',['data' => $data, 'item' => $item, 'customer' => $customer]);
    }

    public function alamatsearch(Request $req){
    	if($req->ajax()){
        $output = '';
        $shipto = DB::table('cust_shipto')
                ->where('shipto','=',$req->shipto)
                ->first();
        if($shipto){
          $output = $shipto->custaddr;
        }else{
          $data = DB::table('customers')
            ->where('cust_code','=',$req->cust)
            ->first();

          $output = $data->cust_alamat;
        }

    		return response($output);
    	}
    }

    public function searchum(Request $req){
      // dd($req->all());
    	if($req->ajax()){
        $data =     DB::table('items')
                        ->where('itemcode','=',$req->item)
                        ->first();

        return response($data->item_um.'||'.$data->item_location);
      }
    }

    public function shiptosearch(Request $req){
        if($req->ajax()){
            $data = DB::table('cust_shipto')
                    ->leftjoin('customers','cust_shipto.shipto','=','customers.cust_code')
                    ->where('cust_shipto.cust_code','=',$req->cust)
                    ->get();

            $output = '';
            $output = $req->cust.' --  ||';
            if(count($data) > 0){
                foreach($data as $data){
                    $output .= $data->shipto.' -- '.$data->cust_desc.'||';
                }
            }

            return response($output);
        }
    }

    public function detailsales(Request $req){
      
    	if($req->ajax()){
    		$data = DB::table('so_mstrs')
    					->join('so_dets','so_mstrs.so_nbr','=','so_dets.so_nbr')
    					->join('items','items.itemcode','=','so_dets.so_itemcode')
    					->where('so_mstrs.so_nbr','=',$req->sonbr)
    					->get();

    		if(!$data->isEmpty()){
	    		$output = '';
	    		foreach($data as $data){
	    			$output .= '<tr class="foottr">'.

	    					   '<td class="foot1" data-label="Barang">'.$data->itemcode.' - '.$data->itemdesc.'</td>'.
	    					   '<td class="foot2" data-label="Jumlah">'.$data->so_qty.'</td>'.
                   '<td class="foot2" data-label="Satuan">'.$data->so_um.'</td>'.
                   '<td class="foot2" data-label="Satuan">'.$data->item_location.'</td>'.


	    			           '</tr>';
	    		}

	    		return response($output);
    		}

    	}
	  }
	
    //YANG LAMA sebelum 3122020
    public function createsosalesLama(Request $req){
      // Validasi Web
          
          if($req->shipto == ''){
              session()->flash('error','Data Shipto harus diisi terlebih dahulu lewat QAD');
              return back();
          }else if($req->barang == ''){
              session()->flash('error','Detail Harus diisi minimal 1 item');
              return back();
          }

          $barangke = 0;

          $users = db::table('site_mstrs')
                      ->where('site_code','=',Session::get('site'))
                      ->where('site_flag','=','N') // Tidak punya cabang
                      ->first();

          // ------------------------ WSA -> klo ada wh ga perlu 
          if($users){
              foreach($req->barang as $barang){
                  // dd($req->barang[$barangke],$req->jumlah[$barangke]);

                  // Var WSA
                  $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';
                  $qxReceiver     = '';
                  $qxSuppRes      = 'false';
                  $qxScopeTrx     = '';
                  $qdocName       = '';
                  $qdocVersion    = '';
                  $dsName         = '';
                  
                  $timeout        = 0;

                  $domain         = '10USA';
                  $itemcode       = '';

                  // ** Edit here
                  $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                  '<Body>'.
                                  '<sisaQty xmlns="urn:iris.co.id:wsatest">'.
                                  '<inpdomain>'.$domain.'</inpdomain>'.
                                  '<inpart>'.$barang.'</inpart>'.
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

                  $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');  

                  
                  $flag = 0;
                  
                  $item    = '';
                  $qty     = 0;
                  $qty_all = 0;
                  
                  foreach($xmlResp->xpath('//ns1:t_part') as $data) { 
                      $item = (string) $xmlResp->xpath('//ns1:t_part')[$flag]; // nama item akan kosong jika tidak ketemu di qad.
                      $qty  += (string) $xmlResp->xpath('//ns1:t_qty')[$flag]; // jumlah qty oh
                      $qty_all += (string) $xmlResp->xpath('//ns1:t_qty_all')[$flag];
                      $flag += 1;
                  }

                  $qtysisa = $qty - $qty_all;


                  if($item != '' && $req->jumlah[$barangke] > $qtysisa){
                      // Barang Dipesan > Stok OH --> Bkin PO ke QAD DNP

                      // Var Web
                      $qtypo = $req->jumlah[$barangke] - $qty;
                      $nopo  = substr(Session::get('site'), 0,2).Carbon::now()->format('dmy');
                      // Var Qxtend
                      $qxUrl          = 'http://qad2017vm.ware:22079/qxi/services/QdocWebService?wsdl';
                      
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
                                          <maintainPurchaseOrder>
                                            <qcom:dsSessionContext>
                                              <qcom:ttContext>
                                                <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                                <qcom:propertyName>domain</qcom:propertyName>
                                                <qcom:propertyValue>10USA</qcom:propertyValue>
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

                      // edit povend buat ke dnp
                      $qdocbody = '<dsPurchaseOrder>
                                      <purchaseOrder>
                                        <poNbr>'.$nopo.'</poNbr>
                                        <poVend>10-200</poVend>
                                        <poSite>10-100</poSite> 
                                        <revChange>false</revChange>
                                        <lineDetail>
                                          <yn>true</yn>
                                          <yn1>true</yn1>
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
                      
                      if($qdocResult=="success" OR $qdocResult=="warning")
                      {
                          Log::channel('customlog')->info('PO : '.$nopo.' Updated, Item : '.$req->barang[$barangke].', Qty : '.$qtypo);
                      }else{
                          Log::channel('customlog')->info('PO : '.$nopo.' Failed, Item : '.$req->barang[$barangke].', Qty : '.$$qtypo);
                      }
                  }

                  $barangke += 1;
              }
          }

          
          // ------------------------ Qxtend
              // Variable Web
              $flg = 0;
              $line = 1;

              // Var Qxtend
              $qxUrl          = 'http://qad2017vm.ware:22079/qxi/services/QdocWebService?wsdl';
              
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
                                        <qcom:propertyValue>10USA</qcom:propertyValue>
                                      </qcom:ttContext>
                                      <qcom:ttContext>
                                        <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                        <qcom:propertyName>scopeTransaction</qcom:propertyName>
                                        <qcom:propertyValue>false</qcom:propertyValue>
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

                                  <soCust>'.$req->custcode.'</soCust>
                                  <soBill>'.$req->custcode.'</soBill>
                                  <soShip>'.$req->shipto.'</soShip>
                                  <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>
                                  <soDueDate>'.$req->duedate.'</soDueDate>
                                  <soDetailAll>true</soDetailAll>';

                                  foreach($req->barang as $barang){
                                          $qdocbody .=    '<salesOrderDetail>'.
                                                                  '<line>'.$line.'</line>'.
                                                                  '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                                  '<sodQtyOrd>'.$req->jumlah[$flg].'</sodQtyOrd>'.
                                                                  '<sodUm>'.$req->um[$flg].'</sodUm>'.
                                                                  '<sodDetailAll>true</sodDetailAll>'.
                                                                  '<allocationDetail>'.
                                                                      '<ladQtyAll>'.$req->jumlah[$flg].'</ladQtyAll>'.
                                                                  '</allocationDetail>'.                                
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
                      // update 20112020
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
                        $total += $price[$flg] * $qty[$flg];
                        $flg += 1;
                      }

                      // itung total nett disc master
                      $total = $total - $total * $disc / 100;
      

                      // Data berhasil terbuat di QAD               
                      $so_nbr = (string) $xmlResp->xpath('//ns1:soNbr')[0];
                      $flgweb = 0;
                      $lineweb = 1;

                      // Insert SO QAD ke web
                      DB::table('so_mstrs')
                              ->insert([
                                  'so_nbr' => $so_nbr,
                                  'so_cust' => $req->custcode,
                                  'so_duedate' => $req->duedate,
                                  'so_shipto' => $req->shipto,
                                  'so_notes' => $req->notes,
                                  'so_status' => 1, // 1 New SO , 2 On Hold
                                  'so_site' => Session::get('site'), // Cek Session User
                                  'so_price' => $total, // Cari Harga update di Qxtend, liat latian hari 3,
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
                                      'so_qty_open' => $req->jumlah[$flgweb], // jika 0 bkin statusny closed
                                      'so_um' => $req->um[$flgweb],
                                      'so_status' => 1, // 1 New SO, 2 On Hold, 3 Habis
                                      'created_at' => Carbon::now()->toDateTimeString(),
                                      'updated_at' => Carbon::now()->toDateTimeString(),
                                  ]);

                          $flgweb += 1;
                          $lineweb += 1;
                      }

                      // WSA ke qad cek apakah status PO Hold dari QAD
                        // Var WSA
                        $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';
                        $qxReceiver     = '';
                        $qxSuppRes      = 'false';
                        $qxScopeTrx     = '';
                        $qdocName       = '';
                        $qdocVersion    = '';
                        $dsName         = '';
                        
                        $timeout        = 0;

                        $domain         = '10USA';

                        // ** Edit here
                        $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                          '<Body>'.
                                          '<statusSO xmlns="urn:iris.co.id:wsatest">'.
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

                        $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');  
                        $status = '';
                        $flag = 0;

                        foreach($xmlResp->xpath('//ns1:t_sonbr') as $data) { 
                            $status  = (string) $xmlResp->xpath('//ns1:t_status')[$flag]; // jumlah qty oh
                            $flag += 1;
                        }

                      if($status != ''){
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
                          }
                          
                          DB::table('so_mstrs')
                              ->where('so_nbr','=',$so_nbr)
                              ->update([
                                  'so_status' => '3' // Hold dari QAD
                              ]);
                      }

                      // Cek Validasi di Web untuk CR jika masalah bkin status QAD jdi hold.

                      $data = DB::table('cust_relation')
                                  ->where('cust_code_parent','=',$req->custcode)
                                  ->get();

                      $cust = array($req->custcode);

                      if(!$data->isEmpty()){
                        // Customer punya parent child di web

                        foreach($data as $data){
                            // bkin array termasuk parentnya.
                            array_push($cust,$data->cust_code_child);
                        }

                        // WSA
                        $totalcc = 0;
                        $totalso = 0;
                        $limitCR = 0;
			$flgcc = '';
                    	$flgso = '';


                        foreach($cust as $cust){
                            // Var WSA
                                $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';
                                $qxReceiver     = '';
                                $qxSuppRes      = 'false';
                                $qxScopeTrx     = '';
                                $qdocName       = '';
                                $qdocVersion    = '';
                                $dsName         = '';
                                
                                $timeout        = 0;

                                $domain         = '10USA';
                                $itemcode       = '';

                                // ** Edit here
                                $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                                '<Body>'.
                                                '<sisaCreditLimit xmlns="urn:iris.co.id:wsatest">'.
                                                '<incust>'.$cust.'</incust>'.
                                                '</sisaCreditLimit>'.
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
                                
                                $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');  
                                
                                $flag = 0;
                                
                                $item    = '';
                                $qty     = 0;
                                $qty_all = 0;
                                
                                $flgcc = (string) $xmlResp->xpath('//ns1:t_flgCRcc')[0];
			    	$flgso = (string) $xmlResp->xpath('//ns1:t_flgCRso')[0];
			
				if($flgcc == 'true'){
					$totalcc += (string) $xmlResp->xpath('//ns1:t_CRcc')[0];
				    }
		                if($flgso == 'true'){
				    	$totalso += (string) $xmlResp->xpath('//ns1:t_CRso')[0];
				    }

                                if((string) $xmlResp->xpath('//ns1:t_cust')[0] == $req->custcode){
                                    $limitCR = (string) $xmlResp->xpath('//ns1:t_CRfix')[0];
                                }
                        }

                        if($limitCR < $totalcc + $totalso){
                            // Melebihi Limit Cretdit --> Hold SO --> Qxtend
                            // Var Qxtend
                                $qxUrl          = 'http://qad2017vm.ware:22079/qxi/services/QdocWebService?wsdl';
                                
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
                                                          <qcom:propertyValue>10USA</qcom:propertyValue>
                                                        </qcom:ttContext>
                                                        <qcom:ttContext>
                                                          <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                                          <qcom:propertyName>scopeTransaction</qcom:propertyName>
                                                          <qcom:propertyValue>false</qcom:propertyValue>
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
                                                    <soNbr>'.$so_nbr.'</soNbr>
                                                    <soStat>CH</soStat>'; // Status Beda" mesti dicek lg
                                                      
                                                      
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

                                if($qdocResult=="success" OR $qdocResult=="warning")
                                {
                                    // Bkin di approval temp

                                    $data = DB::table('so_mstrs')
                                                ->join('approvals','so_mstrs.so_site','=','approvals.site_app')
                                                ->where('so_nbr','=',$so_nbr)
                                                ->orderBy('order','asc')
                                                ->get();

                                    

                                    $statusso = DB::table('so_mstrs')
                                                    ->where('so_nbr','=',$so_nbr)
                                                    ->first();
                                      
                                    // buat status jdi hold
                                    if($statusso->so_status == '3'){  
                                      DB::table('so_mstrs')
                                              ->where('so_nbr','=',$so_nbr)
                                              ->update([
                                                  'so_status' => '4' // On Hold Web parent child + QAD
                                              ]);
                                    }else{
                                      DB::table('so_mstrs')
                                                ->where('so_nbr','=',$so_nbr)
                                                ->update([
                                                    'so_status' => '2' // On Hold Web parent child
                                                ]);
                                                
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
                                      }
                                    }

                                    Log::channel('customlog')->info('SO : '.$so_nbr.' Successfully Hold ');
                                }else{
                                    Log::channel('customlog')->info('SO : '.$so_nbr.' Failed to Hold ');
                                }
                        }
                      
                      }

                      session()->flash('updated','Data Berhasil Disimpan');
                      return back();
              }else{
                  // Error data tidak masuk QAD
                  $resultProcess  = false;
                  $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
                  $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
                  $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
                  $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


                  //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev,$qdocResult);
                  Log::channel('customlog')->info('Failed to create SO , error :'.$qdocMsgDesc.'--'.$qdocMsgData.'--'.$qdocMsgSev);
                  Session()->flash('error','Terdapat Error, Data Gagal tersimpan');
                  return back();
              }
    }

    //YANG BARU untuk 3122020
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

      $barangke = 0;

      $users = db::table('site_mstrs')
                  ->where('site_code','=',Session::get('site'))
                  ->where('site_flag','=','N') // Tidak punya cabang
                  ->first();

      // ------------------------ WSA -> klo ada wh ga perlu 
      if($users){
          foreach($req->barang as $barang){
              // dd($req->barang[$barangke],$req->jumlah[$barangke]);

              // Var WSA
              $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';
              $qxReceiver     = '';
              $qxSuppRes      = 'false';
              $qxScopeTrx     = '';
              $qdocName       = '';
              $qdocVersion    = '';
              $dsName         = '';
              
              $timeout        = 0;

              $domain         = '10USA';
              $itemcode       = '';

              // ** Edit here
              $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                              '<Body>'.
                              '<sisaQty xmlns="urn:iris.co.id:wsatest">'.
                              '<inpdomain>'.$domain.'</inpdomain>'.
                              '<inpart>'.$barang.'</inpart>'.
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

              $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');  

              
              $flag = 0;
              
              $item    = '';
              $qty     = 0;
              $qty_all = 0;
              
              foreach($xmlResp->xpath('//ns1:t_part') as $data) { 
                  $item = (string) $xmlResp->xpath('//ns1:t_part')[$flag]; // nama item akan kosong jika tidak ketemu di qad.
                  $qty  += (string) $xmlResp->xpath('//ns1:t_qty')[$flag]; // jumlah qty oh
                  $qty_all += (string) $xmlResp->xpath('//ns1:t_qty_all')[$flag];
                  $flag += 1;
              }

              $qtysisa = $qty - $qty_all;


              if($item != '' && $req->jumlah[$barangke] > $qtysisa){
                  // Barang Dipesan > Stok OH --> Bkin PO ke QAD DNP

                  // Var Web
                  $qtypo = $req->jumlah[$barangke] - $qty;
                  $data = DB::table('site_mstrs')
                              ->where('site_code','=',Session::get('site'))
                              ->first();

                  $bulan = substr($data->r_nbr_aut, 0, 2);
                  $rn    = substr($data->r_nbr_aut, 2, 2);

                  if($bulan != Carbon::now()->format('m')){
                      // Ganti bulan reset bulan & rn
                      $bulan = Carbon::now()->format('m');
                      $rn    = '01';
                  }else{
                      $rn += 1;
                      $rn = str_pad($rn , 2, '0', STR_PAD_LEFT);
                  }

                  $nopo  = "Q".substr(Session::get('site'), 0,2).substr(Carbon::now()->format('Y'),3).$bulan.$rn;
                  
                  // Var Qxtend
                  $qxUrl          = 'http://qad2017vm.ware:22079/qxi/services/QdocWebService?wsdl';
                  
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
                                      <maintainPurchaseOrder>
                                        <qcom:dsSessionContext>
                                          <qcom:ttContext>
                                            <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                            <qcom:propertyName>domain</qcom:propertyName>
                                            <qcom:propertyValue>10USA</qcom:propertyValue>
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

                  // edit povend buat ke dnp **EDIT HERE
                  $qdocbody = '<dsPurchaseOrder>
                                  <purchaseOrder>
                                    <poNbr>'.$nopo.'</poNbr>
                                    <poVend>10-200</poVend>
                                    <poSite>10-100</poSite> 
                                    <revChange>false</revChange>
                                    <lineDetail>
                                      <yn>true</yn>
                                      <yn1>true</yn1>
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
                  
                  if($qdocResult=="success" OR $qdocResult=="warning")
                  {
                      // Update Running Number di site
                      DB::table('site_mstrs')
                              ->where('site_code','=',Session::get('site'))
                              ->update([
                                    'r_nbr_aut' => $bulan.$rn
                              ]);
                      Log::channel('customlog')->info('PO : '.$nopo.' Updated, Item : '.$req->barang[$barangke].', Qty : '.$qtypo);
                  }else{
                      Log::channel('customlog')->info('PO : '.$nopo.' Failed, Item : '.$req->barang[$barangke].', Qty : '.$$qtypo);
                  }
              }

              $barangke += 1;
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

          $site = substr(Session::get('site'),0,2); // Ambil 2 Digit Site Pertama

          $rn = str_pad($data + 1, 5, '0', STR_PAD_LEFT); // Running Number dari total SO per Site

          $year = substr(Carbon::now()->format('Y'),3); // digit terakhir Tahun

          $noso = $site.$year.$rn;

          // Var Qxtend
          $qxUrl          = 'http://qad2017vm.ware:22079/qxi/services/QdocWebService?wsdl';
          
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
                                    <qcom:propertyValue>10USA</qcom:propertyValue>
                                  </qcom:ttContext>
                                  <qcom:ttContext>
                                    <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                    <qcom:propertyName>scopeTransaction</qcom:propertyName>
                                    <qcom:propertyValue>false</qcom:propertyValue>
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
                              <soNbr>'.$noso.'</soNbr>
                              <soCust>'.$req->custcode.'</soCust>
                              <soBill>'.$req->custcode.'</soBill>
                              <soShip>'.$req->shipto.'</soShip>
                              <soOrdDate>'.Carbon::now()->toDateString().'</soOrdDate>
                              <soDueDate>'.$req->duedate.'</soDueDate>
                              <soDetailAll>true</soDetailAll>';

                              foreach($req->barang as $barang){
                                      $qdocbody .=    '<salesOrderDetail>'.
                                                              '<line>'.$line.'</line>'.
                                                              '<sodPart>'.$req->barang[$flg].'</sodPart>'.
                                                              '<sodQtyOrd>'.$req->jumlah[$flg].'</sodQtyOrd>'.
                                                              '<sodUm>'.$req->um[$flg].'</sodUm>'.
                                                              '<sodDetailAll>true</sodDetailAll>'.
                                                              '<allocationDetail>'.
                                                                  '<ladLoc>'.$req->loc[$flg].'</ladLoc>'.
                                                                  '<ladQtyAll>'.$req->jumlah[$flg].'</ladQtyAll>'.
                                                              '</allocationDetail>'.                                
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
                  // update 20112020
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
                    $total += $price[$flg] * $qty[$flg];
                    $flg += 1;
                  }

                  // itung total nett disc master
                  $total = $total - $total * $disc / 100;
  

                  // Data berhasil terbuat di QAD               
                  $so_nbr = (string) $xmlResp->xpath('//ns1:soNbr')[0];
                  $flgweb = 0;
                  $lineweb = 1;

                  // Insert SO QAD ke web
                  DB::table('so_mstrs')
                          ->insert([
                              'so_nbr' => $so_nbr,
                              'so_cust' => $req->custcode,
                              'so_duedate' => $req->duedate,
                              'so_shipto' => $req->shipto,
                              'so_notes' => $req->notes,
                              'so_status' => 1, // 1 New SO , 2 On Hold
                              'so_site' => Session::get('site'), // Cek Session User
                              'so_price' => $total, // Cari Harga update di Qxtend, liat latian hari 3,
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
                                  'so_qty_open' => $req->jumlah[$flgweb], // jika 0 bkin statusny closed
                                  'so_um' => $req->um[$flgweb],
                                  'so_status' => 1, // 1 New SO, 2 On Hold, 3 Habis
                                  'created_at' => Carbon::now()->toDateTimeString(),
                                  'updated_at' => Carbon::now()->toDateTimeString(),
                              ]);

                      $flgweb += 1;
                      $lineweb += 1;
                  }


                  // WSA ke qad cek apakah status PO Hold dari QAD
                    // Var WSA
                    $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';
                    $qxReceiver     = '';
                    $qxSuppRes      = 'false';
                    $qxScopeTrx     = '';
                    $qdocName       = '';
                    $qdocVersion    = '';
                    $dsName         = '';
                    
                    $timeout        = 0;

                    $domain         = '10USA';

                    // ** Edit here
                    $qdocRequest =    '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                      '<Body>'.
                                      '<statusSO xmlns="urn:iris.co.id:wsatest">'.
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

                    $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');  
                    $status = '';
                    $flag = 0;

                    foreach($xmlResp->xpath('//ns1:t_sonbr') as $data) { 
                        $status  = (string) $xmlResp->xpath('//ns1:t_status')[$flag]; // jumlah qty oh
                        $flag += 1;
                    }

                    if($status != ''){
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
                        }

                        DB::table('so_mstrs')
                          ->where('so_nbr','=',$so_nbr)
                          ->update([
                              'so_status' => '3' // Hold dari QAD
                          ]);
                    }


                  // Cek Validasi di Web untuk CR jika masalah bkin status QAD jdi hold.

                  $data = DB::table('cust_relation')
                              ->where('cust_code_parent','=',$req->custcode)
                              ->get();

                  $cust = array($req->custcode);


                  if(!$data->isEmpty()){
                    // Customer punya parent child di web

                    foreach($data as $data){
                        // bkin array termasuk parentnya.
                        array_push($cust,$data->cust_code_child);
                    }

                    // WSA
                    $totalcc = 0;
                    $totalso = 0;
                    $limitCR = 0;
		    $flgcc = '';
                    $flgso = '';

                    foreach($cust as $cust){
                        // Var WSA
                            $qxUrl          = 'http://qad2017vm.ware:22079/wsa/wsatest';
                            $qxReceiver     = '';
                            $qxSuppRes      = 'false';
                            $qxScopeTrx     = '';
                            $qdocName       = '';
                            $qdocVersion    = '';
                            $dsName         = '';
                            
                            $timeout        = 0;

                            $domain         = '10USA';
                            $itemcode       = '';

                            // ** Edit here
                            $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                                            '<Body>'.
                                            '<sisaCreditLimit xmlns="urn:iris.co.id:wsatest">'.
                                            '<incust>'.$cust.'</incust>'.
                                            '</sisaCreditLimit>'.
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
                            
                            $xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatest');  
                            
                            $flag = 0;
                            
                            $item    = '';
                            $qty     = 0;
                            $qty_all = 0;
                            
                            $flgcc = (string) $xmlResp->xpath('//ns1:t_flgCRcc')[0];
			    $flgso = (string) $xmlResp->xpath('//ns1:t_flgCRso')[0];
			
				if($flgcc == 'true'){
					$totalcc += (string) $xmlResp->xpath('//ns1:t_CRcc')[0];
				    }
		                if($flgso == 'true'){
				    	$totalso += (string) $xmlResp->xpath('//ns1:t_CRso')[0];
				    }

                             
                            if((string) $xmlResp->xpath('//ns1:t_cust')[0] == $req->custcode){
                                $limitCR = (string) $xmlResp->xpath('//ns1:t_CRfix')[0];
                            }
                    }

                    if($limitCR < $totalcc + $totalso){
                        // Melebihi Limit Cretdit --> Hold SO --> Qxtend
                        // Var Qxtend
                            $qxUrl          = 'http://qad2017vm.ware:22079/qxi/services/QdocWebService?wsdl';
                            
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
                                                      <qcom:propertyValue>10USA</qcom:propertyValue>
                                                    </qcom:ttContext>
                                                    <qcom:ttContext>
                                                      <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
                                                      <qcom:propertyName>scopeTransaction</qcom:propertyName>
                                                      <qcom:propertyValue>false</qcom:propertyValue>
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
                                                <soNbr>'.$so_nbr.'</soNbr>
                                                <soStat>CH</soStat>'; // Status Beda" mesti dicek lg **EDIT HERE
                                                  
                                                  
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

                            if($qdocResult=="success" OR $qdocResult=="warning")
                            {
                                // Bkin di approval temp

                                $data = DB::table('so_mstrs')
                                            ->join('approvals','so_mstrs.so_site','=','approvals.site_app')
                                            ->where('so_nbr','=',$so_nbr)
                                            ->orderBy('order','asc')
                                            ->get();

                                $statusso = DB::table('so_mstrs')
                                                ->where('so_nbr','=',$so_nbr)
                                                ->first();
                                  
                                // buat status jdi hold
                                if($statusso->so_status == '3'){  
                                  DB::table('so_mstrs')
                                          ->where('so_nbr','=',$so_nbr)
                                          ->update([
                                              'so_status' => '4' // On Hold Web parent child + QAD
                                          ]);
                                }else{
                                    DB::table('so_mstrs')
                                            ->where('so_nbr','=',$so_nbr)
                                            ->update([
                                                'so_status' => '2' // On Hold Web parent child
                                            ]);

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
                                    }
                                }

                                Log::channel('customlog')->info('SO : '.$so_nbr.' Successfully Hold ');
                            }else{
                                Log::channel('customlog')->info('SO : '.$so_nbr.' Failed to Hold ');
                            }
                    }
                  }

                  session()->flash('updated','Data Berhasil Disimpan');
                  return back();
          }else{
              // Error data tidak masuk QAD
              $resultProcess  = false;
              $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');
              $qdocMsgData    = (string) $xmlResp->xpath('//ns3:tt_msg_data')[0];
              $qdocMsgDesc    = (string) $xmlResp->xpath('//ns3:tt_msg_desc')[0];
              $qdocMsgSev     = (string) $xmlResp->xpath('//ns3:tt_msg_sev')[0];


              //dd($qdocMsgData,$qdocMsgDesc,$qdocMsgSev,$qdocResult);
              Log::channel('customlog')->info('Failed to create SO , error :'.$qdocMsgDesc.'--'.$qdocMsgData.'--'.$qdocMsgSev);
              Session()->flash('error','Terdapat Error, Data Gagal tersimpan');
              return back();
          }
    }
  


}

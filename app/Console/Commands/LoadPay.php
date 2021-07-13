<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Log;

class LoadPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:pay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Payment TR_Hist and TR_Sum';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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
}

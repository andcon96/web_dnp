<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use File;

class LoadPO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:po';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load PO Mstr & Det & Approval & Email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // ============ Import Data Header

            // Open CSV File n Read
            $file = fopen(public_path('pomstr.csv'),"r");

            $importData_arr = array();
              $i = 0;

              while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
                 $num = count($filedata );
                 
                 // Skip first row (Remove below comment if you want to skip the first row)
                 /*if($i == 0){
                    $i++;
                    continue; 
                 }*/
                 for ($c=0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata [$c];
                 }
                 $i++;
              }
              fclose($file);

            //dd($importData_arr);
            //Insert or Update to MySQL database
            foreach($importData_arr as $importData){

                // Check Status Kirim PO Supplier Aktif 
                $alertpo = DB::Table('xalert_mstrs')
                            ->where('xalert_mstrs.xalert_supp','=',$importData[3])
                            ->where('xalert_mstrs.xalert_po_app','=','Yes')
                            ->first();

                if(is_null($alertpo)){
                    // Tidak butuh Approval
                    $data1 = array(
                            'xpo_domain'=>$importData[0],
                            'xpo_nbr'=>$importData[1],
                            'xpo_ord_date'=>$importData[2],
                            'xpo_vend'=>$importData[3],
                            'xpo_ship'=>$importData[4],      
                            'xpo_curr'=>$importData[6],
                            'xpo_due_date'=>$importData[5],    
                            'created_at'=> Carbon::now()->toDateTimeString(),    
                            'updated_at'=> Carbon::now()->toDateTimeString(),
                            'xpo_last_conf'=> null,
                            'xpo_total_conf'=> '0',
                            'xpo_total'=>$new_format_total,
                            'xpo_crt_date'=> Carbon::now()->toDateString(),
                            'xpo_status'=>'Approved',   
                            'xpo_ppn' => str_replace(',', '.', $importData[8]),            
                        );
                    DB::table('xpo_mstrs')->insert($data1);

                }else{
                    // Butuh Approval
                // baca po kedaftar ato ga di web.
                $checkdb = DB::table('xpo_mstrs')
                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                ->first();
                if($checkdb){
                    // Data Ada -> Update
                    $checkreapp = DB::table('xpo_control')
                                    ->where('supp_code','=',$importData[3])
                                    ->first();
                    //dd($checkreapp);
                    if($checkreapp != null){
                        // Supplier ada di control   
                        if($checkreapp->reapprove == 'Yes'){
                            //Butuh Reapprove Realert
                            if($importData[9] == 'delete'){
                                // PO Delete, Hapus PO Mstr, PO Dets, Masukin PO Hist, Update SJ jadi Closed
                                $datadet = DB::table('xpod_dets')
                                                ->join('xpo_mstrs','xpod_nbr','=','xpo_nbr')
                                                ->where('xpod_nbr','=',$importData[1])
                                                ->get();
                                foreach($datadet as $datadet){
                                    // Create History 
                                    //dd($datadet->xpo_due_date);
                                    $createhist = DB::table('xpo_hist')
                                                ->insert([
                                                        'xpo_domain' => $datadet->xpod_domain,
                                                        'xpo_nbr' => $datadet->xpod_nbr,
                                                        'xpo_line' => $datadet->xpod_line,
                                                        'xpo_part' => $datadet->xpod_part,
                                                        'xpo_desc' => $datadet->xpod_desc,
                                                        'xpo_um' => $datadet->xpod_um,
                                                        'xpo_qty_ord' => $datadet->xpod_qty_ord,
                                                        'xpo_qty_rcvd' => $datadet->xpod_qty_rcvd,
                                                        'xpo_qty_open' => $datadet->xpod_qty_open,
                                                        'xpo_qty_prom' => $datadet->xpod_qty_prom,
                                                        'xpo_price' => $datadet->xpod_price,
                                                        'xpo_loc' => $datadet->xpod_loc,
                                                        'xpo_lot' => $datadet->xpod_lot,
                                                        'xpo_due_date' => $datadet->xpod_due_date,
                                                        'xpo_vend' => $datadet->xpo_vend,
                                                        'xpo_status' => 'Closed',
                                                        'created_at' => Carbon::now()->toDateTimeString(),
                                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                                ]);   
                                }                             

                                DB::table('xpo_mstrs')
                                        ->where('xpo_nbr','=',$importData[1])
                                        ->delete();
                                DB::table('xpod_dets')
                                        ->where('xpod_nbr','=',$importData[1])
                                        ->delete();
                            }else if($checkdb->xpo_total != str_replace(',', '.', $importData[7])){
                                // Total Beda Ulangin pas Insert
                                $userapp = DB::table('xalert_mstrs')
                                            ->where('xalert_supp','=',$importData[3])
                                            ->where('xalert_active','=','Yes')
                                            ->where('xalert_po_app','=','Yes')
                                            ->first();
                                //dd($userapp);
                                // Supplier Aktif & Approval Yes
                                if($userapp){
                                    // Supplier ada, aktif dan perlu approval
                                    $data = DB::table('xpo_mstrs')
                                                ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                                                ->join('xpo_control','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                                ->join('users','xpo_control.xpo_approver','=','users.id')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->whereRaw(''.str_replace(',', '.', $importData[7]).' between min_amt and max_amt')
                                                ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                        ,'xpo_control.xpo_approver','users.email')
                                                ->orderBy('min_amt','asc')
                                                ->first();


                                    $altapp = DB::table('xpo_mstrs')
                                                ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                                                ->join('xpo_control','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                                ->join('users','xpo_control.xpo_alt_app','=','users.id')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->whereRaw(''.str_replace(',', '.', $importData[7]).' between min_amt and max_amt')
                                                ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                        ,'xpo_control.xpo_alt_app','users.email')
                                                ->orderBy('min_amt','asc')
                                                ->first();
                                    
                                    if($data){
                                        $rev = DB::table('xpo_mstrs')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->first();

                                        $com = DB::table('com_mstr')
                                                ->first();

                                        $revision = (int)$rev->xpo_rev;

                                        $newrev = $revision + 1;

                                        // Butuh Approval
                                        DB::table('xpo_mstrs')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->update([
                                                    'xpo_app_flg' => '1',
                                                    'xpo_rev' => $newrev,
                                                    'xpo_status' => 'UnConfirm',
                                                ]);

                                        $email2app = $data->email.','.$altapp->email;

                                        $array_email = explode(',', $email2app);   

                                        // Kirim Email Notif Approval
                                        Mail::send('emailapproval', 
                                            ['pesan' => 'There are updates on following PO ',
                                             'note1' => $data->xpo_nbr,
                                             'note2' => $rev->xpo_ord_date,
                                             'note3' => $rev->xpo_due_date,
                                             'note4' => $importData[7],
                                             'note5' => 'Approval is needed, Please check.'], 
                                            function ($message) use ($data, $array_email,$com)
                                            {
                                                $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                                                $message->from($com->com_email); // Email Admin Fix
                                                $message->to($array_email);
                                            });
                                    }
                                }

                                $usernoapp = DB::table('xalert_mstrs')
                                            ->where('xalert_supp','=',$importData[3])
                                            ->where('xalert_active','=','Yes')
                                            ->where('xalert_po_app','=','No')
                                            ->first();
                                
                                // Supplier aktif & Approval No      
                                if($usernoapp){
                                        // Baca Supplier Email di Users
                                        $emailsupp = DB::table('users')
                                                ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','users.supp_id')
                                                ->select('email')
                                                ->where('users.supp_id','=',$importData[3])
                                                //->where('id','=',Session::get('userid'))
                                                ->get();

                                        if(count($emailsupp) != 0){
                                            $email = '';
                                            foreach($emailsupp as $emailsupp){
                                                $email .= $emailsupp->email.',';
                                            }

                                            $com = DB::table('com_mstr')
                                                ->first();

                                            $email = substr($email, 0, strlen($email) - 1);

                                            $array_email = explode(',', $email);    


                                            // Kirim Email Notif Ke Setiap Supplier
                                            Mail::send('emailapproval', 
                                            ['pesan' => 'There are updates on following PO :',
                                             'note1' => $importData[1],
                                             'note2' => $importData[2], // Ord Date
                                             'note3' => $importData[5], // Due Date
                                             'note4' => number_format($importData[7],2), // Total
                                             'note5' => 'This update does not require approval.'], 
                                            function ($message) use ($array_email ,$importData,$com)
                                            {
                                                $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                                                $message->from($com->com_email); // Email Admin Fix
                                                $message->to($array_email);
                                            });
                                        }    
                                }

                                // Update History approval , Yang lama reject & buat yang baru
                                /*
                                $oldapprover = DB::table('xpo_app_hist')
                                                    ->where('xpo_app_nbr','=',$importData[1])
                                                    ->update([
                                                        'xpo_app_status' => '3' // Ubah Status list approver jadi reject
                                                    ]);
                                */
                                $listapprover = DB::table('xpo_control')
                                        ->join('xpo_mstrs','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                        ->join('users','xpo_control.xpo_approver','=','users.id')
                                        ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                        ->whereRaw(''.str_replace(',', '.', $importData[7]).' > min_amt and '.str_replace(',', '.', $importData[7]).'< max_amt')
                                        ->select('xpo_mstrs.xpo_nbr','xpo_control.xpo_approver','xpo_control.xpo_alt_app','xpo_control.min_amt','xpo_control.max_amt')
                                        ->orderBy('min_amt','ASC')
                                        ->get();
                            
                                //pindain data trans ke hist klo blom selesai di trans
                                $listtrans = DB::table('xpo_app_trans')
                                        ->where('xpo_app_nbr','=',$importData[1])
                                        ->where('xpo_app_status','!=','0')
                                        ->get();

                                foreach($listtrans as $listtrans){
                                    DB::table('xpo_app_hist')
                                        ->insert([
                                            'xpo_app_nbr' => $importData[1],
                                            'xpo_app_approver' => $listtrans->xpo_app_approver,
                                            'xpo_app_order' => $listtrans->xpo_app_order, // urutan Approval
                                            'xpo_app_status' => $listtrans->xpo_app_status, // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $listtrans->xpo_app_alt_approver
                                        ]);  
                                }

                                // Delete data di xpo_app_trans
                                DB::table('xpo_app_trans')
                                        ->where('xpo_app_nbr','=',$importData[1])
                                        ->delete();

                                if(count($listapprover) == 0){
                                    // Pake General

                                    $general = DB::table('xpo_control')
                                                    ->where('supp_code','=','General')
                                                    ->whereRaw(''.str_replace(',', '.', $importData[7]).' > min_amt and '.str_replace(',', '.', $importData[7]).'< max_amt')
                                                    ->orderBy('min_amt','ASC')
                                                    ->get();
                                    $i = 0;

                                    foreach($general as $general){
                                        $i++;

                                        $result[$i] = [
                                            'xpo_app_nbr' => $importData[1],
                                            'xpo_app_approver' => $general->xpo_approver,
                                            'xpo_app_order' => $i, // urutan Approval
                                            'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $general->xpo_alt_app
                                        ];
                                        // Pindah Table 24072020
                                        //DB::table('xpo_app_hist')->insert($result[$i]);
                                        DB::table('xpo_app_trans')->insert($result[$i]);

                                    }  

                                }else{
                                    // Pake Specific dri $listapprover
                                    $i = 0;

                                    foreach($listapprover as $listapprover){
                                        $i++;

                                        $result[$i] = [
                                            'xpo_app_nbr' => $listapprover->xpo_nbr,
                                            'xpo_app_approver' => $listapprover->xpo_approver,
                                            'xpo_app_order' => $i, // urutan Approval
                                            'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $listapprover->xpo_alt_app
                                        ];
                                        // Pindah Table 24072020
                                        //DB::table('xpo_app_hist')->insert($result[$i]);
                                        DB::table('xpo_app_trans')->insert($result[$i]);

                                    }  
                                }
                            }

                            // Update PO Mstr --> Butuh Approval
                            DB::table('xpo_mstrs')
                                ->where('xpo_nbr', $importData[1])
                                ->update([
                                        'xpo_ord_date' => $importData[2],
                                        'xpo_vend' => $importData[3],
                                        'xpo_ship' => $importData[4],
                                        'xpo_curr' => $importData[6],
                                        'xpo_due_date' => $importData[5],
                                        'xpo_total' => str_replace(',', '.', $importData[7]),
                                        'xpo_ppn' => str_replace(',', '.', $importData[8]),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);

                        }else{
                            // Tidak butuh Reapprove / Realert --> PO Confirm
                            DB::table('xpo_mstrs')
                                ->where('xpo_nbr', $importData[1])
                                ->update([
                                        'xpo_ord_date' => $importData[2],
                                        'xpo_vend' => $importData[3],
                                        'xpo_ship' => $importData[4],
                                        'xpo_curr' => $importData[6],
                                        'xpo_due_date' => $importData[5],
                                        'xpo_total' => str_replace(',', '.', $importData[7]),
                                        'xpo_status' => 'Approved', //Beda Disini
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);
                        }
                    
                    }else{
                        // Pake General
                        $general = DB::table('xpo_control')
                                    ->where('supp_code','=','General')
                                    ->first();
                        //dd($general);
                        if($general->reapprove == 'Yes'){
                        //dd('123');
                            //Butuh Reapprove Realert
                            if($importData[9] == 'delete'){
                                // PO Delete, Hapus PO Mstr, PO Dets, Masukin PO Hist, Update SJ jadi Closed
                                $datadet = DB::table('xpod_dets')
                                                ->join('xpo_mstrs','xpod_nbr','=','xpo_nbr')
                                                ->where('xpod_nbr','=',$importData[1])
                                                ->get();
                                
                                foreach($datadet as $datadet){
                                    // Create History 
                                    //dd('123');
                                    //dd($datadet);
                                    $createhist = DB::table('xpo_hist')
                                                ->insert([
                                                        'xpo_domain' => $datadet->xpod_domain,
                                                        'xpo_nbr' => $datadet->xpod_nbr,
                                                        'xpo_line' => $datadet->xpod_line,
                                                        'xpo_part' => $datadet->xpod_part,
                                                        'xpo_desc' => $datadet->xpod_desc,
                                                        'xpo_um' => $datadet->xpod_um,
                                                        'xpo_qty_ord' => $datadet->xpod_qty_ord,
                                                        'xpo_qty_rcvd' => $datadet->xpod_qty_rcvd,
                                                        'xpo_qty_open' => $datadet->xpod_qty_open,
                                                        'xpo_qty_prom' => $datadet->xpod_qty_prom,
                                                        'xpo_price' => $datadet->xpod_price,
                                                        'xpo_loc' => $datadet->xpod_loc,
                                                        'xpo_lot' => $datadet->xpod_lot,
                                                        'xpo_due_date' => $datadet->xpod_due_date,
                                                        'xpo_vend' => $datadet->xpo_vend,
                                                        'xpo_status' => 'Closed',
                                                        'created_at' => Carbon::now()->toDateTimeString(),
                                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                                ]);   
                                }
                                //dd('123');               
                                DB::table('xpo_mstrs')
                                        ->where('xpo_nbr','=',$importData[1])
                                        ->delete();
                                DB::table('xpod_dets')
                                        ->where('xpod_nbr','=',$importData[1])
                                        ->delete();
                            }else if($checkdb->xpo_total != $importData[7]){
                                // Total Beda Ulangin pas Insert
                                $userapp = DB::table('xalert_mstrs')
                                            ->where('xalert_supp','=',$importData[3])
                                            ->where('xalert_active','=','Yes')
                                            ->where('xalert_po_app','=','Yes')
                                            ->first();
                                
                                // Supplier Aktif & Approval Yes
                                if($userapp){
                                    // Supplier ada, aktif dan perlu approval
                                    $data = DB::table('xpo_mstrs')
                                                ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                                                ->join('xpo_control','xpo_control.supp_code','=',DB::raw('"General"'))
                                                ->join('users','xpo_control.xpo_approver','=','users.id')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->whereRaw(''.str_replace(',', '.', $importData[7]).' between min_amt and max_amt')
                                                ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                        ,'xpo_control.xpo_approver','users.email')
                                                ->orderBy('min_amt','asc')
                                                ->first();
                                    //dd($data);
                                    if($data){
                                        $rev = DB::table('xpo_mstrs')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->first();

                                        $revision = (int)$rev->xpo_rev;

                                        $newrev = $revision + 1;

                                        $com = DB::table('com_mstr')
                                                ->first();

                                        // Butuh Approval
                                        DB::table('xpo_mstrs')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->update([
                                                    'xpo_app_flg' => '1',
                                                    'xpo_rev' => $newrev,
                                                    'xpo_status' => 'UnConfirm'
                                                ]);

                                        // Kirim Email Notif Approval
                                        Mail::send('emailapproval', 
                                            ['pesan' => 'There are updates on following PO ',
                                             'note1' => $data->xpo_nbr,
                                             'note2' => $rev->xpo_ord_date,
                                             'note3' => $rev->xpo_due_date,
                                             'note4' => number_format($rev->xpo_total,2 ),
                                             'note5' => 'Approval is needed. Please check.'], 
                                            function ($message) use ($data,$com)
                                            {
                                                $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                                                $message->from($com->com_email); // Email Admin Fix
                                                $message->to($data->email);
                                            });
                                    }else{
                                        // Butuh Approval
                                        DB::table('xpo_mstrs')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->update([
                                                    'xpo_app_flg' => '1'
                                                ]);

                                        $emailsupp = DB::table('xalert_mstrs')
                                                        ->join('users','users.supp_id','=','xalert_mstrs.xalert_supp')
                                                        ->where('xalert_supp','=',$importData[3])
                                                        ->get();

                                        $email2app = '';

                                        foreach($emailsupp as $emailsupp){
                                            $email2app .= $emailsupp->email.',';
                                        }

                                        $email2app = substr($email2app, 0, strlen($email2app) - 1);

                                        $array_email = explode(',', $email2app);

                                        $com = DB::table('com_mstr')
                                                ->first();

                                        // Kirim Email Notif Approval
                                        Mail::send('emailapproval', 
                                            ['pesan' => 'There is a PO awaiting your approval.',
                                             'note1' => $importData[1],
                                             'note2' => $importData[2], // Ord Date
                                             'note3' => $importData[5], // Due Date
                                             'note4' => number_format($importData[7],2 ), // Total
                                             'note5' => 'Please check.'], 
                                            function ($message) use ($array_email,$com)
                                            {
                                                $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                                                $message->from($com->com_email); // Email Admin Fix
                                                $message->to($array_email);
                                            });
                                    }
                                }

                                $usernoapp = DB::table('xalert_mstrs')
                                            ->where('xalert_supp','=',$importData[3])
                                            ->where('xalert_active','=','Yes')
                                            ->where('xalert_po_app','=','No')
                                            ->first();
                                
                                // Supplier aktif & Approval No      
                                if($usernoapp){
                                        // Baca Supplier Email di Users
                                        $emailsupp = DB::table('users')
                                                ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','users.supp_id')
                                                ->select('email')
                                                ->where('users.supp_id','=',$importData[3])
                                                //->where('id','=',Session::get('userid'))
                                                ->get();

                                        if(count($emailsupp) != 0){
                                            $email = '';
                                            foreach($emailsupp as $emailsupp){
                                                $email .= $emailsupp->email.',';
                                            }

                                            $email = substr($email, 0, strlen($email) - 1);

                                            $array_email = explode(',', $email); 

                                            $com = DB::table('com_mstr')
                                                ->first();

                                            // Kirim Email Notif Ke Setiap Supplier
                                            Mail::send('emailapproval', 
                                            ['pesan' => 'There are update on following PO',
                                             'note1' => $importData[1],
                                             'note2' => $importData[2], // Ord Date
                                             'note3' => $importData[5], // Due Date
                                             'note4' => number_format($importData[7],2 ), // Total
                                             'note5' => 'This update does not require approval.'], 
                                            function ($message) use ($array_email ,$importData,$com)
                                            {
                                                $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                                                $message->from($com->com_email); // Email Admin Fix
                                                $message->to($array_email);
                                            });
                                        }    
                                }

                                // Update History approval , Yang lama reject & buat yang baru
                                /*
                                $oldapprover = DB::table('xpo_app_hist')
                                                    ->where('xpo_app_nbr','=',$importData[1])
                                                    ->update([
                                                        'xpo_app_status' => '3' // Ubah Status list approver jadi reject
                                                    ]);
                                */
                                $listapprover = DB::table('xpo_control')
                                        ->join('xpo_mstrs','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                        ->join('users','xpo_control.xpo_approver','=','users.id')
                                        ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                        ->whereRaw(''.str_replace(',', '.', $importData[7]).' > min_amt and '.str_replace(',', '.', $importData[7]).'< max_amt')
                                        ->select('xpo_mstrs.xpo_nbr','xpo_control.xpo_approver','xpo_control.xpo_alt_app')
                                        ->orderBy('min_amt','ASC')
                                        ->get();
                                
                                if(count($listapprover) == 0){
                                    // Pake General

                                    $general = DB::table('xpo_control')
                                                    ->where('supp_code','=','General')
                                                    ->whereRaw(''.str_replace(',', '.', $importData[7]).' > min_amt and '.str_replace(',', '.', $importData[7]).'< max_amt')
                                                    ->orderBy('min_amt','ASC')
                                                    ->get();
                                    $i = 0;

                                    foreach($general as $general){
                                        $i++;

                                        $result[$i] = [
                                            'xpo_app_nbr' => $importData[1],
                                            'xpo_app_approver' => $general->xpo_approver,
                                            'xpo_app_order' => $i, // urutan Approval
                                            'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $general->xpo_alt_app
                                        ];
                                        // Pindah Table 24072020
                                        //DB::table('xpo_app_hist')->insert($result[$i]);
                                        DB::table('xpo_app_trans')->insert($result[$i]);

                                    }  

                                }else{
                                    // Pake Specific dri $listapprover
                                    $i = 0;

                                    foreach($listapprover as $listapprover){
                                        $i++;

                                        $result[$i] = [
                                            'xpo_app_nbr' => $listapprover->xpo_nbr,
                                            'xpo_app_approver' => $listapprover->xpo_approver,
                                            'xpo_app_order' => $i, // urutan Approval
                                            'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                            'xpo_app_alt_approver' => $listapprover->xpo_alt_app
                                        ];
                                        // Pindah Table 24072020
                                        //DB::table('xpo_app_hist')->insert($result[$i]);
                                        DB::table('xpo_app_trans')->insert($result[$i]);

                                    }  
                                }
                            }

                            // Update PO Mstr --> Butuh Approval
                            DB::table('xpo_mstrs')
                                ->where('xpo_nbr', $importData[1])
                                ->update([
                                        'xpo_ord_date' => $importData[2],
                                        'xpo_vend' => $importData[3],
                                        'xpo_ship' => $importData[4],
                                        'xpo_curr' => $importData[6],
                                        'xpo_due_date' => $importData[5],
                                        'xpo_total' => str_replace(',', '.', $importData[7]),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);
                        }else{
                            // Tidak butuh Reapprove / Realert --> PO Confirm
                            DB::table('xpo_mstrs')
                                ->where('xpo_nbr', $importData[1])
                                ->update([
                                        'xpo_ord_date' => $importData[2],
                                        'xpo_vend' => $importData[3],
                                        'xpo_ship' => $importData[4],
                                        'xpo_curr' => $importData[6],
                                        'xpo_due_date' => $importData[5],
                                        'xpo_total' => str_replace(',', '.', $importData[7]),
                                        'xpo_status' => 'Approved', //Beda Disini
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);

                            
                        }
                    }

                }else{
                    // Data tidak ada -> Insert

                    if($importData[9] != 'delete'){
                        $new_format_total = str_replace(',', '.', $importData[7]);

                        $data1 = array(
                            'xpo_domain'=>$importData[0],
                            'xpo_nbr'=>$importData[1],
                            'xpo_ord_date'=>$importData[2],
                            'xpo_vend'=>$importData[3],
                            'xpo_ship'=>$importData[4],      
                            'xpo_curr'=>$importData[6],
                            'xpo_due_date'=>$importData[5],    
                            'created_at'=> Carbon::now()->toDateTimeString(),    
                            'updated_at'=> Carbon::now()->toDateTimeString(),
                            'xpo_last_conf'=> null,
                            'xpo_total_conf'=> '0',
                            'xpo_total'=>$new_format_total,
                            'xpo_crt_date'=> Carbon::now()->toDateString(),
                            'xpo_status'=>'UnConfirm',   
                            'xpo_ppn' => str_replace(',', '.', $importData[8]),            
                        );
                        DB::table('xpo_mstrs')->insert($data1);

                        $userapp = DB::table('xalert_mstrs')
                                        ->where('xalert_supp','=',$importData[3])
                                        ->where('xalert_active','=','Yes')
                                        ->where('xalert_po_app','=','Yes')
                                        ->first();
                            
                            // Supplier Aktif & Approval Yes
                            if($userapp){
                                // Supplier ada, aktif dan perlu approval
                                $data = DB::table('xpo_mstrs')
                                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                                            ->join('xpo_control','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                            ->join('users','xpo_control.xpo_approver','=','users.id')
                                            ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                            ->whereRaw('xpo_total between min_amt and max_amt')
                                            ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                    ,'xpo_control.xpo_approver','users.email')
                                            ->orderBy('min_amt','asc')
                                            ->first();

                                $altapp = DB::table('xpo_mstrs')
                                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                                            ->join('xpo_control','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                            ->join('users','xpo_control.xpo_alt_app','=','users.id')
                                            ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                            ->whereRaw(''.str_replace(',', '.', $importData[7]).' between min_amt and max_amt')
                                            ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                    ,'xpo_control.xpo_alt_app','users.email')
                                            ->orderBy('min_amt','asc')
                                            ->first();

                                    if($data){
                                            // Butuh Approval Specific
                                            DB::table('xpo_mstrs')
                                                    ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                    ->update([
                                                        'xpo_app_flg' => '1'
                                                    ]);

                                            $email2app = $data->email.','.$altapp->email;

                                            $array_email = explode(',', $email2app);

                                              $com = DB::table('com_mstr')
                                                ->first();


                                            // Kirim Email Notif Approval
                                            Mail::send('emailapproval', 
                                                ['pesan' => 'There is a PO awaiting your approval',
                                                 'note1' => $data->xpo_nbr,
                                                 'note2' => $importData[2], // Ord Date
                                                 'note3' => $importData[5], // Due Date
                                                 'note4' => number_format($importData[7],2 ), // Total
                                                 'note5' => 'Please check.'], 
                                                function ($message) use ($array_email,$importData,$com)
                                                {
                                                    $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                                                    $message->from($com->com_email); // Email Admin Fix
                                                    $message->to($array_email);
                                                });
                                    }else{
                                        // Butuh Approval General
                                        DB::table('xpo_mstrs')
                                                ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                ->update([
                                                    'xpo_app_flg' => '1'
                                                ]);

                                        $appgeneral = DB::table('xpo_mstrs')
                                                    ->join('xpo_control','xpo_control.supp_code','=',DB::raw('"General"'))
                                                    ->join('users','xpo_control.xpo_approver','=','users.id')
                                                    ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                    ->whereRaw('xpo_total between min_amt and max_amt')
                                                    ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                            ,'xpo_control.xpo_approver','users.email')
                                                    ->orderBy('min_amt','asc')
                                                    ->first();

                                        $appaltgeneral = DB::table('xpo_mstrs')
                                                    ->join('xpo_control','xpo_control.supp_code','=',DB::raw('"General"'))
                                                    ->join('users','xpo_control.xpo_alt_app','=','users.id')
                                                    ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                                    ->whereRaw('xpo_total between min_amt and max_amt')
                                                    ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                                            ,'xpo_control.xpo_approver','users.email')
                                                    ->orderBy('min_amt','asc')
                                                    ->first();

                                        if($appgeneral){
                                            $email2app = $appgeneral->email.','.$appaltgeneral->email;

                                            $array_email = explode(',', $email2app);

                                             $com = DB::table('com_mstr')
                                                ->first();

                                            //dd($email2app);
                                            //dd($emailapprover,$emailaltapp);

                                            // Kirim Email Notif Approval
                                            Mail::send('emailapproval', 
                                                ['pesan' => 'There is a PO awaiting your approval',
                                                 'note1' => $importData[1],
                                                 'note2' => $importData[2], // Ord Date
                                                 'note3' => $importData[5], // Due Date
                                                 'note4' => number_format($importData[7],2 ), // Total
                                                 'note5' => 'Please check.'], 
                                                function ($message) use ($array_email,$importData,$com)
                                                {
                                                    $message->subject('PhD - Purchase Order Approval Task - '.$com->com_name);
                                                    $message->from($com->com_email); // Email Admin Fix
                                                    $message->to($array_email);
                                                });
                                        }
                                        
                                    }
                            }

                        $usernoapp = DB::table('xalert_mstrs')
                                        ->where('xalert_supp','=',$importData[3])
                                        ->where('xalert_active','=','Yes')
                                        ->where('xalert_po_app','=','No')
                                        ->first();
                        
                            // Supplier aktif & Approval No      
                            if($usernoapp){
                                    //dd('123');
                                    // Baca Supplier Email di Users
                                    $emailsupp = DB::table('users')
                                            ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','users.supp_id')
                                            ->select('email')
                                            ->where('users.supp_id','=',$importData[3])
                                            //->where('id','=',Session::get('userid'))
                                            ->get();

                                    if(count($emailsupp) != 0){
                                        $email = '';
                                        foreach($emailsupp as $emailsupp){
                                            $email .= $emailsupp->email.',';
                                        }

                                        $email = substr($email, 0, strlen($email) - 1);

                                        $array_email = explode(',', $email);   

                                        $com = DB::table('com_mstr')
                                                ->first();

                                        // Kirim Email Notif Ke Setiap Supplier
                                        Mail::send('emailapproval', 
                                        ['pesan' => 'There a new PO for you',
                                         'note1' => $importData[1],
                                         'note2' => $importData[2], // Ord Date
                                         'note3' => $importData[5], // Due Date
                                         'note4' => number_format($importData[7],2 ), // Total
                                         'note5' => 'Please check.'], 
                                        function ($message) use ($array_email ,$importData,$com)
                                        {
                                            $message->subject('PhD - Purchase Order - '.$com->com_name);
                                            $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                                            $message->to($array_email);
                                        });
                                    }    
                            }


                        $ubahstatus = DB::table('xpo_mstrs')
                                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                                    ->join('xpo_control','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                    ->join('users','xpo_control.xpo_approver','=','users.id')
                                    ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                    ->whereRaw('xpo_total between min_amt and max_amt')
                                    ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                            ,'xpo_control.xpo_approver','users.email')
                                    ->orderBy('min_amt','asc')
                                    ->first();

                        $ubahstatusgeneral = DB::table('xpo_mstrs')
                                    ->join('xpo_control','xpo_control.supp_code','=',DB::raw('"General"'))
                                    ->join('users','xpo_control.xpo_approver','=','users.id')
                                    ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                    ->whereRaw('xpo_total between min_amt and max_amt')
                                    ->select('xpo_mstrs.xpo_nbr','xpo_mstrs.xpo_total','xpo_control.min_amt','xpo_control.max_amt'
                                            ,'xpo_control.xpo_approver','users.email')
                                    ->orderBy('min_amt','asc')
                                    ->first();

                            // Supplier Aktif & PO tidak perlu Approval
                            if(is_null($ubahstatus) && is_null($ubahstatusgeneral)){
                                DB::table('xpo_mstrs')
                                        ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                        ->update([
                                            'xpo_mstrs.xpo_status' => 'Approved'
                                        ]);
                            }

                        // Buat History untuk approval
                        $listapprover = DB::table('xpo_control')
                                    ->join('xpo_mstrs','xpo_control.supp_code','=','xpo_mstrs.xpo_vend')
                                    ->join('users','xpo_control.xpo_approver','=','users.id')
                                    ->where('xpo_mstrs.xpo_nbr','=',$importData[1])
                                    ->whereRaw(''.str_replace(',', '.', $importData[7]).' > min_amt and '.str_replace(',', '.', $importData[7]).'< max_amt')
                                    ->select('xpo_mstrs.xpo_nbr','xpo_control.xpo_approver','xpo_control.xpo_alt_app')
                                    ->orderBy('min_amt','ASC')
                                    ->get();
                        
                        if(count($listapprover) == 0){
                            // Pake General
                            
                            $general = DB::table('xpo_control')
                                            ->where('supp_code','=','General')
                                            ->whereRaw(''.str_replace(',', '.', $importData[7]).' > min_amt and '.str_replace(',', '.', $importData[7]).'< max_amt')
                                            ->orderBy('min_amt','ASC')
                                            ->get();
                            $i = 0;
                            
                            foreach($general as $general){
                                $i++;

                                $result[$i] = [
                                    'xpo_app_nbr' => $importData[1],
                                    'xpo_app_approver' => $general->xpo_approver,
                                    'xpo_app_order' => $i, // urutan Approval
                                    'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                    'xpo_app_alt_approver' => $general->xpo_alt_app
                                ];
                                // Pindah Table 24072020
                                //DB::table('xpo_app_hist')->insert($result[$i]);
                                DB::table('xpo_app_trans')->insert($result[$i]);

                            }  

                        }else{
                            // Pake Specific dri $listapprover
                           // dd('Specific');
                            $i = 0;
                            foreach($listapprover as $listapprover){
                                $i++;

                                $result[$i] = [
                                    'xpo_app_nbr' => $listapprover->xpo_nbr,
                                    'xpo_app_approver' => $listapprover->xpo_approver,
                                    'xpo_app_order' => $i, // urutan Approval
                                    'xpo_app_status' => '0', // 0 Waiting , 1 Approved , 2 Reject
                                    'xpo_app_alt_approver' => $listapprover->xpo_alt_app
                                ];
                                // Pindah Table 24072020
                                //DB::table('xpo_app_hist')->insert($result[$i]);
                                DB::table('xpo_app_trans')->insert($result[$i]);

                            }    
                        }
                }
                }

                
            }





            // ============ Import Data Details

            // Open CSV File n Read
            $file = fopen(public_path('poddet.csv'),"r");

            $importData_arr = array();
              $i = 0;

              while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
                 $num = count($filedata );
                 
                 // Skip first row (Remove below comment if you want to skip the first row)
                 /*if($i == 0){
                    $i++;
                    continue; 
                 }*/
                 for ($c=0; $c < $num; $c++) {
                    $importData_arr[$i][] = $filedata [$c];
                 }
                 $i++;
              }
              fclose($file);

            //dd($importData_arr);

            // Insert or Update to MySQL database
            foreach($importData_arr as $importData){
                if($importData[10] == null ){
                    $newdate = '2020-01-01';
                }else{
                    //$date = date_create_from_format('d/m/Y', $importData[10]);
                    //$newdate = $date->format('Y-m-d');
                    $newdate = $importData[10];
                }

                //dd($newdate);
                if($importData[13] == 'delete'){

                    // Delete Row, Masukin Hist
                    $datadetail = DB::table('xpo_mstrs')
                                    ->join('xpod_dets','xpo_mstrs.xpo_nbr','=','xpod_dets.xpod_nbr')
                                    ->where('xpod_nbr','=',$importData[1])
                                    ->where('xpod_line','=',$importData[2])
                                    ->get();

                    if(is_null($datadetail)){
                        // tidak ada data
                    }else{
                        // ada data
                        foreach($datadetail as $datadetail){
                        DB::table('xpo_hist')
                            ->insert([
                                    'xpo_domain' => $datadetail->xpod_domain,
                                    'xpo_nbr' => $datadetail->xpod_nbr,
                                    'xpo_line' => $datadetail->xpod_line,
                                    'xpo_part' => $datadetail->xpod_part,
                                    'xpo_desc' => $datadetail->xpod_desc,
                                    'xpo_um' => $datadetail->xpod_um,
                                    'xpo_qty_ord' => $datadetail->xpod_qty_ord,
                                    'xpo_qty_rcvd' => $datadetail->xpod_qty_rcvd,
                                    'xpo_qty_open' => $datadetail->xpod_qty_open,
                                    'xpo_qty_prom' => $datadetail->xpod_qty_prom,
                                    'xpo_price' => $datadetail->xpod_price,
                                    'xpo_loc' => $datadetail->xpod_loc,
                                    'xpo_lot' => $datadetail->xpod_lot,
                                    'xpo_due_date' => $datadetail->xpod_due_date,
                                    'xpo_vend' => $datadetail->xpo_vend,
                                    'xpo_status' => 'Closed',
                                    'created_at' => Carbon::now()->toDateTimeString(),
                                    'updated_at' => Carbon::now()->toDateTimeString(), 
                            ]);

                        }

                        DB::table('xpod_dets')
                                    ->where('xpod_nbr','=',$importData[1])
                                    ->where('xpod_line','=',$importData[2])
                                    ->delete();
                    }
                }else{
                    DB::table('xpod_dets')->updateOrInsert(
                        ['xpod_domain' => $importData[0], 'xpod_nbr' => $importData[1], 'xpod_line' => $importData[2] ],
                        ['xpod_part' => $importData[3], 
                         'xpod_desc' => $importData[4],
                         'xpod_um' => $importData[5],
                         'xpod_qty_ord' => $importData[6],
                         'xpod_qty_rcvd' => '0',
                         'xpod_qty_open' => $importData[6],
                         'xpod_qty_prom' => $importData[6],
                         'xpod_price' => $importData[7],
                         'xpod_loc' => $importData[8],
                         'xpod_lot' => $importData[9],
                         'xpod_due_date' => $importData[10],
                         'xpod_date' => $newdate,
                         'created_at' => Carbon::now()->toDateTimeString(),
                         'updated_at' =>  Carbon::now()->toDateTimeString()
                     ]);


                    $suppname = DB::table('xpo_mstrs')
                                ->where('xpo_nbr','=',$importData[1])
                                ->get();
                    
                    foreach($suppname as $suppname){
                        $vendor = $suppname->xpo_vend;
                        $status = $suppname->xpo_status;
                        if(is_null($newdate)){
                            $newdate = '2020-01-1';
                        }
                        // Create History 
                        DB::table('xpo_hist')
                                ->insert([
                                        'xpo_domain' => $importData[0],
                                        'xpo_nbr' => $importData[1],
                                        'xpo_line' => $importData[2],
                                        'xpo_part' => $importData[3],
                                        'xpo_desc' => $importData[4],
                                        'xpo_um' => $importData[5],
                                        'xpo_qty_ord' => $importData[6],
                                        'xpo_qty_rcvd' => '0',
                                        'xpo_qty_open' => $importData[6],
                                        'xpo_qty_prom' => $importData[6],
                                        'xpo_price' => $importData[7],
                                        'xpo_loc' => $importData[8],
                                        'xpo_lot' => $importData[9],
                                        'xpo_due_date' => $newdate,
                                        'xpo_vend' => $vendor,
                                        'xpo_status' => $status,
                                        'created_at' => Carbon::now()->toDateTimeString(),
                                        'updated_at' => Carbon::now()->toDateTimeString(),
                                ]);
                        
                    }
                    
                }
            }

        }
    }
}

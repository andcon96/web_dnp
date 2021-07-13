<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotifPO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:po';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim Email kalo PO mendekati parameter';

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
        $data = DB::table('xalert_mstrs')
                    ->where('xalert_active','=','Yes')
                    ->get();
        foreach($data as $data){
            
            if($data->xalert_day1 != null){
                //$stringnew .= '1';
                $po = DB::table('xpo_mstrs')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                        ->where('xpo_mstrs.xpo_vend','=',$data->xalert_supp)
                        ->whereRaw('datediff(xpo_mstrs.xpo_due_date,CURDATE()) = '.$data->xalert_day1.'')
                        ->get();

                $com = DB::table('com_mstr')
                        ->first();

                foreach($po as $po){
                    $array_email = explode(',', $po->xalert_email1);   

                    // Kirim Email Notif Approval
                    Mail::send('emailnotif', 
                        ['pesan' => 'There is an upcoming due for purchase order: ',
                         'note1' => $po->xpo_nbr,
                         'note2' => $po->xpo_ord_date,
                         'note3' => $po->xpo_due_date,
                         'note4' => number_format($po->xpo_total,2),
                         'note5' => $data->xalert_day1,
                         'note6' => 'Please check.'], 
                        function ($message) use ($po, $array_email,$data,$com)
                        {
                            $message->subject('PhD - Purchase Order Due in '.$data->xalert_day1.' day(s) - '.$com->com_name);
                            $message->from($com->com_email); // Email Admin Fix
                            $message->to($array_email);
                        });
       
                }
            }

            if($data->xalert_day2 != null){
                //$stringnew .= '1';
                $po = DB::table('xpo_mstrs')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                        ->where('xpo_mstrs.xpo_vend','=',$data->xalert_supp)
                        ->whereRaw('datediff(xpo_mstrs.xpo_due_date,CURDATE()) = '.$data->xalert_day2.'')
                        ->get();

                $com = DB::table('com_mstr')
                        ->first();


                foreach($po as $po){
                    $array_email = explode(',', $po->xalert_email2);   

                    // Kirim Email Notif Approval
                    Mail::send('emailnotif', 
                        ['pesan' => 'There is an upcoming due for purchase order: ',
                         'note1' => $po->xpo_nbr,
                         'note2' => $po->xpo_ord_date,
                         'note3' => $po->xpo_due_date,
                         'note4' => number_format($po->xpo_total,2),
                         'note5' => $data->xalert_day2,
                         'note6' => 'Please check.'], 
                        function ($message) use ($po, $array_email,$data,$com)
                        {
                            $message->subject('PhD - Purchase Order Due in '.$data->xalert_day2.' day(s) -'.$com->com_name);
                            $message->from($com->com_email); // Email Admin Fix
                            $message->to($array_email);
                        });
                }
            }

            if($data->xalert_day3 != null){
                //$stringnew .= '1';
                $po = DB::table('xpo_mstrs')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                        ->where('xpo_mstrs.xpo_vend','=',$data->xalert_supp)
                        ->whereRaw('datediff(xpo_mstrs.xpo_due_date,CURDATE()) = '.$data->xalert_day3.'')
                        ->get();

                $com = DB::table('com_mstr')
                        ->first();

                foreach($po as $po){
                    $array_email = explode(',', $po->xalert_email3);   

                    // Kirim Email Notif Approval
                    Mail::send('emailnotif', 
                        ['pesan' => 'There is an upcoming due for purchase order: ',
                         'note1' => $po->xpo_nbr,
                         'note2' => $po->xpo_ord_date,
                         'note3' => $po->xpo_due_date,
                         'note4' => number_format($po->xpo_total,2),
                         'note5' => $data->xalert_day3,
                         'note6' => 'Please check.'], 
                        function ($message) use ($po, $array_email,$data,$com)
                        {
                            $message->subject('PhD - Purchase Order Due in '.$data->xalert_day3.' day(s) - '.$com->com_name);
                            $message->from($com->com_email); // Email Admin Fix
                            $message->to($array_email);
                        });
       
                }
            }

            if($data->xalert_day4 != null){
                //$stringnew .= '1';
                $po = DB::table('xpo_mstrs')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                        ->where('xpo_mstrs.xpo_vend','=',$data->xalert_supp)
                        ->whereRaw('datediff(xpo_mstrs.xpo_due_date,CURDATE()) = '.$data->xalert_day4.'')
                        ->get();
                $com = DB::table('com_mstr')
                        ->first();


                foreach($po as $po){
                    $array_email = explode(',', $po->xalert_email4);   

                    // Kirim Email Notif Approval
                    Mail::send('emailnotif', 
                        ['pesan' => 'There is an upcoming due for purchase order: ',
                         'note1' => $po->xpo_nbr,
                         'note2' => $po->xpo_ord_date,
                         'note3' => $po->xpo_due_date,
                         'note4' => number_format($po->xpo_total,2),
                         'note5' => $data->xalert_day4,
                         'note6' => 'Please check.'], 
                        function ($message) use ($po, $array_email,$data,$com)
                        {
                            $message->subject('PhD - Purchase Order Due in '.$data->xalert_day4.' day(s) - '.$com->com_name);
                            $message->from($com->com_email); // Email Admin Fix
                            $message->to($array_email);
                        });
       
                }
            }

            if($data->xalert_day5 != null){
                //$stringnew .= '1';
                $po = DB::table('xpo_mstrs')
                        ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                        ->where('xpo_mstrs.xpo_vend','=',$data->xalert_supp)
                        ->whereRaw('datediff(xpo_mstrs.xpo_due_date,CURDATE()) = '.$data->xalert_day5.'')
                        ->get();

                $com = DB::table('com_mstr')
                        ->first();

                foreach($po as $po){
                    $array_email = explode(',', $po->xalert_email5);   

                    // Kirim Email Notif Approval
                    Mail::send('emailnotif', 
                        ['pesan' => 'There is an upcoming due for purchase order: ',
                         'note1' => $po->xpo_nbr,
                         'note2' => $po->xpo_ord_date,
                         'note3' => $po->xpo_due_date,
                         'note4' => number_format($po->xpo_total,2),
                         'note5' => $data->xalert_day5,
                         'note6' => 'Please check.'], 
                        function ($message) use ($po, $array_email,$data,$com)
                        {
                            $message->subject('PhD - Purchase Order Due in '.$data->xalert_day5.' day(s) - '.$com->com_name);
                            $message->from($com->com_email); // Email Admin Fix
                            $message->to($array_email);
                        });
       
                }
            }
        }

    }
}

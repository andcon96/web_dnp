<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon;

class NotifIdlePO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:idlepo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim Email Jika status PO masi approved blom konfirm masuk menu shipper confirm';

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
        // Scedule Idle PO
        $data = DB::table('xpo_mstrs')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                    ->join('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                    //->where('xalert_supp','=','10S1003')
                    ->where('xpo_status','=','Approved')
                    ->whereRaw('xpo_app_date is not null')
                    ->selectRaw('xpo_nbr, max(xpo_app_date) as appdate, xalert_idle_emails, xpo_vend, xalert_idle_days, xpo_due_date, xpo_ord_date, xpo_total')
                    ->orderBy('xpo_nbr')
                    ->orderBy('xpo_app_date','Desc')
                    ->groupBy('xpo_mstrs.xpo_nbr')
                    ->get();

        $com = DB::table('com_mstr')
                    ->first();



        foreach($data as $data){
            if(\Carbon\Carbon::parse( $data->appdate )->diffInDays(today()) >= $data->xalert_idle_days){
                //sudah lewat idle days
                $array_email = explode(',', $data->xalert_idle_emails);   
                // Kirim Email Notif Approval
                Mail::send('emailidle', 
                     ['pesan' => 'There is a PO awaiting your confirmation:',
                     'note1' => $data->xpo_nbr,
                     'note2' => $data->xpo_ord_date,
                     'note3' => $data->xpo_due_date,
                     'note4' => number_format($data->xpo_total,2),
                     'note5' => Carbon\Carbon::parse( $data->appdate )->diffInDays(today()),
                     'note6' => 'Please check.'], 
                    function ($message) use ($data, $array_email,$com)
                    {
                        $message->subject('PhD - Idle Purchase Order '.Carbon\Carbon::parse( $data->appdate )->diffInDays(today()).' day(s)'.$com->com_name);
                        $message->from($com->com_email); // Email Admin Fix
                        $message->to($array_email);
                    });
                
            }else{
                // Belum lewat idle days
            }
        }

        // Schedule PO Approve ga pke approval 
        $data1 = DB::table('xpo_mstrs')
                    ->join('xalert_mstrs','xalert_mstrs.xalert_supp','=','xpo_mstrs.xpo_vend')
                    ->leftJoin('xpo_app_hist','xpo_app_hist.xpo_app_nbr','=','xpo_mstrs.xpo_nbr')
                    //->where('xalert_supp','=','10S1003')
                    ->where('xpo_status','=','Approved')
                    ->whereRaw('xpo_app_date is null')
                    ->selectRaw('xpo_nbr, max(xpo_app_date) as appdate, xalert_idle_emails, xpo_vend, xalert_idle_days, xpo_due_date, xpo_ord_date, xpo_total,created_at')
                    ->orderBy('xpo_nbr')
                    ->orderBy('xpo_app_date','Desc')
                    ->groupBy('xpo_mstrs.xpo_nbr')
                    ->get();

        foreach($data1 as $data1){
            if(\Carbon\Carbon::parse( $data1->created_at )->diffInDays(today()) >= $data1->xalert_idle_days){
                //sudah lewat idle days
                $array_email = explode(',', $data->xalert_idle_emails);   
                // Kirim Email Notif Approval
                Mail::send('emailidle', 
                     ['pesan' => 'There is a PO awaiting your confirmation:',
                     'note1' => $data1->xpo_nbr,
                     'note2' => $data1->xpo_ord_date,
                     'note3' => $data1->xpo_due_date,
                     'note4' => number_format($data1->xpo_total,2),
                     'note5' => Carbon\Carbon::parse( $data1->created_at )->diffInDays(today()),
                     'note6' => 'Please check.'], 
                    function ($message) use ($data1, $array_email,$com)
                    {
                        $message->subject('PhD - Idle Purchase Order '.Carbon\Carbon::parse( $data1->created_at )->diffInDays(today()).' day(s)'.$com->com_name);
                        $message->from($com->com_email); // Email Admin Fix
                        $message->to($array_email);
                    });
                
            }else{
                // Belum lewat idle days
            }
        }

    }
}

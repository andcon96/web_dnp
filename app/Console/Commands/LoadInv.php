<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use File;

class LoadInv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:inv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Inv Mstr & Det';

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
        DB::table('xinv_mstr')->delete();
      DB::table('xinvd_det')->delete();
      
    //exec("start cmd /c inv.bat"); 
       $file = fopen(public_path('inv.csv'),"r");

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
             
     
        // Insert to MySQL database
        foreach($importData_arr as $importData){  
       
            $checkdb = DB::table('xsite_mstr')
                              ->where('xsite_mstr.xsite_site','=',$importData[3])
                              ->first();
                            
            $pc1 = DB::table('xitem_mstr')
                              ->where('xitem_mstr.xitem_part','=',$importData[1])
                              ->first();        
            $com = DB::table('com_mstr')
                              ->first();        
            if ($pc1) {
            $pct = $importData[2] + ($importData[2] * $pc1->xitem_sfty) / 100;
                if($importData[4] > $importData[2] and $importData[4] < $pct) {
                  $ss = "Y";
                        Mail::send('emailexp', 
                          ['pesan' => 'There is an item that going to expired',
                          'note1' => $importData[1],
                          'note2' => $importData[4],
                          'note3' => $importData[3],
                          'note4' => $importData[2],                  
                          'note7' => 'Please check'], 
                          function ($message) use ($show,$com)
                          {
                              $message->subject('Web Support IMI Notification');
                              $message->from($com->com_email); // Email Admin Fix
                              $message->to($show->xitem_sfty_email);
                          });          
              
                }
                else {
                    $ss = "N";
                }   
                                
            }   
            if ($checkdb) {
                     $checkitm = DB::table('xitem_mstr')
                            ->where('xitem_mstr.xitem_part','=',$importData[1])
                            ->first();
                     if ($checkitm) {       
                           DB::table('xinv_mstr')->updateOrInsert(
                               ['xinv_domain' => $importData[0], 'xinv_part' => $importData[1],'xinv_site' => $importData[3] ],
                               ['xinv_sft_stock' => $importData[2],                                 
                                'xinv_qty_oh' => $importData[4],
                                'xinv_qty_ord' => $importData[5],
                                'xinv_qty_req' => $importData[6],
                                'xinv_ss' => $importData[7],
                                'xinv_ss_pct' => $ss,                 
                            ]);
                     }
            }  
        }
        
        $file = fopen(public_path('invdet.csv'),"r");

        $importData_arrx = array();
          $i = 0;

          while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
             $num = count($filedata );
             
             // Skip first row (Remove below comment if you want to skip the first row)
             /*if($i == 0){
                $i++;
                continue; 
             }*/
             for ($c=0; $c < $num; $c++) {
                $importData_arrx[$i][] = $filedata [$c];
             }
             $i++;
          }
          fclose($file);
                  
        // Insert to MySQL database
        $site = DB::table('xsite_mstr')->get(); 
        
        foreach($importData_arrx as $importData){  
        
             $checkdb = DB::table('xsite_mstr')
                            ->where('xsite_mstr.xsite_site','=',$importData[1])
                            ->first();
                            
            $importData11 = str_replace(",", "." , $importData[11]);        
            if ($checkdb) {
                $checkitm = DB::table('xitem_mstr')
                            ->where('xitem_mstr.xitem_part','=',$importData[2])
                            ->first();
                     if ($checkitm) {    
                        DB::table('xinvd_det')->updateOrInsert(
                            ['xinvd_domain' => $importData[0], 'xinvd_site' => $importData[1], 'xinvd_part' => $importData[2], 'xinvd_loc' => $importData[3], 
                             'xinvd_lot' => $importData[4] ],
                            ['xinvd_ref' => $importData[5],
                             'xinvd_qty_oh' => $importData[6],
                             'xinvd_qty_all' => $importData[7],
                             'xinvd_expire' => $importData[8],
                             'xinvd_ed' => $importData[9],  
                             'xinvd_days' => $importData[10],
                             'xinvd_amt' => $importData11,               
                         ]);
                     }
            }  
         
         /*============alert3============*/
        $data = DB::table('xitem_mstr')                           
                            ->where('xitem_mstr.xitem_part','=',$importData[2])
                            ->where('xitem_mstr.xitem_day1','=',$importData[10])
                            ->where('xitem_mstr.xitem_day_email1','!=', "")
                            ->get();       
        $com = DB::table('com_mstr')
                          ->first();
                      
        if(count($data) != 0){
            foreach ($data as $show){                                                             
                          Mail::send('emailexp', 
                          ['pesan' => 'There is an item that going to expired',
                          'note1' => $importData[2],
                          'note2' => $importData[6],
                          'note3' => $importData[3],
                          'note4' => $importData[4],
                          'note5' => $importData[5],
                          'note6' => $importData[10].' Days',
                          'note7' => 'Please check'], 
                          function ($message) use ($show,$com)
                          {
                              $message->subject('Web Support IMI Notification');
                              $message->from($com->com_email); // Email Admin Fix
                              $message->to($show->xitem_day_email1);
                          });                    
               }              
            }
              
            //dd('123');
            
            $data2 = DB::table('xitem_mstr')
                            ->where('xitem_mstr.xitem_part','=',$importData[2])
                            ->where('xitem_mstr.xitem_day2','=',$importData[10])
                            ->where('xitem_mstr.xitem_day_email2','!=', "")
                            ->get();      
           
        if(count($data2) != 0){
         
            foreach ($data2 as $show){                                                               
                          Mail::send('emailexp', 
                          ['pesan' => 'There is an item that going to expired',
                          'note1' => $importData[2],
                          'note2' => $importData[6],
                          'note3' => $importData[3],
                          'note4' => $importData[4],
                          'note5' => $importData[5],
                          'note6' => $importData[10].' Days',
                          'note7' => 'Please check'], 
                          function ($message) use ($show,$com)
                          {
                              $message->subject('Web Support IMI Notification');
                              $message->from($com->com_email); // Email Admin Fix
                              $message->to($show->xitem_day_email2);
                          });
                     
               }              
            }
           
            $data3 = DB::table('xitem_mstr')
                            ->where('xitem_mstr.xitem_part','=',$importData[2])
                            ->where('xitem_mstr.xitem_day3','=',$importData[10])
                            ->where('xitem_mstr.xitem_day_email3','!=', "")
                            ->get();
                            
        if(count($data3) != 0){
            foreach ($data3 as $show){                                                               
                          Mail::send('email', 
                          ['pesan' => 'There is an item that going to expired',
                          'note1' => $importData[2],
                          'note2' => $importData[6],
                          'note3' => $importData[3],
                          'note4' => $importData[4],
                          'note5' => $importData[5],
                          'note6' => $importData[10].' Days',
                          'note7' => 'Please check'], 
                          function ($message) use ($show,$com)
                          {
                              $message->subject('Web Support IMI Notification');
                              $message->from($com->com_email); // Email Admin Fix
                              $message->to($show->xitem_day_email3);
                          });
                     
               }              
            }
            
        }   
        
        /*============alert2============
        $data = DB::table('xalertitem_mstrs')
                            ->where('xalertitem_mstrs.xalertitem_day2','=',$importData[10])
                            ->get();
                            
        if(count($data) != 0){
            foreach ($data as $show){
               if ($show->xalertitem_supp == "") {
                  
                 Mail::send('email', 
                 ['pesan' => 'Expired in '.$importData[10]. ' days',
                 'note1' => 'Item Number : '.$importData[2],
                 'note2' => 'Please check'], 
                 function ($message) use ($show)
                 {
                     $message->subject('Notifikasi Web Support IMI');
                     $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                     $message->to($show->xalertitem_email2);
                 });
               
               }
               else{
               Mail::send('email', 
                 ['pesan' => 'Expired in '.$importData[10]. ' days',
                 'note1' => 'Item Number : '.$importData[2],
                 'note2' => 'Please check'], 
                 function ($message) use ($show)
                 {
                     $message->subject('Notifikasi Web Support IMI');
                     $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                     $message->to($show->xalertitem_email2);
                 });
               
               }
            
               
            }
        }   
        
         $data = DB::table('xalertitem_mstrs')
                            ->where('xalertitem_mstrs.xalertitem_day1','=',$importData[10])
                            ->get();
                            
        if(count($data) != 0){
            foreach ($data as $show){
               if ($show->xalertitem_supp == "") {
                  
                 Mail::send('email', 
                 ['pesan' => 'Expired in '.$importData[10]. ' days',
                 'note1' => 'Item Number : '.$importData[2],
                 'note2' => 'Please check'], 
                 function ($message) use ($show)
                 {
                     $message->subject('Notifikasi Web Support IMI');
                     $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                     $message->to($show->xalertitem_email1);
                 });
               
               }
               else{
               Mail::send('email', 
                 ['pesan' => 'Expired in '.$importData[10]. ' days',
                 'note1' => 'Item Number : '.$importData[2],
                 'note2' => 'Please check'], 
                 function ($message) use ($show)
                 {
                     $message->subject('Notifikasi Web Support IMI');
                     $message->from('andrew@ptimi.co.id'); // Email Admin Fix
                     $message->to($show->xalertitem_email1);
                 });
               
               }
            
               
            }
        }   
        ---------------------*/
        /*
         // Open CSV File n Read
        $file = fopen(public_path('trhist.csv'),"r");

        $importData_arr = array();
          $i = 0;
          while (($filedata = fgetcsv($file, 1000, ";")) !== FALSE) {
             $num = count($filedata );
             
             
             for ($c=0; $c < $num; $c++) {
                $importData_arr[$i][] = $filedata [$c];
             }
             $i++;
          }
          fclose($file);
          //dd($importData_arr);
          foreach($importData_arr as $importData){    
          $importData5 = str_replace(",", "." , $importData[5]);           
          $importData8 = str_replace(",", "." , $importData[8]);      
                  
            DB::table('xtrhist')->updateOrInsert(
                ['xtrhist_domain' => $importData[0], 'xtrhist_part' => $importData[1] ],
                ['xtrhist_desc' => $importData[2], 
                 'xtrhist_um' => $importData[3],
                 'xtrhist_pm' => $importData[4],
                 'xtrhist_qty_oh' => $importData5,
                 'xtrhist_last_date' =>$importData[6],
                 'xtrhist_days' =>$importData[7],
                 'xtrhist_amt' => $importData8,  
                 'xtrhist_ket1' => $importData[9],  
                 'xtrhist_ket2' => $importData[10],  
                 'xtrhist_ket3' => $importData[11],  
                 'xtrhist_ket4' => $importData[12],  
                 'xtrhist_type' => $importData[13],  
             ]);

        }  
                */
    }
}

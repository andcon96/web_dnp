<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class LoadItm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:item';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Item Mstr';

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
        //
        DB::table('xitem_mstr')->delete();
    
      $file = fopen(public_path('itm.csv'),"r");

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
          
         $itm = DB::table('xitm_ctrl')->first();          
         if(is_null($itm))
         {
             foreach($importData_arr as $importData){   
                        DB::table('xitem_mstr')->updateOrInsert(
                               ['xitem_domain' => $importData[0], 'xitem_part' => $importData[1] ],
                               ['xitem_desc' => $importData[2], 
                                'xitem_um' => $importData[3],
                                'xitem_prod_line' => $importData[4],
                                'xitem_type' => $importData[5],
                                'xitem_group' => $importData[6],
                                'xitem_pm' => $importData[7],
                                'xitem_sfty_stk' => $importData[8],     
                                'xitem_promo' => $importData[9], 
                                'xitem_dsgn' => $importData[10], 
                            ]);
             }
         }
         else
         {
            
                           // Insert to MySQL database
                 foreach($importData_arr as $importData){    
                     $checkitm = DB::table('xitm_ctrl')
                            ->where('xitm_ctrl.xitm_part','=',$importData[1])
                            ->first();
                     if ($checkitm) {
                         DB::table('xitem_mstr')->updateOrInsert(
                                     ['xitem_domain' => $importData[0], 'xitem_part' => $importData[1] ],
                                     ['xitem_desc' => $importData[2], 
                                      'xitem_um' => $importData[3],
                                      'xitem_prod_line' => $importData[4],
                                      'xitem_type' => $importData[5],
                                      'xitem_group' => $importData[6],
                                      'xitem_pm' => $importData[7],
                                      'xitem_sfty_stk' => $importData[8],     
                                      'xitem_promo' => $importData[9], 
                                      'xitem_dsgn' => $importData[10], 
                                  ]);                          
                     }
                     else
                     {   
                           $checkdb = DB::table('xitm_ctrl')
                                  ->where('xitm_ctrl.xitm_prod_line','=',$importData[4])
                                  ->where('xitm_ctrl.xitm_type','=',$importData[5])
                                  ->where('xitm_ctrl.xitm_group','=',$importData[6])
                                  ->where('xitm_ctrl.xitm_design','=',$importData[10])
                                  ->where('xitm_ctrl.xitm_promo','=',$importData[9])
                                  ->first();
                           if ($checkdb) {
                                 DB::table('xitem_mstr')->updateOrInsert(
                                     ['xitem_domain' => $importData[0], 'xitem_part' => $importData[1] ],
                                     ['xitem_desc' => $importData[2], 
                                      'xitem_um' => $importData[3],
                                      'xitem_prod_line' => $importData[4],
                                      'xitem_type' => $importData[5],
                                      'xitem_group' => $importData[6],
                                      'xitem_pm' => $importData[7],
                                      'xitem_sfty_stk' => $importData[8],     
                                      'xitem_promo' => $importData[9], 
                                      'xitem_dsgn' => $importData[10], 
                                  ]);
                           }
                     }
                  }
         } 
    }
}

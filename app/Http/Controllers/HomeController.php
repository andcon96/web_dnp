<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Session;
use Auth;

use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $id = Auth::id();

        $users = DB::table("users")
                    ->where("users.id",$id)
                    ->get();
        
        if (session::get('pusat_cabang')==1) {
            $so = DB::table("so_mstrs")
                        ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                        ->where('so_status', '!=', 10)
                        ->where('so_status',1)
                        ->count();

            $spb = DB::table("do_mstr")
                ->where("do_status",1)
                ->count();
        }

        if (session::get('pusat_cabang')==0) {
            $so = DB::table("so_mstrs")
                ->join('customers','so_mstrs.so_cust','=','customers.cust_code')
                ->where('so_status', '!=', 10)
                ->where('so_status',1)
                ->where('so_user', '=', Session::get('username'))
                ->where("so_site","=",session::get('site'))
                ->count();

            $spb = DB::table("do_mstr")
                ->where("do_site","=",session::get('site'))
                ->where("do_status",1)
                ->count();
        }

        $topsales = DB::table('transaksi_sum')
                    ->join('customers','transaksi_sum.cust_code','=','customers.cust_code')
                    ->join('items','items.itemcode','=','transaksi_sum.item_code')
                    ->selectRaw('customers.cust_code,customer_site as region,item_code,cust_desc, sum(total) as "g_total"')
                    ->groupBy('customers.cust_code')
                    ->orderBy('g_total','DESC')
                    ->take(10)
                    ->get();

        $topitem = DB::table('transaksi_sum')
                    ->join('customers','transaksi_sum.cust_code','=','customers.cust_code')
                    ->join('items','items.itemcode','=','transaksi_sum.item_code')
                    ->selectRaw('customers.cust_code,customer_site as region,item_code,itemdesc, sum(total) as "g_total"')
                    ->groupBy('item_code')
                    ->orderBy('g_total','DESC')
                    ->take(10)
                    ->get();

        $topregion = DB::table('transaksi_sum')
                    ->join('customers','transaksi_sum.cust_code','=','customers.cust_code')
                    ->join('items','items.itemcode','=','transaksi_sum.item_code')
                    ->join('site_mstrs','customers.customer_site','=','site_mstrs.site_code')
                    ->selectRaw('customers.cust_code,site_desc as region,item_code,itemdesc, sum(total) as "g_total"')
                    ->groupBy('customers.customer_site')
                    ->orderBy('g_total','DESC')
                    ->take(10)
                    ->get();

        $topyear = DB::table('months')
                    ->leftjoin('transaksi_sum', function ($join) {
                        $join->on(DB::raw('month(date_trans)'), '=', DB::raw('months.angkabulan'));
                    })
                    ->selectRaw('COALESCE(sum(total),0) as "g_total", year(date_trans) as year, angkabulan as month')
                    ->groupBy('year','month')
                    ->orderBy('month','asc')
                    ->orderBy('year','asc')
                    ->get();


        return view("home", ["users" => $users, "so" => $so, "spb" => $spb, "topsales" => $topsales, "topitem" => $topitem, 
            "topregion" => $topregion, "topyear" => $topyear]);
    }
}

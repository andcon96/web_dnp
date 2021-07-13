<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Default Auth Laravel
Route::get('/', function () {
	if(Auth::check()){return Redirect::to('home');}
    return view('auth.login');
});

Route::group(['middleware' => ['auth']], function() {
	Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

    // User Maint
	route::get('/usermt', 'SettingController@usermenu');
	route::post('/createuser', 'SettingController@createuser');
	route::get('/usermt/pagination', 'SettingController@userpaging');
	route::post('/edituser','SettingController@edituser'); /*21 Sept 2020*/
	route::post('/deleteuser','SettingController@deleteuser'); /*21 Sept 2020*/
	route::get('/menuuser','SettingController@getmenuuser'); /*23 Sept 2020*/
	route::get('/changepassword', 'SettingController@indchangepass');
	route::post('/userchange/changepass', 'SettingController@changepass');

	// Role Maint
	route::get('/rolemaster', 'SettingController@rolemenu');
	route::post('/createrole', 'SettingController@createrole');
	route::post('/editrole','SettingController@editrole');
	route::post('/deleterole','SettingController@deleterole');
	route::get('/rolesearch', 'SettingController@rolesearch');
	route::get('/menugetrole','SettingController@menugetrole');
	route::get('/rolemaster/pagination', 'SettingController@rolepaging');

	// Site Maint
	route::get('/sitemaster', 'SettingController@sitemenu');
	route::post('/editsite', 'SettingController@editsite');
	route::get('/sitesearch', 'SettingController@sitesearch');
	route::get('/menugetsite','SettingController@menugetsite');
	route::get('/sitemaster/pagination', 'SettingController@sitepaging');
	route::post('/reloadtabelsite','SettingController@insertqadtotablesite');

	// Item Maint
	route::get('/itemmt', 'SettingController@itemmenu');
	route::get('/itemmt/pagination', 'SettingController@itempaging');
	route::post('/createitem','SettingController@itemcreate'); /*21 Sept 2020*/
	route::post('/edititem','SettingController@itemedit'); /*21 Sept 2020*/
	route::post('/deleteitem','SettingController@itemdelete'); /*21 Sept 2020*/
	route::get('/searchsubgroup','SettingController@searchsubgroup'); /*21 Sept 2020*/
	route::post('/reloadtabelitem','SettingController@insertqadtotableitem');

	// Cust Maint
	route::get('/custmt', 'SettingController@custmenu');
	route::get('/validasicust', 'SettingController@validasicust');
	route::post('/createcust', 'SettingController@createcust');
	route::post('/editcust', 'SettingController@editcust');
	route::get('/custmt/pagination', 'SettingController@customerpaging');
	route::post('/deletecust', 'SettingController@deletecust');  /*21 Sept 2020*/
	route::post('/reloadtabelcustomer','SettingController@insertqadtotablecust');

	// supplier maint
	route::get('/suppmaint', 'SettingController@suppmenu');
	route::post('/createtype', 'SettingController@createtype');
	route::post('/updatetype', 'SettingController@updatetype');
	route::post('/delete', 'SettingController@deletetype');
	route::get('/suppmaint/pagination', 'SettingController@supppaging');
	route::post('/reloadtabelsupplier','SettingController@insertqadtotablesupp');

	// Customer ST maint
	route::get('/custshipto', 'SettingController@custstmain');
	route::get('/custshipto/pagination', 'SettingController@custstpaginate');
	route::post('/custshiptoload', 'SettingController@custshiptoload'); //02122020 
	
	// Item Konversi maint
	route::get('/itemkonversi', 'SettingController@itemkonvmenu');
	route::get('/itemkonversi/pagination', 'SettingController@itemkonvpaging');
	route::post('/loaditemkonv', 'SettingController@loaditemkonv'); //02122020

	// SO Sales
	route::post('/createsosaless','SalesOrderSADController@createsosales');

	//cust relation Master
	route::get('/custrelation', 'SettingController@menucustrelation');
	route::post('/custrelation/create', 'SettingController@createrelation');
	route::post('/custrelation/edit', 'SettingController@editrelation');
	route::post('/custrelation/delete', 'SettingController@deleterelation');
	route::get('/custrelation/pagination', 'SettingController@paginatecustrelation');

	
	//activity Master
	route::get('/activitymt', 'SettingController@menuactivity');
	route::post('/activitymt/create', 'SettingController@createactivity');
	route::post('/activitymt/edit', 'SettingController@editactivity');
	route::post('/activitymt/delete', 'SettingController@deleteactivity');
	route::get('/activitymt/pagination', 'SettingController@paginateactivity');

	
	// Approval Maint
	route::get('/approvalmt', 'SettingController@approvalmenu');
	route::post('/createapproval', 'SettingController@createapproval');
	route::get('/approvalsearch', 'SettingController@approvalsearch');
	route::get('/approval/searching', 'SettingController@searchingapp');

	//salesactivity
	route::get('/checkincheckoutbrowse', 'SettingController@checkincheckoutbrowse');
	route::get('/salesactivity', 'SettingController@salesactivitymenu')->name("slsactivity");
	route::get('/salesactivity/logstatus', 'SettingController@sagetlogstatus');
	route::post('btnsales', 'SettingController@sabutton');
	route::get('/salesactivity/searching', 'SettingController@salesactivitysearch');

	//soshipmentconfirm
	route::get('/soshipmentconfirm', 'SettingController@sosconfirmmenu');
	route::post('soconfirm', 'SettingController@sosconfirm');
	route::get('/soshipmentconfirm/pagination', 'SettingController@sopaging');
	route::get('/soshipmentgetinfo','SettingController@soshipmentgetinfo');
		

	// ---------------------------------- Andrew 

	// SO Sales
	route::get('/sosales','SalesOrderSalesController@index');
	route::get('/sosales/pagination','SalesOrderSalesController@index');
	route::get('/alamatsearch','SalesOrderSalesController@alamatsearch');
	route::get('/brelnamesearch','SalesOrderSADController@brelnamesearch');
	route::get('/detailsales','SalesOrderSalesController@detailsales');
	route::get('/shiptosearch','SalesOrderSalesController@shiptosearch');
	// --------------------------------- Tommy
	route::get('/searchum', 'SalesOrderSalesController@searchum');


	// SO SAD
	route::get('/sosad','SalesOrderSADController@index');
	route::post('/createsosales','SalesOrderSADController@createsosales');
	route::get('/createsosales','SalesOrderSADController@index');
	route::get('/shiptoedit','SalesOrderSADController@shiptoedit');
	route::get('/editdetail','SalesOrderSADController@editdetail');
	route::post('/editsalesorder','SalesOrderSADController@editsalesorder');
	route::post('/deletesalesorder', 'SalesOrderSADController@deletesalesorder');
	route::get('/editsalesorder','SalesOrderSADController@index');
	route::get('/deletesalesorder', 'SalesOrderSADController@index');
	route::get('/getumitem','SalesOrderSADController@getumitem');
	route::get('/sosalessad/pagination','SalesOrderSADController@sadpagination');
	route::get('/detailsalessad','SalesOrderSADController@detailsales');
	route::post('/approvehold','SalesOrderSADController@approvehold');
	route::post('/confirmso','SalesOrderSADController@confirmso');
	route::get('/approvehold','SalesOrderSADController@index');
	route::get('/confirmso','SalesOrderSADController@index');
	route::get('/checkspb','SalesOrderSADController@checkspb');
	route::get('/alamatcust','SalesOrderSADController@alamatcust');
	route::get('/checkallspb','SalesOrderSADController@checkallspb');

	
	// --------------------------------- Tommy
	route::get('/searchum2', 'SalesOrderSADController@searchumsad');


	// SO retur
	route::get('/soretur','SalesOrderSADController@retur');
	route::get('/detailretur','SalesOrderSADController@detailretur');
	route::get('/alamatretur','SalesOrderSADController@alamatretur');
	route::post('/returqad','SalesOrderSADController@returqad');
	route::get('/returqad','SalesOrderSADController@index');
	route::get('/soreturbrowse','SalesOrderSADController@returbrowse');
	route::get('/soretur/pagination','SalesOrderSADController@retursearching');
	route::get('/detailreturbrowse','SalesOrderSADController@detailreturbrowse');
	route::get('/umretur','SalesOrderSADController@getumitem');
	route::post('/createsoretur','SalesOrderSADController@createsoretur');
	route::post('/createsoreturweb','SalesOrderSADController@createsoreturweb');
	route::get('/createsoretur','SalesOrderSADController@index');
	route::get('/createsoreturweb','SalesOrderSADController@index');
	route::get('/editdetailretur','SalesOrderSADController@editdetailretur');
	route::post('/deleteretur','SalesOrderSADController@deleteretur');
	route::post('/editsoreturweb','SalesOrderSADController@editsoreturweb');
	route::get('/deleteretur','SalesOrderSADController@index');
	route::get('/editsoreturweb','SalesOrderSADController@index');
	route::get('/editsoreturwebdetail','SalesOrderSADController@editsoreturwebdetail');
	route::get('/getlocretur','SalesOrderSADController@getlocretur');
	route::get('/returpdf','SalesOrderSADController@returpdf');
	route::get('/returpdftest','SalesOrderSADController@returpdftest');

	// SO OH
	route::get('/sosalesoh','SalesOrderSADController@sosalesoh');
	route::get('/sooh/pagination','SalesOrderSADController@sosalesoh');

	// SO Consignment
	route::get('/socons','SalesOrderSADController@socons');
	route::get('/getlistum','SalesOrderSADController@getlistum');
	route::post('/createsocons','SalesOrderSADController@createsocons');
	route::get('/createsocons','SalesOrderSADController@get');
	route::get('/socons/pagination','SalesOrderSADController@soconssearch');
	route::post('/createsoconsweb','SalesOrderSADController@createsoconsweb');
	route::get('/createsoconsweb','SalesOrderSADController@index');
	route::get('/detaileditcons','SalesOrderSADController@detaileditcons');
	route::post('/editsoconsweb','SalesOrderSADController@editsoconsweb');
	route::post('/confirmsocons','SalesOrderSADController@confirmsocons');
	route::get('/editsoconsweb','SalesOrderSADController@index');
	route::get('/confirmsocons','SalesOrderSADController@index');
	route::get('/detailsalescons','SalesOrderSADController@detailsalescons');
	route::post('/deletesocons','SalesOrderSADController@deletesocons');
	route::get('/deletesocons','SalesOrderSADController@index');
	
	// End Of Day Process
	route::get('/menueof','EOFController@index');
	route::post('/eofsubmit','EOFController@submiteof');
	route::post('/loadloc','EOFController@loadloc');
	route::get('/locmenu','EOFController@locmenu');
	route::get('/locmaster/pagination','EOFController@locmenu');
	route::post('/loadParentC','EOFController@loadParentC');
	route::get('/eofsubmit','EOFController@index');
	route::get('/loadloc','EOFController@index');
	route::get('/loadParentC','EOFController@index');

	route::get('/testing','SalesOrderSADController@testxml');


	// Surat Jalan
	route::get('/do','DeliveryOrderController@index')->name('ddodelete');
	route::get('/detaildo','DeliveryOrderController@detaildo');
	route::get('/lastdo','DeliveryOrderController@lastdo');
	route::get('/noso','DeliveryOrderController@noso');
	route::get('/searchitem','DeliveryOrderController@searchitem');
	route::get('/searchqty','DeliveryOrderController@searchqty');
	route::get('/createdo','DeliveryOrderController@createdo')->name('ddosave');
	route::post('/createdoTemp','DeliveryOrderController@createdoTemp');
	route::get('dotemp','DeliveryOrderController@dotemp')->name('dotemp');
	route::post('/dosave','DeliveryOrderController@dosave');
	route::get('/dosearch','DeliveryOrderController@testdo');
	route::post('deletetemp','DeliveryOrderController@deletetemp');
	route::get('doprint','DeliveryOrderController@doprint');
	route::get('/dobrowse/pagination','DeliveryOrderController@dopage');
	route::get('/docreate/pagination','DeliveryOrderController@docreatepage');
	route::post('dodelete','DeliveryOrderController@dodelete');
	route::get('docetak','DeliveryOrderController@doprint');
	route::get('inv','DeliveryOrderController@inv');
	route::post('inv','DeliveryOrderController@inv');
	route::get('/createdoTemp','DeliveryOrderController@index');
	route::get('/dosave','DeliveryOrderController@index');
	route::get('deletetemp','DeliveryOrderController@index');
	route::get('dodelete','DeliveryOrderController@index');
	route::post('/donlod','DeliveryOrderController@donlod');
	

        /*dr*/
        route::get('/porcp1','porcpcontroller@porcp1');
        route::post('/porcp1','porcpcontroller@porcp1');
        route::post('/porcpupd','porcpcontroller@update');
        Route::post('/porcpok', 'porcpcontroller@porcpok');  
        route::get('/porcp1','porcpcontroller@porcp1');
        route::get('/porcpupd','porcpcontroller@porcp1');
        Route::get('/porcpok', 'porcpcontroller@porcp1');  

    route::get('/testingnew','SOAPController@testing');      
    route::get('/test123','SOAPController@test');
    route::post('/loadoldso','EOFController@loadexcel');      

    route::get('menurnbr','EOFController@runnbrmenu');
	route::get('/menurnbr/pagination', 'EOFController@runnbrmenu');
    route::post('editrnbr','EOFController@updatemenurnbr');
    route::get('loadsodkh','EOFController@loadsodkh');

    route::get('checksoqad','EOFController@menuchecksoqad');
    route::post('checksoqadweb','EOFController@checksoqad');

    // MRP EOD
    route::get('mrpeod','EOFController@mrpeod')->name('mrpeod');
    route::post('createeod','EOFController@createeod');
    route::get('mrppo','EOFController@mrppo')->name('mrppo');
    route::post('createmrppo','EOFController@createmrppo');
    route::get('/mrppo/search','EOFController@mrppo');
    route::get('/exportMRPPO','EOFController@exportmrppo');

    // SPB Check
    route::get('menuspbcheck','EOFController@menuspbcheck');
    route::get('/spbcheck','EOFController@spbcheck');
	route::get('/menuspbcheck/pagination', 'EOFController@menuspbcheck');


	// item child kons
	route::get('itemchildmenu','SettingController@itemchildmenu');
	route::get('itemchild/pagination','SettingController@itemchildpaging');
	route::post('loaditemchild','SettingController@loaditemchild');

	// test dashboard actavis
	route::get('homealt',function(){
		$id = Auth::id();

        $users = DB::table("users")
                    ->where("users.id",$id)
                    ->get();
        
		$so = DB::table("so_mstrs")
					->join('customers','so_mstrs.so_cust','=','customers.cust_code')
					->where('so_status', '!=', 10)
					->where('so_status',1)
					->count();

		$spb = DB::table("do_mstr")
			->where("do_status",1)
			->count();

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

		// dd($topyear);
        return view("homealt", ["users" => $users, "so" => $so, "spb" => $spb, "topsales" => $topsales, "topitem" => $topitem, 
            "topregion" => $topregion, "topyear" => $topyear]);
	});

});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

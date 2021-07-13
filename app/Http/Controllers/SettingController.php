<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

use Auth;
use App\User;
use Carbon\Carbon;
use Svg\Tag\Rect;



class SettingController extends Controller
{

    private function httpHeader($req) {
        return array('Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'SOAPAction: ""',        // jika tidak pakai SOAPAction, isinya harus ada tanda petik 2 --> ""
            'Content-length: ' . strlen(preg_replace("/\s+/", " ", $req)));
    }
    // User Maint
    public function usermenu()
    {
        if (strpos(Session::get('menu_access'), 'MT01') !== false) {
            $data = DB::table('users')
                ->leftjoin('roles', 'users.role_user', 'roles.role_code')
                ->leftjoin('site_mstrs', 'users.site', 'site_mstrs.site_code')
                ->paginate(10);
            //dd($data);
            $datarole = DB::table('roles')
                ->get();
            $datasite = DB::table('site_mstrs')
                ->get();
            return view('setting.usermaint', ['data' => $data, 'datarole' => $datarole, 'datasite' => $datasite]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function createuser(Request $req)
    {
        //dd($req->all());      
        //dd($flag);

        $this->validate($req, [
            'username' => 'unique:users|max:6',
            'name' => 'max:24',
            'password' => 'max:15|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'max:15'
        ], [
            'username.unique' => 'Username sudah terdaftar',
            'password.same' => 'Password & Confirm Password Harus sama'
        ]);

        $dataarray = array(
            'username' => $req->username,
            'name' => $req->name,
            'role_user' => $req->Role,
            'site' => $req->Site,
            'password' => Hash::make($req->password),
            'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
            'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
        );
        DB::table('users')->insert($dataarray);

        session()->flash("updated", "User Berhasil Dibuat");
        return back();
    }

    public function edituser(Request $req)
    {
        $flag = $req->e_cbUserMT . $req->e_cbAppMT . $req->e_cbItemMT . $req->e_cbCustMT . $req->e_cbCustTypeMT . $req->e_cbRewardMT . $req->e_cbGroupMT . $req->e_cbSGroupMT . $req->e_cbContractTR . $req->e_cbSContractTR . $req->e_cbContractApp . $req->e_cbRewardTR . $req->e_cbPrintTR . $req->e_cbContractHist . $req->e_cbSContractHist . $req->e_cbLocation;

       if($req->get('e_newpass') == ''){
	
	  DB::table("users")
            ->where('username', '=', $req->e_username)
            ->update([
                'name' => $req->e_name,
                'role_user' => $req->e_role,
                'site' => $req->e_site,
		'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
            ]);

	}
	else{
	    
	    DB::table("users")
            ->where('username', '=', $req->e_username)
            ->update([
                'name' => $req->e_name,
                'role_user' => $req->e_role,
                'site' => $req->e_site,
		'password' => Hash::make($req->e_newpass),
		'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),

            ]);

	}


        session()->flash('updated', 'User berhasil diupdate');
        return back();
    }

    public function deleteuser(Request $req)
    {
        //dd($req->all());

        DB::table("users")
            ->where('username', '=', $req->tmp_username)
            ->delete();

        session()->flash('updated', 'User berhasil dihapus');
        return back();
    }

    public function getmenuuser(Request $req)
    {
        if ($req->ajax()) {
            $data = DB::table('users')
                ->where('username', '=', $req->search)
                ->first();

            return response($data->flag);
        }
    }


    public function userpaging(Request $req)
    {
        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $username = $req->get('username');
            $name = $req->get('name');
            $cabang = $req->get('cabang');


            if ($username == '' and $name == '' and $cabang == '') {
                // dd('aaaa');
                $data = DB::table('users')
                    ->leftjoin('roles', 'users.role_user', 'roles.role_code')
                    ->leftjoin('site_mstrs', 'users.site', 'site_mstrs.site_code')
                    ->selectRaw('username,name,role_code,role_desc,site_code,site_desc')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-usermaint', ['data' => $data]);
            } else {
                $kondisi = "id > 0";

                if ($username != '') {
                    $kondisi .= ' and username = "' . $username . '"';
                }
                if ($name != '') {
                    $kondisi .= ' and name = "' . $name . '"';
                }
                if ($cabang != '') {
                    $kondisi .= ' and site_desc = "' . $cabang . '"';
                }

                $data = DB::table('users')
                    ->join('roles', 'users.role_user', 'roles.role_code')
                    ->join('site_mstrs', 'users.site', 'site_mstrs.site_code')
                    ->selectRaw('*')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-usermaint', compact('data'));
            }
        }
    }

    // Role Menu
    public function rolemenu()
    {
        if (strpos(Session::get('menu_access'), 'MT02') !== false) {
            $data = DB::table('roles')
                ->orderBy('role_code', 'ASC')
                ->paginate(10);

            // $user = DB::table('users')
            //     ->orderBy('name', 'ASC')
            //     ->get();

            // dd($data);

            return view('setting.rolemaster', ['data' => $data]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function createrole(Request $req)
    {
        // dd($req->all());

        $access = $req->cbUser . $req->cbRole . $req->cbSite . $req->cbItem . $req->cbSupplier . $req->cbCustomer . $req->cbCustomerRelation . $req->cbActivity . $req->cbCustomerST . $req->cbItemKonv . $req->cbapprovallmt . $req->cbeodp . $req->cbpor . $req->cbsosc . $req->cbsoc . $req->cbsj . $req->cbsosad . $req->cbsosales . $req->cbsor .$req->cbsorbrow . $req->cbsa . $req->cbsooa . $req->cbsab . $req->cblocationmt. $req->cbinventory. $req->cbviewso . $req->cbspbview . $req->cbrunningnumber. $req->cbcheckso. $req->cbpoeod. $req->cbdashboard. $req->cbstockspb. $req->cbpchildmt;
	$sales = $req->slsmn;
	if($sales == ""){$sales = "N";}
        //dd($access);

        $this->validate($req, [
            'role_code' => 'unique:roles|max:24',
            'role_desc' => 'unique:roles|max:50',
        ], [
            'role_code.unique' => 'Role code sudah dipakai',
            'role_desc.required' => 'Deskripsi role harus diisi',
            'role_desc.unique' => 'Deskripsi role sudah dipakai'
        ]);
        $dataarray = array(
            'role_code' => $req->role_code,
            'role_desc' => $req->role_desc,
	    'salesman' => $sales,
            'menu_access' => $access,
            'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
            'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
        );
        DB::table('roles')->insert($dataarray);


        session()->flash("updated", "Role Berhasil Dibuat");
        return back();
    }

    public function editrole(Request $req)
    {
        
        $access = $req->e_cbUser . $req->e_cbRole . $req->e_cbSite . $req->e_cbItem . $req->e_cbSupplier . $req->e_cbCustomer . $req->e_cbCustomerRelation . $req->e_cbActivity . $req->e_cbCustomerST . $req->e_cbItemKonv . $req->e_cbapprovallmt . $req->e_cbeodp . $req->e_cbpor . $req->e_cbsosc . $req->e_cbsoc . $req->e_cbsj . $req->e_cbsosad . $req->e_cbsosales . $req->e_cbsor .$req->e_cbsorbrow . $req->e_cbsa . $req->e_cbsooa . $req->e_cbsab . $req->e_cbLocation. $req->e_cbinventory. $req->e_cbviewso . $req->e_cbspbview . $req->e_cbrunningnumber. $req->e_cbcheckso. $req->e_cbpoeod. $req->e_cbdashboard. $req->e_cbstockspb. $req->e_cbpchildmt;
	$sales = $req->e_slsmn;
	if($sales == ""){$sales = "N";}
        
        DB::table("roles")
            ->where('role_code', '=', $req->e_rolecode)
            ->update([
                'role_desc' => $req->role_desc,
		'salesman' => $sales,
                'menu_access' => $access,
                'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString()
            ]);

        session()->flash('updated', 'Role berhasil diupdate');
        return back();
    }

    public function deleterole(Request $req)
    {
        // dd($req->all());

        DB::table("roles")
            ->where('role_code', '=', $req->tmp_rolecode)
            ->delete();

        session()->flash('updated', 'role berhasil dihapus');
        return back();
    }

    public function menugetrole(Request $req)
    {
        if ($req->ajax()) {
            $data = DB::table('roles')
                ->where('role_code', '=', $req->search)
                ->first();

            return response($data->menu_access.$data->salesman);
        }
    }

    public function rolepaging(Request $req)
    {

        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $rolecode = $req->get('rolecode');
            $roledesc = $req->get('roledesc');


            if ($rolecode == '' and $roledesc == '') {
                $data = DB::table('roles')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-rolemaster', ['data' => $data]);
            } else {
                $kondisi = "role_code != ''";

                if ($rolecode != '') {
                    $kondisi .= ' and role_code = "' . $rolecode . '"';
                    // dd($kondisi);
                }
                if ($roledesc != '') {
                    $kondisi .= ' and role_desc = "' . $roledesc . '"';
                }


                $data = DB::table('roles')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                //dd($data);
                return view('setting.table-rolemaster', ['data' => $data]);
            }
        }
    }

    // Site Master

    public function sitemenu()
    {
        
        if (strpos(Session::get('menu_access'), 'MT03') !== false) {
            $data = DB::table('site_mstrs')
                ->orderBy('site_code', 'ASC')
                ->paginate(10);

            $data2 = DB::table('site_mstrs')
                ->select('site_code')
                ->where('pusat_cabang','=',1)
                ->orderBy('site_code', 'ASC')
                ->first();

            

            // $user = DB::table('users')
            //     ->orderBy('name', 'ASC')
            //     ->get();

            // dd($data);

            return view('setting.sitemaster', ['data' => $data,'data2'=>$data2]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function editsite(Request $req)
    {   
        if($req->e_pusatcabang == null){
            DB::table("site_mstrs")
            ->where('site_code', '=', $req->e_sitecode)
            ->update([
                'site_flag' => $req->e_siteflag,
                'pusat_cabang' =>0,
                'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString()
            ]);
        }
        else{
            DB::table("site_mstrs")
            ->where('site_code', '=', $req->e_sitecode)
            ->update([
                'site_flag' => $req->e_siteflag,
                'pusat_cabang' =>$req->e_pusatcabang,
                'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString()
            ]);
        }
        

        session()->flash('updated', 'site '.$req->e_sitecode. ' berhasil diupdate');
        return back();
    }

    public function menugetsite(Request $req)
    {
        
        //dd($req->all());
        if ($req->ajax()) {
            $data = DB::table('site_mstrs')
                ->where('site_code', '=', $req->search)
                ->first();

            return response($data->ssite_flag);
        }
    }

    public function sitepaging(Request $req)
    {

        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $sitecode = $req->get('sitecode');
            $sitedesc = $req->get('sitedesc');
            $warehouse = $req->get('warehouse');


            if ($sitecode == '' and $sitedesc == '' and $warehouse == '') {
                // dd('aaaa');
                $data = DB::table('site_mstrs')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                $data2 = DB::table('site_mstrs')
                    ->select('site_code')
                    ->where('pusat_cabang','=',1)
                    ->orderBy('site_code', 'ASC')
                    ->first();

                return view('setting.table-sitemaster', ['data' => $data,'data2'=>$data2]);
            } else {
                $kondisi = "site_code != ''";

                if ($sitecode != '') {
                    $kondisi .= ' and site_code = "' . $sitecode . '"';
                    // dd($kondisi);
                }
                if ($sitedesc != '') {
                    $kondisi .= ' and site_desc = "' . $sitedesc . '"';
                }
                if ($warehouse != '') {
                    $kondisi .= ' and site_flag = "' . $warehouse . '"';
                }

                $data = DB::table('site_mstrs')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);
                $data2 = DB::table('site_mstrs')
                    ->select('site_code')
                    ->where('pusat_cabang','=',1)
                    ->orderBy('site_code', 'ASC')
                    ->first();
                //dd($data);
                return view('setting.table-sitemaster', ['data' => $data,'data2'=>$data2]);
            }
        }
    }

    public function insertqadtotablesite(Request $req)
    {
            // Validasi WSA --> item code

		// Validasi WSA
		$qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
		$qxReceiver     = '';
		$qxSuppRes      = 'false';
		$qxScopeTrx     = '';
		$qdocName       = '';
		$qdocVersion    = '';
		$dsName         = '';
        
		$timeout        = 0;

		// ** Edit here
		$qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                                '<site_master xmlns="urn:iris.co.id:wsatrain">'.
                                    '<inpdomain>DKH</inpdomain>'.
                                '</site_master>'.
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
		
		$flag = 0;
		
		$item = '';
		
		foreach($xmlResp->xpath('//ns1:t_site') as $data) { 
		
			$item = (string) $xmlResp->xpath('//ns1:t_site')[$flag]."||".(string) $xmlResp->xpath('//ns1:t_desc')[$flag];
            DB::table('site_mstrs')
            ->updateOrInsert([
            'site_code' => (string) $xmlResp->xpath('//ns1:t_site')[$flag]],
            [
            'site_code' => (string) $xmlResp->xpath('//ns1:t_site')[$flag],
            'site_desc' => (string) $xmlResp->xpath('//ns1:t_desc')[$flag],
            'updated_at' => Carbon::now()->toDateTimeString()
            ]);
          
            $flag += 1;
        }
        $data = DB::table('site_mstrs')
        ->orderBy('site_code', 'ASC')
        ->get();

        foreach($data as $data){
            if($data->pusat_cabang == null){
                DB::table("site_mstrs")
                ->where('site_code', '=', $data->site_code)
                ->update([
                    'pusat_cabang' =>0,
                    'created_at' => Carbon::now()->toDateTimeString(),
                ]);
            }
        }
        
        
        Session()->flash('updated', 'Table data value has been updated');
        return back();
	
	}
    


    // Item Maint
    public function itemmenu()
    {
        
        if (strpos(Session::get('menu_access'), 'MT04') !== false) {
           
            $data = DB::table('items')
                ->paginate(10);

            $itemtype = DB::table('items')
                ->groupBy('item_um')
                ->get();



            return view('setting.itemmaint', ['data' => $data, 'itemtype' => $itemtype]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function insertqadtotableitem(Request $req)
    {
		 // Validasi WSA --> item code

		// Validasi WSA
		$qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
		$qxReceiver     = '';
		$qxSuppRes      = 'false';
		$qxScopeTrx     = '';
		$qdocName       = '';
		$qdocVersion    = '';
		$dsName         = '';
        
		$timeout        = 0;

		// ** Edit here
		$qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                                '<itemmaster xmlns="urn:iris.co.id:wsatrain">'.
                                    '<inpdomain>'.'DKH'.'</inpdomain>'.
                                '</itemmaster>'.
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

		$flag = 0;
		
		$item = '';
		$itemdesc = '';

        DB::table('items')
                ->delete();

		foreach($xmlResp->xpath('//ns1:tempRow') as $data5){
			$items = DB::table('items')
				->where('itemcode','=',$data5->t_part)
				->first();
		
			if($items){
				DB::table('items')
				->where('itemcode','=',$data5->t_part)
				->update([
            				'itemcode' => $data5->t_part,
            				'itemdesc' => $data5->t_desc1." ".$data5->t_desc2,
            				'item_um'  => $data5->t_um,
                            'item_site'=> $data5->t_site,
            				'safety_stock' => $data5->t_safetystock,
            				'item_location' => $data5->t_loc,
                            'item_type' => $data5->t_ptype,
            				'updated_at' => Carbon::now()->toDateTimeString()]);
					
			      }
			else{
				DB::table('items')
				->insert([
            				'itemcode' => $data5->t_part,
            				'itemdesc' => $data5->t_desc1." ".$data5->t_desc2,
            				'item_um'  => $data5->t_um,
                            'item_site'=> $data5->t_site,
            				'safety_stock' => $data5->t_safetystock,
            				'item_location' => $data5->t_loc,
                            'item_type' => $data5->t_ptype,
            				'created_at' => Carbon::now()->toDateTimeString()]);
					
				}	
		}

	        Session()->flash('updated', 'Table data has been updated');
        return back();
	
	}




    public function itempaging(Request $req)
    {
        if ($req->ajax()) {
            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $itemcode = $req->get('itemcode');
            $itemdesc = $req->get('itemdesc');
            $itemtype = $req->get('itemtype');


            if ($itemcode == '' and  $itemdesc == '' and $itemtype == '') {
                $data = DB::table('items')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-itemmaint', ['data' => $data]);
            } else {

                $kondisi = 'itemcode != ""';

                if ($itemcode != '') {
                    $kondisi .= ' and itemcode = "' . $itemcode . '"';
                }
                if ($itemdesc != '') {
                    $kondisi .= ' and itemdesc = "' . $itemdesc . '"';
                }
                if ($itemtype != '') {
                    $kondisi .= ' and item_um = "' . $itemtype . '"';
                }


                $data = DB::table('items')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-itemmaint', compact('data'));
            }
        }
    }


    // Customer Maint
    public function custmenu()
    {
        if (strpos(Session::get('menu_access'), 'MT05') !== false) {
            $data = DB::table('customers')
                ->leftJoin('site_mstrs', 'customers.customer_site', 'site_mstrs.site_code')
                ->paginate(10);

            return view('setting.custmaint', ['datas' => $data]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }



    public function customerpaging(Request $req)
    {
        if ($req->ajax()) {
            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $custcode = $req->get('custcode');
            $custdesc = $req->get('custdesc');
            $region = $req->get('region');
            $custsite = $req->get('custsite');
            if ($custcode == '' and $custdesc == '' and $region == '' and $custsite == '') {
                $data = DB::table('customers')
                    ->leftJoin('site_mstrs', 'customers.customer_site', 'site_mstrs.site_code')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-customer', ['datas' => $data]);
            } else {


                $kondisi = 'cust_code != ""';

                if ($custcode != '') {
                    $kondisi .= ' and cust_code  = "' . $custcode . '"';
                }
                if ($custdesc != '') {
                    $kondisi .= ' and cust_desc = "' . $custdesc . '"';
                }
                if ($region != '') {
                    $kondisi .= ' and customer_region = "' . $region . '"';
                }
                if ($custsite != '') {
                    $kondisi .= ' and customer_site = "' . $custsite . '"';
                }

                $datas = DB::table('customers')
                    ->selectRaw('*')
                    ->leftJoin('site_mstrs', 'customers.customer_site', 'site_mstrs.site_code')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);


                return view('setting.table-customer', compact('datas'));
            }
        }
    }

    public function insertqadtotablecust(Request $req)
    {
		DB::disableQueryLog();
		$testarray = array(); 
		$testarray2 = array();
		set_time_limit(360);
		  // Validasi WSA --> item code

		// Validasi WSA
		$qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
		$qxReceiver     = '';
		$qxSuppRes      = 'false';
		$qxScopeTrx     = '';
		$qdocName       = '';
		$qdocVersion    = '';
		$dsName         = '';
        
		$timeout        = 0;

		// ** Edit here
		$qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                                '<cust_master xmlns="urn:iris.co.id:wsatrain">'.
                                    '<inpdomain>DKH</inpdomain>'.
                                '</cust_master>'.
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
		
		

		$flag = 0;
		$testcount = 0;
		$item = '';
		$custaddr = '';
		        DB::table('customers')
            		->truncate();

		foreach($xmlResp->xpath('//ns1:tempRow') as $data2){
			
			DB::table('customers')
				->insert([
					'cust_code' => $data2->t_cmaddr,
                            'cust_desc' => $data2->t_sortname,
            				'cust_alt_name' => $data2->t_cmname,
            				'cust_top'  => $data2->t_cmtop,
            				'cust_alamat' => $data2->t_addr1. $data2->t_addr2. $data2->t_addr3,
            				'customer_site' => $data2->t_cmsite,
            				'customer_region'  => $data2->t_cmregion,
            				'custcredit_limit'  => $data2->t_cmcrlimit,
            				'created_at' => Carbon::now()->toDateTimeString()
					]);

					
		}
		
	
	

        Session()->flash('updated', 'Table data has been updated');
        return back();
	
	}


    //supplier maint
    public function suppmenu()
    {
        if (strpos(Session::get('menu_access'), 'MT06') !== false) {
            
            $data = DB::table('supp_mstrs')
                ->paginate(10);

            return view('setting.suppmaint', ['datas' => $data]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function supppaging(Request $req)
    {

        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $suppcode = $req->get('suppcode');
            $suppdesc = $req->get('suppdesc');



            if ($suppcode == '' and $suppdesc == '') {
                // dd('aaaa');
                $data = DB::table('supp_mstrs')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-suppmaint', ['datas' => $data]);
            } else {
                $kondisi = "supp_code != ''";

                if ($suppcode != '') {
                    $kondisi .= ' and supp_code = "' . $suppcode . '"';
                    // dd($kondisi);
                }
                if ($suppdesc != '') {
                    $kondisi .= ' and supp_desc = "' . $suppdesc . '"';
                }

                $data = DB::table('supp_mstrs')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                //dd($data);
                return view('setting.table-suppmaint', ['datas' => $data]);
            }
        }
    }

    public function insertqadtotablesupp(Request $req){
        // Validasi WSA
		$qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
		$qxReceiver     = '';
		$qxSuppRes      = 'false';
		$qxScopeTrx     = '';
		$qdocName       = '';
		$qdocVersion    = '';
		$dsName         = '';
        
		$timeout        = 0;

		// ** Edit here
		$qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                                '<supp_master xmlns="urn:iris.co.id:wsatrain">'.
                                   		 '<inpdomain>DKH</inpdomain>'.
                                '</supp_master>'.
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
		
		$flag = 0;
		
		$item = '';
		
		foreach($xmlResp->xpath('//ns1:t_suppcode') as $data) { 
		
			$item = (string) $xmlResp->xpath('//ns1:t_suppcode')[$flag]."||".(string) $xmlResp->xpath('//ns1:t_suppname')[$flag]."||".(string) $xmlResp->xpath('//ns1:t_supptelephone')[$flag];
            DB::table('supp_mstrs')
            ->updateOrInsert([
            'supp_code' => (string) $xmlResp->xpath('//ns1:t_suppcode')[$flag]],
            [
            'supp_code' => (string) $xmlResp->xpath('//ns1:t_suppcode')[$flag],
            'supp_desc' => (string) $xmlResp->xpath('//ns1:t_suppname')[$flag],
            'supp_telepon'=>(string) $xmlResp->xpath('//ns1:t_supptelephone')[$flag],
'supp_site'=>(string) $xmlResp->xpath('//ns1:t_site')[$flag],
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString()]);
          
            $flag += 1;
        }
        Session()->flash('updated', 'Table data has been updated');
        return back();
    }
    
    //menampilkan menu cust. relation
    public function menucustrelation(Request $req)
    {  

        if (strpos(Session::get('menu_access'), 'MT07') !== false) {
    

            // $custrel = db::select('select approver1.id, approver1.cust_code_parent, approver1.cust_desc, approver1.cust_code_child, customers.cust_desc as "nama"
            // from
            // (select cust_relation.id, customers.cust_code, cust_relation.cust_code_parent, customers.cust_desc ,cust_relation.cust_code_child
            //                      from cust_relation 
            //                      join customers 
            //                      on cust_relation.cust_code_parent = customers.cust_code)approver1
            //                      JOIN
            //                      customers on customers.cust_code = approver1.cust_code_child
            //                     ');

            $custrel = db::table('cust_relation as cr')
                ->crossjoin('customers as cs1','cr.cust_code_parent','cs1.cust_code' )
                ->crossjoin('customers as cs2','cr.cust_code_child','cs2.cust_code')
                ->select('id','cr.cust_code_parent','cs1.cust_desc as cust_desc_parent','cr.cust_code_child','cs2.cust_desc as cust_desc_child')                
                ->orderby('cr.cust_code_parent','ASC')
                ->paginate(10);
              
                

            // $custrel = db::table('cust_relation')
            //     ->join('customers','cust_relation.cust_code_child','customers.cust_code')
            //     ->select('cust_code_child','cust_desc')
            //     ->orderby('id')
            //     ->join($custrel2,$custrel2('cust_relation.cust_code_parent'),'cust_relation.cust_code_parent')
            //     ->get();
                
            // dd($custrel);

            
            
            $cust = DB::table('customers')
                ->get();
            
            
                
            return view('setting.menu-custrelation', ['data' => $custrel, 'datacust' => $cust]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    //untuk create customer relation
    public function createrelation(Request $req)
    {
        // dd($req->all());

        if ($req->t_custparent == $req->t_custchild) {
            session()->flash('error', 'Parent dan Child tidak boleh sama');
            return back();
        }


        Schema::create('temp_table', function($table){
            $table->string('temp_cust_parent');
            $table->string('temp_cust_child');
            $table->temporary();
        });

        $thistemp = array(
            'temp_cust_parent' => $req->t_custparent,
            'temp_cust_child' => $req->t_custchild
        );

        DB::table('temp_table')->insert($thistemp);

        $tempdata = DB::table('temp_table')
                    ->get();

        // dd($tempdata);

        

        foreach($tempdata as $temp){
            $olddata = DB::table('cust_relation')
                    ->get();

            foreach($olddata as $showold){
                    // dd($showold->cust_code_parent);
                if($temp->temp_cust_parent == $showold->cust_code_parent && $temp->temp_cust_child == $showold->cust_code_child){
                    // dd('test1');
                    Session()->flash('error', 'Data cust. relation sudah ada');
                    return back();
            
                }
            }
        }

        DB::table('cust_relation')
        ->insert([
            'cust_code_parent' => $req->t_custparent,
            'cust_code_child' => $req->t_custchild,
            'created_at' => Carbon::now()->toDateTimeString(),
            'updated_at' => Carbon::now()->toDateTimeString(),
        ]);

        session()->flash('updated', 'Cust. Relation Successfully Created');
        return back();
        
    }

    //untuk edit customer relation
    public function editrelation(Request $req)
    {
        // dd($req->all());
        if ($req->te_custparent == $req->te_custchild) {
            session()->flash('error', 'Parent dan Child tidak boleh sama');
            return back();
        }

        if($req->te_custparent == $req->hidden_parent && $req->te_custchild == $req->hidden_child){
            session()->flash('updated', 'Tidak ada data yang diupdate');
            return back();
        }


        Schema::create('temp_table', function($table){
            $table->string('temp_cust_parent');
            $table->string('temp_cust_child');
            $table->temporary();
        });

        $thistemp = array(
            'temp_cust_parent' => $req->te_custparent,
            'temp_cust_child' => $req->te_custchild
        );

        DB::table('temp_table')->insert($thistemp);

        $tempdata = DB::table('temp_table')
                    ->get();

        // dd($tempdata);

        

        foreach($tempdata as $temp){
            $olddata = DB::table('cust_relation')
                    ->get();

            foreach($olddata as $showold){
                    // dd($showold->cust_code_parent);
                if($temp->temp_cust_parent == $showold->cust_code_parent && $temp->temp_cust_child == $showold->cust_code_child){
                    // dd('test1');
                    Session()->flash('error', 'Data cust. relation sudah ada');
                    return back();
            
                }
            }
        }


        DB::table('cust_relation')
            ->where('id', '=', $req->idrel)
            ->update([
                'cust_code_parent' => $req->te_custparent,
                'cust_code_child' => $req->te_custchild,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);

        // dd($req->te_custparent);


        session()->flash('updated', 'Cust. Relation Successfully Updated');
        return back();
    }
    //unutk delete customer relation
    public function deleterelation(Request $req)
    {
        DB::table('cust_relation')
            ->where('cust_code_parent', '=', $req->d_custparent)
            ->delete();


        session()->flash('updated', 'Cust. Relation Successfully Deleted');
        return back();
    }
    
    //untuk paginate customer relation
    public function paginatecustrelation(Request $req)
    {
        if ($req->ajax()) {
            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $parent = $req->get('parent');
            $child = $req->get('child');
            // $activitycode = $req->get('activitycode');


            if ($parent == '' && $child == '') {
                // $data = db::select('select approver1.id, approver1.cust_code_parent, approver1.cust_desc, approver1.cust_code_child, customers.cust_desc as "nama"
                // from
                // (select cust_relation.id, customers.cust_code, cust_relation.cust_code_parent, customers.cust_desc ,cust_relation.cust_code_child
                //                      from cust_relation 
                //                      join customers 
                //                      on cust_relation.cust_code_parent = customers.cust_code)approver1
                //                      JOIN
                //                      customers on customers.cust_code = approver1.cust_code_child
                //                     ');

                $data = db::table('cust_relation as cr')
                ->crossjoin('customers as cs1','cr.cust_code_parent','cs1.cust_code' )
                ->crossjoin('customers as cs2','cr.cust_code_child','cs2.cust_code')
                ->select('id','cr.cust_code_parent','cs1.cust_desc as cust_desc_parent','cr.cust_code_child','cs2.cust_desc as cust_desc_child')                
                ->orderby($sort_by,$sort_type)
                ->paginate(10);

                return view('setting.table-custrelation', ['data' => $data]);
            } else {
                $query = 'id > 0';

                if ($parent != '') {
                    $query .= ' and cust_code_parent = "' . $parent . '"';
                }
                if ($child != '') {
                    $query .= ' and cust_code_child = "' . $child . '"';
                }

                $data = db::table('cust_relation as cr')
                ->crossjoin('customers as cs1','cr.cust_code_parent','cs1.cust_code' )
                ->crossjoin('customers as cs2','cr.cust_code_child','cs2.cust_code')
                ->select('id','cust_code_parent','cs1.cust_desc as cust_desc_parent','cust_code_child','cs2.cust_desc as cust_desc_child')                
                ->whereRaw($query)
                ->orderby($sort_by,$sort_type)
                ->paginate(10);
                

                return view('setting.table-custrelation', ['data' => $data]);
            }

            // dd('searching kosong');


            // dd('searching ada isi');


        }
    }

    //untuk menampilkan menu activity
    public function menuactivity(Request $req)
    {
        if (strpos(Session::get('menu_access'), 'MT08') !== false) {
            $data = DB::table('activity')
                ->paginate(8);

            return view('setting.menu-activitymt', ['data' => $data]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }


    //untuk create activity
    public function createactivity(Request $req)
    {
        // dd($req->all());
        $this->validate($req, [
            'activity_code' => 'unique:activity',
            'activity_desc' => 'unique:activity'
        ], [
            'activity_code.unique' => 'Activity code sudah ada',
            'activity_desc.unique' => 'Activity desc sudah ada'
        ]);

        DB::table('activity')
            ->insert([
                'activity_code' => $req->activity_code,
                'activity_desc' => $req->activity_desc,
                'created_at' => Carbon::now()->toDateTimeString(),
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);

        // dd($test);
        session()->flash('updated', 'Activity Successfully Created');

        return back();
    }


    //untuk edit activity
    public function editactivity(Request $req)
    {
        // dd($req->all());

        // dd('tahan');
        $this->validate($req, [
            'activity_desc' => 'unique:activity'
        ], [
            'activity_desc.unique' => 'Activity desc sudah ada'
        ]);

        DB::table('activity')
            ->where('activity_code', '=', $req->activity_code)
            ->update([
                'activity_desc' => $req->activity_desc,
                'updated_at' => Carbon::now()->toDateTimeString(),
            ]);

        session()->flash('updated', 'Activity Successfully Updated');

        return back();
    }

    //untuk delete activity
    public function deleteactivity(Request $req)
    {
        // dd($req->all());

        DB::table('activity')
            ->where('activity_code', '=', $req->d_activityid)
            ->delete();

        session()->flash('updated', ' Activity Successfully Deleted');
        return back();
    }


    //untuk paginate activity
    public function paginateactivity(Request $req)
    {
        if ($req->ajax()) {
            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $activitycode = $req->get('activitycode');



            if ($activitycode == '') {
                // dd('searching kosong');
                $data = DB::table('activity')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-activity', ['data' => $data]);
            } else {
                // dd('searching ada isi');


                $data = DB::table('activity')
                    ->where('activity_code', '=', $activitycode)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-activity', ['data' => $data]);
            }
        }
    }

    //customer st maint
    public function custstmain(){
        if (strpos(Session::get('menu_access'), 'MT09') !== false) {    
            $data = DB::table('cust_shipto')
                ->Leftjoin('customers','customers.cust_code','cust_shipto.cust_code')
                ->selectraw('*,cust_shipto.cust_code as custcodeship' )
                ->orderby('cust_shipto.cust_code','asc')
                ->paginate(10);

            
            return view('setting.custshipto', ['data' => $data]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function custshiptoload(){
        //WSA custshipto
        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
		$qxReceiver     = '';
		$qxSuppRes      = 'false';
		$qxScopeTrx     = '';
		$qdocName       = '';
		$qdocVersion    = '';
		$dsName         = '';
        
		$timeout        = 0;

		// ** Edit here
		$qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                            <Body>
                                <custshipto xmlns="urn:iris.co.id:wsatrain"/>
                            </Body>
                        </Envelope>';

  
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

        //dd($qdocResponse);     
	
		$xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain');  
		
		$flag = 0;
		
		$item = '';
        DB::table('cust_shipto')
            ->truncate();

		foreach($xmlResp->xpath('//ns1:tempRow') as $data4){

				DB::table('cust_shipto')
				->insert([
					'cust_code' => $data4->t_custcode,
                    'shipto' => $data4->t_shipto,
                    'custname' => $data4->t_custname,
                    'custaddr' => $data4->t_shiptoaddr,
            		'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString()
					]);

					
		}

		        
        return back();
    }

    public function custstpaginate(Request $req){
         //dd($req->all());
        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $custcode = $req->get('custcode');
            $shipto = $req->get('shipto');
            
            if ($custcode == '' and $shipto == '') {
                $data = DB::table('cust_shipto')
                    ->leftjoin('customers','cust_shipto.cust_code','customers.cust_code')
                    ->selectraw('*,cust_shipto.cust_code as custcodeship')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-custshipto', ['data' => $data]);
            } else {
                $kondisi = "id>=0";
                
                if ($custcode != '') {
                    $kondisi .= ' and cust_shipto.cust_code = "' . $custcode . '"';
                     //dd($kondisi);
                }
                if ($shipto != '') {
                    $kondisi .= ' and shipto = "' . $shipto . '"';
                }
                //dd($kondisi);
                //dd('aa');
                $data = DB::table('cust_shipto')
                    ->leftjoin('customers','cust_shipto.cust_code','customers.cust_code')
                    ->selectraw('*,cust_shipto.cust_code as custcodeship')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);
                // $data = DB::table('cust_shipto')
                //     ->leftjoin('customers','cust_shipto.cust_code','customers.cust_code')
                //     ->selectRaw('*,cust_shipto.cust_code as custcodeship')
                //     ->whereRaw($kondisi)
                //     ->orderBy($sort_by, $sort_type)
                //     ->paginate(10);
                
                //dd($data);
                return view('setting.table-custshipto', ['data' => $data]);
            }
        }
    }

    //item konversi maint
    public function itemkonvmenu(){
        if (strpos(Session::get('menu_access'), 'MT10') !== false) {
            $data = DB::table('item_konversi')
                    ->paginate(10);
            $um1 = DB::table('item_konversi')
                    ->select('um_1')
                    ->groupBy('um_1')
                    ->get();
            $um2 = DB::table('item_konversi')
                    ->select('um_2')
                    ->groupBy('um_2')
                    ->get();
            return view('setting.itemkonversi', ['data' => $data, 'um_1' => $um1, 'um_2' => $um2]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function loaditemkonv(){
        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
            $qxReceiver     = '';
            $qxSuppRes      = 'false';
            $qxScopeTrx     = '';
            $qdocName       = '';
            $qdocVersion    = '';
            $dsName         = '';
            
            $timeout        = 0;
    
            // ** Edit here
            $qdocRequest =  '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
                                <Body>
                                    <itemconv xmlns="urn:iris.co.id:wsatrain">
                                        <inpdomain>DKH</inpdomain>
                                    </itemconv>
                                </Body>
                            </Envelope>';
    
      
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
            
            $flag = 0;
            
            $item = '';
		
		foreach($xmlResp->xpath('//ns1:tempRow') as $data3){
			$shipto = DB::table('item_konversi')
				->where('item_code','=',$data3->t_itemcode)
				->first();
		
			if($shipto){
				DB::table('item_konversi')
				->where('item_code','=',$data3->t_itemcode)
				->update([
                        		   'um_1' => $data3->t_um1,
                        		   'um_2' => $data3->t_um2,
                        		   'qty_item' => $data3->t_qtyitem,
                        		   'updated_at' =>  Carbon::now('ASIA/JAKARTA')->toDateTimeString()

					]);
			           }
			else{DB::table('item_konversi')
				->insert([

					   'item_code' => $data3->t_itemcode,
                        		   'um_1' => $data3->t_um1,
                        		   'um_2' => $data3->t_um2,
                        		   'qty_item' => $data3->t_qtyitem,
                        		   'created_at' =>  Carbon::now('ASIA/JAKARTA')->toDateTimeString()
					]);

				}	
		}
            
            Session()->flash('updated', 'Table data value has been updated');
            return back();

    }

    public function itemkonvpaging(Request $req)
    {
        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $itemkonvcode = $req->get('itemcode');
            $um1 = $req->get('um1');
            $um2 = $req->get('um2');

            if ($itemkonvcode == '' and $um1 == '' and $um2 == '') {
                $data = DB::table('item_konversi')
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('setting.table-itemkonversi', ['data' => $data]);
            } else {
                $kondisi = "id>=0";

                if ($itemkonvcode != '') {
                    $kondisi .= ' and item_code = "' . $itemkonvcode . '"';
                    // dd($kondisi);
                }
                if ($um1 != '') {
                    $kondisi .= ' and um_1 = "' . $um1 . '"';
                }
                if ($um2 != '') {
                    $kondisi .= ' and um_2 = "' . $um2 . '"';
                    // dd($kondisi);
                }


                $data = DB::table('item_konversi')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                //dd($data);
                return view('setting.table-itemkonversi', ['data' => $data]);
            }
        }
    }
    // Approval Maint
    public function approvalmenu(Request $req)
    {
        if (strpos(Session::get('menu_access'), 'MT11') !== false) {
            $data = DB::table('site_mstrs')
                ->leftjoin('approvals', 'approvals.site_app', 'site_mstrs.site_code')
                ->groupBy('site_code')
                ->get();

            // dd($data);

            $user = DB::table('users')
                ->orderBy('name', 'ASC')
                ->get();

            //dd($data);

            return view('setting.approvalmaint', ['data' => $data, 'user' => $user]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }


    public function createapproval(Request $req)
    {
        // dd($req->all());

        $site = $req->site;
        $site = substr($site, 0, strpos($site, ' '));


        if (is_null($req->userid) ) {
            
        } else {
            $listapprover = '';
            $flg = 0;
            $order = '';
            // hitung ada brp data
            foreach ($req->userid as $data) {
                $flg += 1;
            }
            // loop & kasi error klo ada duplikat
            for ($x = 0; $x < $flg; $x++) {

                if (strpos($listapprover, $req->userid[$x]) !== false) {
                    // Approver sama kirim error
                    session()->flash("error", "Approver tidak boleh sama");
                    return back();
                }
                if ($order == $req->order[$x]) {
                    return redirect()->back()->with('error', 'Nomor order tidak boleh sama');
                }

                $order .= $req->order[$x];

                $listapprover .= $req->userid[$x];
            }
        }

        DB::table('approvals')
                ->where('site_app', '=', $site)
                ->delete();


        if(is_null($req->userid)){
            session()->flash('updated', 'Approval Berhasil Didelete untuk Site : ' . $site);
            return back();
        }else{
            if (count($req->userid) > 0) {
                foreach ($req->userid as $item => $v) {

                    $data2 = array(
                        'userid' => $req->userid[$item],
                        'site_app' => $site,
                        'order' => $req->order[$item],
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString()
                    );
                    DB::table('approvals')->insert($data2);
                }
            }
        }

        session()->flash('updated', 'Approval Berhasil Diupdate untuk Site : ' . $site);
        return back();
    }

    public function approvalsearch(Request $req)
    {
        // dd($req->search)
        if ($req->ajax()) {
            $data = DB::table('approvals')
                ->where('site_app', '=', $req->search)
                ->get();

            $datauser = DB::table('users')
                ->orderBy('name', 'ASC')
                ->get();

            if (!is_null($data)) {
                $output = "";

                foreach ($data as $data) {
                    $output .= "<tr>" .

                        "<td data-label='Approver'>
                        <select id='userid[]' class='form-control userid' name='userid[]' required autofocus>";
                    foreach ($datauser as $newuser) :
                        if ($data->userid == $newuser->username) :
                            $output .= '<option value=' . $newuser->username . ' Selected >' . $newuser->username . ' -- ' . $newuser->name . '</option>';
                        else :
                            $output .= '<option value=' . $newuser->username . ' >' . $newuser->username . ' -- ' . $newuser->name . '</option>';
                        endif;
                    endforeach;
                    $output .= "</select>
                    </td>" .

                        "<td data-label='Order'> 
                        <input type='number' class='form-control order' min='1' step='1' Autocomplete='Off' id='order[]' name='order[]' style='height:38px' value='" . $data->order . "' required/>
                    </td>" .

                        "<td data-title='Action'><input type='button' class='ibtnDel btn btn-danger'  value='Delete'></td>" .

                        "<tr>";
                }
                return response($output);
            }
        }
    }

    //30112020
    public function searchingapp(Request $req){
        if ($req->ajax()) {
            $sitecode = $req->get('sitecode');
            $sitedesc = $req->get('sitedesc');

            if ($sitecode == '' and $sitedesc == '') {
                // dd('aaaa');
                $data = DB::table('site_mstrs')
                    ->leftjoin('approvals', 'approvals.site_app', 'site_mstrs.site_code')
                    ->groupBy('site_code')
                    ->get();

                $user = DB::table('users')
                    ->orderBy('name', 'ASC')
                    ->get();

                    return view('setting.table-menuapproval', ['data' => $data, 'user' => $user]);
            } else {
                $kondisi = "site_code != ''";

                if ($sitecode != '') {
                    $kondisi .= ' and site_code = "' . $sitecode . '"';
                    // dd($kondisi);
                }
                if ($sitedesc != '') {
                    $kondisi .= ' and site_desc = "' . $sitedesc . '"';
                }

                $data = DB::table('site_mstrs')
                        ->leftjoin('approvals', 'approvals.site_app', 'site_mstrs.site_code')
                        ->whereRaw($kondisi)
                        ->groupBy('site_code')
                        ->get();

                $user = DB::table('users')
                        ->orderBy('name', 'ASC')
                        ->get();

                //dd($data);
                return view('setting.table-menuapproval', ['data' => $data, 'user' => $user]);
            }
        }
    }

    public function salesactivitymenu(Request $req)
    {
        // dd($req->all());
        $status = '';
        $trigger = '';
        $kecustomer = '';
        $waktucheckin='';
        if (strpos(Session::get('menu_access'), 'TK01') !== false) {
            $cust = DB::table('customers')
                ->where('customer_site','=',Session::get('site'))
                ->get();
            //dd($data);
            $acti = DB::table('activity')
                ->get();
            $username = Session::get('username');
            $salesid = DB::table('sales_activity')
                ->join('customers','sales_activity.to_cust','=','customers.cust_code')
                ->where('username_sales', '=', $username)
		->selectRaw('*,sales_activity.created_at as "saca"')
                ->orderby('id', 'desc')
                ->first();
            $salesinout = DB::table('sales_activity')
                ->select('inout')
                ->where('username_sales', '=', $username)
                ->orderby('id', 'desc')
                ->first();

            
            if ($salesid == null) {
                $status = "checkout";
            } else {


                if ($salesinout->inout == 'checkin') {
                    $status = "checkin";
                    $kecustomer = $salesid->cust_desc;
                    $waktucheckin =strval($salesid->saca);
                } else if ($salesinout->inout == 'checkout') {

                    $kecustomer = "--";
                    $waktucheckin ="--";
                    $status = "checkout";
                }
            }
            $id = '';
            

            // dd($salesinout);
            return view('so.salesactivity', ['cust' => $cust, 'acti' => $acti, 'status' => $status, 'id' => $id,'kecustomer' => $kecustomer,'waktucheckin'=>$waktucheckin]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }

    public function sabutton(Request $req)
    {
        // dd($req->all());
        $username = Session::get('username');
        $status = $req->get('status');
        $customer = $req->get('sacustomer');
        $activity = $req->get('sactivity');
        if ($status == 'checkout') {
            // dd('test1');

            if ($customer != '') {
                $datarray = array(
                    'to_cust' => $customer,
                    'username_sales' => $username,
                    'created_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                    'inout' => 'checkin'
                );
                DB::table('sales_activity')->insert($datarray);

                $salesid = DB::table('sales_activity')
		    ->join('customers','customers.cust_code','sales_activity.to_cust')
->selectRaw('*,sales_activity.created_at as "saca"')                    
->where('username_sales', '=', $username)
                    ->orderby('id', 'desc')
                    ->first();

                $id = $salesid->id;
                $status = $salesid->inout;

                $kecustomer = $salesid->cust_desc;
                $waktucheckin = $salesid->saca;
                
                $cust = DB::table('customers')
                    ->where('customer_site','=',Session::get('site'))
                    ->get();
                //dd($data);
                $acti = DB::table('activity')
                    ->get();
                // return view('so.salesactivity', ['id' => $id, 'status' => $status, 'cust' => $cust, 'acti' => $acti, 'kecustomer' => $kecustomer, 'waktucheckin' => $waktucheckin]);

                return redirect()->route('slsactivity');
            } else {
                session()->flash("error", "Pilih Customer");
                // return redirect('/salesactivity');
                return redirect()->route('slsactivity');
            }
        } else if ($status == 'checkin') {
            // dd('test2');
            // $salesid = $req->get('id');
            if ($activity != '') {
                DB::table('sales_activity')
                    ->where('username_sales', '=', $username)
                    ->where('inout', '=', $status)
                    ->update([
                        'activity_sales' => $activity,
                        'inout' => 'checkout',
                        'updated_at' => Carbon::now('ASIA/JAKARTA')->toDateTimeString(),
                    ]);

                $salesid = DB::table('sales_activity')
                    ->where('username_sales', '=', $username)
                    ->orderby('id', 'desc')
                    ->first();

                $kecustomer = '--';
                $waktucheckin = '--';
                
                $status = 'checkout';
                $cust = DB::table('customers')
                    ->where('customer_site','=',Session::get('site'))
                    ->get();
                //dd($data);
                $acti = DB::table('activity')
                    ->get();
                $id = '';
                
                // return view('so.salesactivity', ['id' => $id, 'status' => $status, 'cust' => $cust, 'acti' => $acti,  'kecustomer' => $kecustomer, 'waktucheckin' => $waktucheckin]);
                
                return redirect()->route('slsactivity');
            } else if ($activity == '') {
                session()->flash("error", "Pilih Activity");
                // return redirect('/salesactivity');
                return redirect()->route('slsactivity');
            }
        }
    }

    public function sosconfirmmenu()
    {    
        if (strpos(Session::get('menu_access'), 'TS03') !== false) {
            
            $username = Session::get('username');
            $soslist = DB::table('do_mstr')
                ->join('customers', 'do_mstr.do_cust', 'customers.cust_code')
                // ->join('cust_shipto','do_mstr.do_shipto')
                ->where('do_user', '=', $username)
                ->where('do_status', '=', 1)
                ->orderby('do_nbr', 'asc')
                ->paginate(10);

            
            $sosedit = '';
            return view('so.soshipmentconfirm', ['soslist' => $soslist, 'sosedit' => $sosedit]);
        } else {
            Session()->flash('error', 'Anda tidak memiliki akses menu, Silahkan kontak admin');
            return back();
        }
    }
    public function soshipmentgetinfo(Request $req)
    {
        
        if ($req->ajax()) {
            $donbr = $req->get('donbr');
            $sosedit = DB::table('dod_det')
                ->join('do_mstr', 'dod_det.dod_nbr', 'do_mstr.do_nbr')
                ->join('so_mstrs', 'dod_det.dod_so', 'so_mstrs.so_nbr')
                ->join('so_dets', function($join){
                    $join->on('dod_det.dod_so','so_dets.so_nbr');
                    $join->on('dod_det.dod_part','so_dets.so_itemcode');
                    $join->on('dod_det.dod_line','so_dets.so_line');
                })
                ->leftjoin('items', 'dod_det.dod_part', 'items.itemcode')
                ->selectRaw("*, so_mstrs.created_at as 'created_at'")
                ->where('do_nbr', '=', $req->search)
                ->orderby('do_nbr')
                ->get();
                

            $output = '';
            
            // return response($sosedit);
            if (count($sosedit) != 0) {
                foreach ($sosedit as $sosedit) {
                    $new_duedate = date('d-m-Y', strtotime($sosedit->so_duedate));
                    $new_created_at = date('d-m-Y', strtotime($sosedit->created_at));
                    $new_item_price = number_format((float)$sosedit->so_harga * (int)$sosedit->dod_qty,0);
                
                     // if(strpos($sosedit->so_harga,".00000") !== false){
                     //       $new_item_price =  number_format($new_item_price,2,'.',',');
                     // }else{
                     //    if(strpos(strrev(rtrim(($new_item_price), "0")), ".") == 1){
                     //       $new_item_price = number_format($new_item_price,2,'.',',');
                     //    }else{
                     //        $new_item_price = rtrim(number_format($new_item_price,2,'.',','), "0");
                     //    }
                     // }

                    // dd($sosedit);
                    $output .= "<tr>" .
                        "<td>"
                        . $sosedit->dod_so .
                        "</td>" .
                        "<td>"
                        . $sosedit->dod_part .
                        "</td>" .
                        "<td>"
                        . $sosedit->itemdesc .
                        "</td>" .
                        "<td>"
                        . $sosedit->do_shipto .
                        "</td>" .
                        "<td>"
                        . $sosedit->dod_qty .
                        "</td>" .
                        "<td>"
                        .$new_item_price.
                        "</td>" .
                        "<td>"
                        . $new_duedate .
                        "</td>" .
                        "<td>"
                        . $new_created_at .
                        "</td>" .
                        "</tr>";
                }
            }        
            return Response($output);
        }
    }
    
    public function sosconfirm(Request $req)
    {
	
        $testarray[]='';
        $testarray2[]='';
        $sosedit = DB::table('dod_det')
                ->join('do_mstr', 'dod_det.dod_nbr', 'do_mstr.do_nbr')
                ->join('so_mstrs', 'dod_det.dod_so', 'so_mstrs.so_nbr')
                ->leftjoin('items', 'dod_det.dod_part', 'items.itemcode')
                ->selectRaw("*, so_mstrs.created_at as 'created_at'")
                ->where('do_nbr', '=', $req->e_suratjalan)
                ->where('do_status','=',4)
                ->orderby('do_nbr')
                ->first();

        $sosedit2 = DB::table('dod_det')
                ->join('do_mstr', 'dod_det.dod_nbr', 'do_mstr.do_nbr')
                ->join('so_mstrs', 'dod_det.dod_so', 'so_mstrs.so_nbr')
                ->leftjoin('items', 'dod_det.dod_part', 'items.itemcode')
                ->selectRaw("*, so_mstrs.created_at as 'created_at'")
                ->where('do_nbr', '=', $req->e_suratjalan)
                ->where('do_status','=',4)
                ->orderby('do_nbr')
                ->get();

        $solistnum = DB::table('dod_det')
                ->join('do_mstr', 'dod_det.dod_nbr', 'do_mstr.do_nbr')
                ->join('so_mstrs', 'dod_det.dod_so', 'so_mstrs.so_nbr')
                ->leftjoin('items', 'dod_det.dod_part', 'items.itemcode')
                ->selectRaw('dod_so')
                ->where('do_nbr', '=', $req->e_suratjalan)
                ->where('do_status','=',4)
                ->groupBy('dod_so')
                ->orderby('do_nbr')
                ->get();                

	$testtr = array();
	$arrxml = array();
	$test = count($sosedit2);
	$temparray[$test] = array();
	foreach($sosedit2 as $show){
		
			// 	// Validasi WSA --> SPB


			// // 	// Validasi WSA
			// 	$qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
			// 	$qxReceiver     = '';
			// 	$qxSuppRes      = 'false';
			// 	$qxScopeTrx     = '';
			// 	$qdocName       = '';
			// 	$qdocVersion    = '';
			// 	$dsName         = '';
		        
			// 	$timeout        = 0;

			// 	// ** Edit here
			// 	$qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">
		 //    					<Body>
		 //        				  <sisaQty xmlns="urn:iris.co.id:wsatrain">
		 //            					<inpdomain>'.'DKH'.'</inpdomain>
		 //            					<inpart>'.$show->itemcode.'</inpart>
		 //            					<insite>'.Session::get('site').'</insite>
		 //        				  </sisaQty>
		 //    					</Body>
			// 			</Envelope>';
		  
			// 	$curlOptions = array(CURLOPT_URL => $qxUrl,
			// 						 CURLOPT_CONNECTTIMEOUT => $timeout,        // in seconds, 0 = unlimited / wait indefinitely.
			// 						 CURLOPT_TIMEOUT => $timeout + 120, // The maximum number of seconds to allow cURL functions to execute. must be greater than CURLOPT_CONNECTTIMEOUT
			// 						 CURLOPT_HTTPHEADER => $this->httpHeader($qdocRequest),
			// 						 CURLOPT_POSTFIELDS => preg_replace("/\s+/", " ", $qdocRequest),
			// 						 CURLOPT_POST => true,
			// 						 CURLOPT_RETURNTRANSFER => true,
			// 						 CURLOPT_SSL_VERIFYPEER => false,
			// 						 CURLOPT_SSL_VERIFYHOST => false);
							 
			// 	$getInfo = '';
			// 	$httpCode = 0;
			// 	$curlErrno = 0;
			// 	$curlError = '';
			// 	$qdocResponse = '';

			// 	$curl = curl_init();
			// 	if ($curl) {
			// 		curl_setopt_array($curl, $curlOptions);
			// 		$qdocResponse = curl_exec($curl);           // sending qdocRequest here, the result is qdocResponse.
			// 		$curlErrno    = curl_errno($curl);
			// 		$curlError    = curl_error($curl);
			// 		$first        = true;
				
			// 		foreach (curl_getinfo($curl) as $key=>$value) {
			// 			if (gettype($value) != 'array') {
			// 				if (! $first) $getInfo .= ", ";
			// 				$getInfo = $getInfo . $key . '=>' . $value;
			// 				$first = false;
			// 				if ($key == 'http_code') $httpCode = $value;
			// 			}
			// 		}
			// 		curl_close($curl);
					
			// 	}
				
				   
			// 	$xmlResp = simplexml_load_string($qdocResponse);       
			
			// 	$xmlResp->registerXPathNamespace('ns1', 'urn:iris.co.id:wsatrain');  
			// 	$batasatas = $xmlResp->xpath('//ns1:t_qty')[0];
				
			// 	$testbatas = floatval($batasatas) - $dow;
				
			// 	$qtyweb = $show->dod_qty;
			// 	$qtyall = $testbatas - $qtyweb;
			// 	if($qtyall >=0){
			// 	  array_push($temparray,"yes");
			// 	}
			// 	else if ($qtyall <0){
			// 		array_push($temparray,"no");
				
			// 	}

			// }

			// 	if(in_array("no",$temparray)){
		 // 			session()->flash('error', 'Jumlah Barang di QAD tidak cukup');
		 //           		return back();
			// 	}


			// 	else{
		            foreach($solistnum as $sol){

		                $sosedit3 = DB::table('dod_det')
		                ->join('do_mstr', 'dod_det.dod_nbr', 'do_mstr.do_nbr')
		                ->join('so_mstrs', 'dod_det.dod_so', 'so_mstrs.so_nbr')
		                ->leftjoin('items', 'dod_det.dod_part', 'items.itemcode')
		                ->selectRaw("*, so_mstrs.created_at as 'created_at'")
		                ->where('do_nbr', '=', $req->e_suratjalan)
		                ->where('do_status','=',4)
		                ->where('dod_so','=',$sol->dod_so)
		                ->orderby('do_nbr')
		                ->get();


				        $qxUrl          = 'http://qadeedpaint.svr:8081/qxiqadtrain/services/QdocWebService';
		            		$qxReceiver     = '';
		            		$qxSuppRes      = 'false';
		            		$qxScopeTrx     = '';
		            		$qdocName       = '';
		            		$qdocVersion    = '';
		            		$dsName         = '';
		                    
		                    $timeout        = 0;
		                    
		                    // ** Edit here
		                        
		                    $qdocHeader = '<?xml version="1.0" encoding="UTF-8"?>
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
		                        <shipSalesOrder>
		                            <qcom:dsSessionContext>
		                                <qcom:ttContext>
		                                    <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                    <qcom:propertyName>domain</qcom:propertyName>
		                                    <qcom:propertyValue>DKH</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                    <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                    <qcom:propertyName>scopeTransaction</qcom:propertyName>
		                                    <qcom:propertyValue>true</qcom:propertyValue>
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
		                                <qcom:ttContext>
		                                    <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                    <qcom:propertyName>username</qcom:propertyName>
		                                    <qcom:propertyValue>mfg</qcom:propertyValue>
		                                </qcom:ttContext>
		                                <qcom:ttContext>
		                                    <qcom:propertyQualifier>QAD</qcom:propertyQualifier>
		                                    <qcom:propertyName>password</qcom:propertyName>
		                                    <qcom:propertyValue>XVytW</qcom:propertyValue>
		                                </qcom:ttContext>
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
		            	
		                    $qdocBody = '<dsSalesOrderShipment>
		                                    <SalesOrderShipment>
		                                        <soNbr>'.$sol->dod_so.'</soNbr>
		                                        <document>'.$sosedit->dod_nbr.'</document>';
		                                        foreach($sosedit3 as $sosedit3){ 
		                                            $qdocBody.= '
		            				                <lineDetail>
		                                            <line>'.$sosedit3->dod_line.'</line>
		                                            <lotserialQty>'.$sosedit3->dod_qty.'</lotserialQty>
                                                <location>'.$sosedit3->item_location.'</location>
		                                            <yn>true</yn>
		                                            <yn1>true</yn1>        
		                                            </lineDetail>';
		                                        }
		                    $qdocfooter =   '</SalesOrderShipment> 
		                                    </dsSalesOrderShipment>
		                                            </shipSalesOrder>
		                                            </soapenv:Body>
		                                            </soapenv:Envelope>';
		                                            
		                    $qdocRequest = $qdocHeader.$qdocBody.$qdocfooter;
                            
		                    array_push($arrxml,$qdocRequest);  
		                    
		            		// $qdocRequest =   '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
		                    //                     '<Body>'.
		                    //                         '<supp_master xmlns="urn:iris.co.id:wsatrain">'.
		                    //                             '<inpdomain>DKH</inpdomain>'.
		                    //                         '</supp_master>'.
		                    //                     '</Body>'.
		                    //                 '</Envelope>';

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
		                    //dd($qdocResponse);
		            	    $xmlResp->registerXPathNamespace('ns1', 'urn:schemas-qad-com:xml-services');  
		                    $xmlResp->registerXPathNamespace('ns3', 'urn:schemas-qad-com:xml-services:common');    
		                    //dd($qdocResponse,$xmlResp->xpath('//ns3:tt_msg_desc'));
		                    //dd($qdocResponse);
		            	
		                   	array_push($testtr,$xmlResp->xpath('//ns1:result')[0]);
		                 
		        	}
		        
		        	if (in_array('error', $testtr)){
		            	array_push($testarray,$xmlResp->xpath('//ns1:result'));
		            	array_push($testarray2,$xmlResp->xpath('//ns3:tt_msg_desc'));
		            
		            	session()->flash('error', 'Shipment Gagal Diapprove');
		            	return back();
		        	}
		        	else{
		            	array_push($testarray,$xmlResp->xpath('//ns1:result'));
		            	array_push($testarray2,'success');
		            
                        // $newsosedit = DB::table('dod_det')
                        //     ->join('do_mstr', 'dod_det.dod_nbr', 'do_mstr.do_nbr')
                        //     ->join('so_mstrs', 'dod_det.dod_so', 'so_mstrs.so_nbr')
                        //     ->join('so_dets','dod_det.dod_so','so.dets_so.nbr')
                        //     ->leftjoin('items', 'dod_det.dod_part', 'items.itemcode')
                        //     ->selectRaw("*, so_mstrs.created_at as 'created_at'")
                        //     ->where('do_nbr', '=', $req->e_suratjalan)
                        //     ->where('do_status','=',4)
                        //     ->where('do_site','=',Session::get('site'))
                        //     ->orderby('do_nbr')
                        //     ->get();

                        // foreach($newsosedit as $sol){
                        //     $tablecek = DB::table('so_dets')
                        //                 ->where('so_nbr','=',$sol->dod_so)
                        //                 ->where('so_itemcode','=',$sol->dod_so)
                        //                 ->where ('so_line','=',$sol->dod_line)            
                        //                 ->select(/*nama kolom*/)
                        //                 ->get();

                        //     if($tablecek > 0){
                        //         $shipnow = /*value kolom ship sekarang*/;
                        //         $newship = $shipnow + $sol->doqty;            
                        //         DB::table('so_dets')
                        //          ->where('so_nbr','=',$sol->dod_so)
                        //          ->where('so_itemcode','=',$sol->dod_so)
                        //          ->where ('so_line','=',$sol->dod_line)
                        //          ->update([/*nama kolom => $newship*/]);
                        //     }

                        //     else if($tablecek == 0){
                        //         DB::table('so_dets')
                        //          ->where('so_nbr','=',$sol->dod_so)
                        //          ->where('so_itemcode','=',$sol->dod_so)
                        //          ->where ('so_line','=',$sol->dod_line)
                        //          ->update([/*nama kolom => value*/]);

                        //     }
                                
                        // }
                        
                           DB::table('do_mstr')
                            ->where('do_nbr', '=', $req->e_suratjalan)
                            ->update(['do_status' => 2,
				      'tanggal_confirm' =>  Carbon::now('ASIA/JAKARTA')->toDateTimeString()
				    ]);

                            DB::table('dod_det')
                             ->where('dod_nbr', '=', $req->e_suratjalan)
                             ->update(['dod_status' => 2]);
		            	session()->flash('updated', 'Shipment Berhasil Diconfirm');
		             	return back();
                    }
		  }
    }
            // }
		              



    public function sopaging(Request $req)
    {
        
        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $suratjalan = $req->get('suratjalan');
            $customer = $req->get('customercode');


            if ($suratjalan == '' and $customer == '') {
                // dd('aaaa');
                $soslist = DB::table('do_mstr')
                    ->join('customers', 'do_mstr.do_cust', 'customers.cust_code')
                    // ->join('cust_shipto','do_mstr.do_shipto')
                    ->where('do_user', '=', Session::get('username'))
                    ->where('do_status', '=', 0)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('so.table-soshipmentconfirm', ['soslist' => $soslist]);
            } else {
                $username = Session::get('username');
                $kondisi = "do_status = 0";

                if ($suratjalan != '') {
                    $kondisi .=  ' and do_user = "' . $username . '"' . ' and do_nbr = "' . $suratjalan . '"';
                }
                if ($customer != '') {
                    $kondisi .= ' and do_user = "' . $username . '"'.' and do_cust = "' . $customer . '"';
                }

                $soslist = DB::table('do_mstr')
                    ->join('customers', 'do_mstr.do_cust', 'customers.cust_code')
                    ->selectRaw('*')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                return view('so.table-soshipmentconfirm', compact('soslist'));
            }
        }
    }

    public function indchangepass(Request $req)
    {
        $value = $req->session()->get('username');
        $value1 = $req->session()->get('userid');

        $users = DB::table("users")
                    ->where("users.id",$value1)
                    ->first();

        return view('/auth/changepw', compact('users'));
    }

    public function changepass(Request $request)
    {
        $id = $request->input('id');
        $password = $request->input('password');
        $confpass = $request->input('confpass');
        $oldpass = $request->input('oldpass');

        $hasher = app('hash');

        $users = DB::table("users")
                    ->select('id','password')
                    ->where("users.id",$id)
                    ->first();

        if($hasher->check($oldpass,$users->password))
        {
            if($password != $confpass)
            {
                session()->flash("error","Password & Confirm Password Berbeda");
                return back();
            }else{
                DB::table('users')
                ->where('id', $id)
                ->update(['password' => Hash::make($password)]);

                session()->flash("updated","Password Successfully Updated !");
                return back();
            }
        }else{
                session()->flash("error","Old Password is Wrong");
                return back();    
        }  
    }
   
    public function checkincheckoutbrowse()
    {
        //dd('123');
        
        if (strpos(Session::get('menu_access'), 'TK02') !== false) {
           
            $username = Session::get('username');

	    $salesman = DB::table('sales_activity')
		->join('users','sales_activity.username_sales','=','users.username')
                ->where('users.site', '=', Session::get('site'))
                ->groupBy('users.name')
                ->orderby('users.name', 'asc')
                ->get();

            $customers = DB::table('sales_activity')
		->join('customers','sales_activity.to_cust','=','customers.cust_code')
                
                ->groupBy('customers.cust_code')
                ->orderby('customers.cust_desc', 'asc')
                ->get();
	
		//dd($customers);

            $data = DB::table('sales_activity')
                ->join('customers','sales_activity.to_cust','=','customers.cust_code')
		->join('users','sales_activity.username_sales','=','users.username')
		->leftjoin('activity', 'sales_activity.activity_sales', '=', 'activity.activity_code')
                ->where('users.site', '=', Session::get('site'))
		->selectRaw('*,sales_activity.created_at as "checkindate", sales_activity.updated_at as "checkoutdate"')
                ->orderby('sales_activity.id', 'desc')
                ->paginate(5);
	   return view('so.salesactivitybrowse', ['data' => $data, 'salesman'=>$salesman, 'customers'=>$customers]);
    }

    
    }

    public function salesactivitysearch(Request $req)
    {
        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $salesman = $req->get('salesman');
            $customer = $req->get('customer');
 	    $checkindate = $req->get('checkindate');
            $checkoutdate = $req->get('checkoutdate');
	   //dd($checkindate);
	
            if ($salesman == '' and $customer == '' and $checkindate == '' and $checkoutdate == '') {
                // dd('aaaa');
                $data = DB::table('sales_activity')
		        ->join('customers','sales_activity.to_cust','=','customers.cust_code')
			->join('users','sales_activity.username_sales','=','users.username')
			->leftjoin('activity', 'sales_activity.activity_sales', '=', 'activity.activity_code')
		        ->where('users.site', '=', Session::get('site'))
			->selectRaw('*,sales_activity.created_at as "checkindate", sales_activity.updated_at as "checkoutdate"')
		        ->orderby('sales_activity.id', 'desc')
		        ->paginate(5);

                return view('so.table-checkincheckout', ['data' => $data]);
            } else {
                $username = Session::get('username');
                $kondisi = "sales_activity.id > 0";

                if ($salesman != '') {
                    $kondisi .=  ' and username_sales = "' . $salesman . '"';
                }
                if ($customer != '') {
                    $kondisi .= ' and to_cust = "' . $customer . '"';
                }
		if ($checkindate != '') {
                    $kondisi .= ' and date(sales_activity.created_at) = "' . $checkindate . '"';
                }
		if ($checkoutdate != '') {
                    $kondisi .= ' and date(sales_activity.updated_at) = "' . $checkoutdate . '"';
                }

                $data = DB::table('sales_activity')
		        ->join('customers','sales_activity.to_cust','=','customers.cust_code')
			->join('users','sales_activity.username_sales','=','users.username')
			->leftjoin('activity', 'sales_activity.activity_sales', '=', 'activity.activity_code')
			->whereRaw($kondisi)		        
			->where('users.site', '=', Session::get('site'))
			->selectRaw('*,sales_activity.created_at as "checkindate", sales_activity.updated_at as "checkoutdate"')
		        ->orderby('sales_activity.id', 'desc')
		        ->paginate(5);

                return view('so.table-checkincheckout', compact('data'));
            }
        }
    }


    public function itemchildmenu(Request $req){
        $data = DB::table('itemchilds')
                    ->paginate(10);

        $dropdown = DB::table('itemchilds')
                    ->get();

        return view('setting.itemchild',['data' => $data, 'dropdown' => $dropdown]);
    }

    public function loaditemchild(Request $req){
        // Validasi WSA
        $qxUrl          = 'http://qadeedpaint.svr:8080/wsatrain/wsa1';
        $qxReceiver     = '';
        $qxSuppRes      = 'false';
        $qxScopeTrx     = '';
        $qdocName       = '';
        $qdocVersion    = '';
        $dsName         = '';
        
        $timeout        = 0;

        $domain         = 'DKH';
        // ** Edit here
        $qdocRequest =      '<Envelope xmlns="http://schemas.xmlsoap.org/soap/envelope/">'.
                            '<Body>'.
                            '<childcons xmlns="urn:iris.co.id:wsatrain">'.
                            '<inpdomain>'.$domain.'</inpdomain>'.
                            '</childcons>'.
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
        
        if($qdocResultx == 'true'){
            DB::table('itemchilds')->delete();
            foreach($dataloop as $data){
                DB::table('itemchilds')
                        ->insert([
                            'item_code' => $data->t_part,
                            'item_child' => $data->t_child,
                            'item_qty_per' => $data->t_qty,
                        ]);
            }
        }else{
            Session()->flash('error', 'Load data failed');
            return back();
        }

        
        
        Session()->flash('updated', 'Table data value has been updated');
        return back();
    }

    public function itemchildpaging(Request $req)
    {
        if ($req->ajax()) {

            $sort_by = $req->get('sortby');
            $sort_type = $req->get('sorttype');
            $itemcode = $req->get('itemcode');
            $childcode = $req->get('childcode');


            if ($itemcode == '' and $childcode == '') {
                // dd('aaaa');
                $data = DB::table('itemchilds')
                    ->paginate(10);

                $dropdown = DB::table('itemchilds')
                            ->get();

                return view('setting.table-itemchild',['data' => $data, 'dropdown' => $dropdown]);
            } else {
                $kondisi = "id > 0";

                if ($itemcode != '') {
                    $kondisi .= ' and item_code = "' . $itemcode . '"';
                }
                if ($childcode != '') {
                    $kondisi .= ' and item_child = "' . $childcode . '"';
                }

                $data = DB::table('itemchilds')
                    ->whereRaw($kondisi)
                    ->orderBy($sort_by, $sort_type)
                    ->paginate(10);

                $dropdown = DB::table('itemchilds')
                            ->get();

                return view('setting.table-itemchild', compact('data','dropdown'));
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class MasterController extends Controller
{
    public function getMasterData($type,$id='')
    {
        try{
            $masterData = [];
            if(!empty($type))
            {
                if($type == 'country') {
                    $masterData = DB::table('mst_country')->get();
                } else if($type == 'state' && !empty($id)) {
                    $masterData = DB::table('mst_states')->where('country_id',$id)->get();
                } else if($type == 'title') {
                    $masterData = DB::table('mst_titles')->get();
                } else if($type == 'info_type') {
                    $masterData = DB::table('mst_info_type')->get();
                } else if($type == 'roles') {
                    $masterData = DB::table('mst_roles')->get();
                }
            } else {
                $message = config('constants.MESSAGE.INVALID_DATA');
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $masterData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

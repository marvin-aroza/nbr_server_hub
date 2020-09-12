<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;

class UserController extends Controller
{
    public function getFrontEndUserList() {
        try {
            $userslist = User::leftjoin('lk_user_role as r','users.id','r.user_id')
                        ->leftjoin('mst_roles as mr','r.role_id','mr.id')
                        ->leftjoin('user_details as us','users.id','us.user_id')
                        ->leftjoin('mst_country as c','us.country','c.id')
                        ->leftjoin('mst_states as s','us.state','s.id')
                        ->leftjoin('mst_titles as t','us.title','t.id')
                        ->leftjoin('mst_info_type as it','us.info_type','it.id')
                        ->select('users.*','us.last_name','us.dob','mr.id as role_id','mr.name as role_name','us.country as country_id','c.name as country','us.state as state_id','s.name as state',
                                'us.title as title_id','t.name as title','us.info_type as info_type_id','it.name as info_type','us.is_active','us.is_deleted')
                        ->where(['users.is_active'=>true,'users.is_deleted'=>0,'mr.id'=>5])//Only front end users
                        ->get()->toArray();
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $userslist, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function getFrontEndUserData($id) {
        try {
            $userslist = User::leftjoin('lk_user_role as r','users.id','r.user_id')
                        ->leftjoin('mst_roles as mr','r.role_id','mr.id')
                        ->leftjoin('user_details as us','users.id','us.user_id')
                        ->leftjoin('mst_country as c','us.country','c.id')
                        ->leftjoin('mst_states as s','us.state','s.id')
                        ->leftjoin('mst_titles as t','us.title','t.id')
                        ->leftjoin('mst_info_type as it','us.info_type','it.id')
                        ->select('users.*','us.last_name','us.dob','mr.id as role_id','mr.name as role_name','us.country as country_id','c.name as country','us.state as state_id','s.name as state',
                                'us.title as title_id','t.name as title','us.info_type as info_type_id','it.name as info_type','us.is_active','us.is_deleted')
                        ->where(['users.is_active'=>true,'users.is_deleted'=>0])//,'mr.id'=>5])//Only front end users
                        ->where('users.id',$id)
                        ->get()->toArray();
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $userslist, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function UserDelete($id) {
        try {
            $user_exists = User::where('id',$id)->first();
            if(!empty($user_exists)) {
                User::where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
                $message = config('constants.MESSAGE.USER_DELETED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.INVALID_USER');
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function UserInactive($id) {
        try {
            $user_exists = User::where('id',$id)->first();
            if(!empty($user_exists)) {
                User::where('id',$id)->update(['is_active'=>false]);
                $message = config('constants.MESSAGE.USER_INACTIVATED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.INVALID_USER');
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

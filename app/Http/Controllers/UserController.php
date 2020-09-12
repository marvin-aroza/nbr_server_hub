<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
use Validator;

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
                        ->leftjoin('application_get_in_touch as ap','users.id','ap.user_id')
                        ->select('users.*','us.last_name','us.dob','mr.id as role_id','mr.name as role_name','us.country as country_id','c.name as country','us.state as state_id','s.name as state',
                                'us.title as title_id','t.name as title','us.info_type as info_type_id','it.name as info_type','us.is_active','us.is_deleted')
                        ->where(['users.is_active'=>true,'users.is_deleted'=>0])//,'mr.id'=>5])//Only front end users
                        ->where('users.id',$id)
                        ->get()->toArray();
            $application_data = DB::table('application_get_in_touch')->where('user_id',$id)->first();
            if(!empty($userslist) && !empty($application_data)) {
                $userslist[0]['application_data'] = $application_data;
            } elseif(!empty($userslist)) {
                $userslist[0]['application_data'] = null;
            }
            $message = config('constants.MESSAGE.SUCCESS');
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
    
    public function addApplicationInTouchForm(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'=>'required',
                'first_name'=>'required',
                //'last_name'=>'required',
                'email'=>'required|email',
                //'phone'=>'required',
            ]);
            if($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $data = $request->all();
            $usermodel = new User();
            $resp = $usermodel->addApplicationForm($data);
            if($resp) {
                $message = config('constants.MESSAGE.APPLICATION_FORM_ADDED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.FAILED_APPLICATION_FORM_ADD');
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    
    public function applicationInTouchList() {
        try {
            $usermodel = new User();
            $formList = $usermodel->formList();
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $formList, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    
    public function applicationInTouchData($formid) {
        try {
            $usermodel = new User();
            $pageData = $usermodel->formData($formid);
            $message = config('constants.MESSAGE.SUCCESS');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $pageData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    
    public function applicationDataDelete($formid) {
        try {
            $usermodel = new User();
            $usermodel->formDataDelete($formid);
            $message = config('constants.MESSAGE.APPLICATION_FORM_DELETED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, null, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Validator;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\Hash;
use Zend\Diactoros\ServerRequest;
use \Laravel\Passport\Http\Controllers\AccessTokenController;

class AuthenticationController extends AccessTokenController
{
    public function registerFrontEndUser(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'firstname' => 'required',
                'lastname' => 'required',
                'dob' => 'required|date',
                'country' => 'required',
                'state' => 'required',
                'email' => 'required|email|unique:users|email',
                'password' => 'required',
                'info_type' => 'required',
            ]);
            if ($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            DB::beginTransaction();
            $input = $request->all();
            $userdata = array(
                'first_name' => $input['firstname'],
                'email' => $input['email'],
                'password' => Hash::make($input['password'])
            );
            $user = User::create($userdata);
            if(!empty($user)){
                $userInfo = array(
                    'user_id' => $user->id,
                    'title' => $input['title'],
                    'last_name' => $input['lastname'],
                    'dob' => $input['dob'],
                    'country' => $input['country'],
                    'state' => $input['state'],
                    'info_type' => $input['info_type'],
                );
                $success['id'] = $user->id;
                $success['token'] = $user->createToken('GW')->accessToken;
                $success['name'] = $user->first_name;
                $saveUserInfo = DB::table('user_details')->insert($userInfo);
                $roleid = DB::table('mst_roles')->where('name','User')->first();
                if(!empty($roleid->id)) {
                    $insertRole = DB::table('lk_user_role')->insert(['user_id'=>$user->id, 'role_id'=>$roleid->id]);
                }
                if($saveUserInfo && $insertRole) {
                    $success['role'] = $roleid->name;
                    DB::commit();
                    $message = config('constants.MESSAGE.USER_REGISTER_SUCCESS');
                    $code = config('constants.ERROR.CODE.OK'); // Ok
                    return jsonResponse(true, $success, $message, $code);
                } else {
                    DB::rollBack();
                    $message = config('constants.MESSAGE.USER_REGISTER_FAIL');
                    $code = config('constants.ERROR.CODE.BAD_REQUEST');
                    return jsonResponse(false, null, $message, $code);
                }
            } else {
                DB::rollBack();
                $message = config('constants.MESSAGE.USER_REGISTER_FAIL');
                $code = config('constants.ERROR.CODE.BAD_REQUEST');
                return jsonResponse(false, null, $message, $code);
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        } catch (QueryException $e) {
            $message = $e->getMessage();
            $code = 400;
            return jsonResponse(false, null, $message, $code);
        }
    }

    public function loginUserAdmin(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails())
            {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $user = User::where('email', $request->email)->first();
            if($user){
                if(Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('GW')->accessToken;
                    $role = DB::table('lk_user_role as lr')
                            ->join('mst_roles as mr','lr.role_id','mr.id')
                            ->select('mr.name as role_name','lr.role_id')
                            ->where('lr.user_id',$user->id)
                            ->first();
                    $data = array(
                        'id' => $user->id,
                        'token' => $token,
                        'name' => $user->first_name,
                        'role' => ($role) ? $role->role_name: ''
                    );
                    $message = config('constants.MESSAGE.LOGIN_SUCCESS');
                    $code = config('constants.ERROR.CODE.OK'); // Ok
                    return jsonResponse(true, $data, $message, $code);
                } else {
                    $message = config('constants.MESSAGE.INVALID_CREDENTIALS');
                    $code = config('constants.ERROR.CODE.UNAUTHORISED');
                    return jsonResponse(false, null, $message, $code);
                }
            } else {
                $message = config('constants.MESSAGE.USER_NOT_EXIST');
                $code = config('constants.ERROR.CODE.UNAUTHORISED');
                return jsonResponse(false, null, $message, $code);
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            $code = $e->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function registerAdmin(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users|email',
                'password' => 'required',
                'phone' => 'required',
                'role' => 'required'
            ]);
            if ($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            DB::beginTransaction();
            $input = $request->all();
            $userdata = array(
                'first_name' => $input['name'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
                'phone' => $input['phone']
            );
            $user = User::create($userdata);
            if(!empty($user)){
                $success['id'] = $user->id;
                $success['token'] = $user->createToken('GW')->accessToken;
                $success['name'] = $user->first_name;
                $roleid = DB::table('mst_roles')->where('name',$input['role'])->first();
                if(!empty($roleid->id)) {
                    $insertRole = DB::table('lk_user_role')->insert(['user_id'=>$user->id, 'role_id'=>$roleid->id]);
                }
                if($insertRole) {
                    $success['role'] = $roleid->name;
                    DB::commit();
                    $message = config('constants.MESSAGE.ADMIN_REGISTER_SUCCESS');
                    $code = config('constants.ERROR.CODE.OK'); // Ok
                    return jsonResponse(true, $success, $message, $code);
                } else {
                    DB::rollBack();
                    $message = config('constants.MESSAGE.USER_REGISTER_FAIL');
                    $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                    return jsonResponse(false, null, $message, $code);
                }
            } else {
                DB::rollBack();
                $message = config('constants.MESSAGE.USER_REGISTER_FAIL');
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

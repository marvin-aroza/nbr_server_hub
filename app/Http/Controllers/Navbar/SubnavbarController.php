<?php

namespace App\Http\Controllers\Navbar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Validator;
use App\Models\Navbar;

class SubnavbarController extends Controller
{
    public function addSubNavbar(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name'=>'required',
                'url'=>'required',
                'sort_order'=>'required',
                'navbar_id'=>'required'
            ]);
            if($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $data = $request->all();
            $navbar = new Navbar();
            $resp = $navbar->subnavbarAddOrUpdate($data);
            if($resp) {
                $message = config('constants.MESSAGE.SUBNAVBAR_ADDED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.FAILED_SUBNAVBAR_ADD');
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
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
    public function getSubNavbarList($id) {
        try {
            $navbar = new Navbar();
            $navList = $navbar->subnavbarList($id);
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $navList, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function getSubNavbarData($id) {
        try {
            $navbar = new Navbar();
            $navData = $navbar->subnavbarData($id);
            $message = config('constants.MESSAGE.SUCCESS');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $navData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function deleteSubNavbar($id) {
        try {
            $navbar = new Navbar();
            $navData = $navbar->subnavbarDelete($id);
            $message = config('constants.MESSAGE.NAVBAR_DELETED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, null, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

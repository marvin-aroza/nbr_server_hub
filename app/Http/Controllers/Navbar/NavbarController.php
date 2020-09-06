<?php

namespace App\Http\Controllers\Navbar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Validator;
use DB;
use App\Models\User;
use App\Models\Navbar;

class NavbarController extends Controller
{
    public function addNavbar(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name'=>'required',
                'url'=>'required',
                'sort_order'=>'required',
            ]);
            if($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $data = $request->all();
            $navbar = new Navbar();
            $resp = $navbar->navbarAddOrUpdate($data);
            if($resp) {
                $message = config('constants.MESSAGE.NAVBAR_ADDED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.FAILED_NAVBAR_ADD');
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
    public function getNavbarList() {
        try {
            $navbar = new Navbar();
            $navList = $navbar->navbarList();
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $navList, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function getNavbarData($id) {
        try {
            $navbar = new Navbar();
            $navData = $navbar->navbarData($id);
            $message = config('constants.MESSAGE.SUCCESS');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $navData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function deleteNavbar($id) {
        try {
            $navbar = new Navbar();
            $navData = $navbar->navbarDelete($id);
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

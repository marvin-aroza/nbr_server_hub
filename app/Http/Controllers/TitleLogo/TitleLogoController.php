<?php

namespace App\Http\Controllers\TitleLogo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TitleLogo;
use Validator;

class TitleLogoController extends Controller
{
    public function addTitleLogo(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                //'logo_image'=>'required',
                'logo_title'=>'required',
                'website_name'=>'required',
                'website_title'=>'required'
            ]);
            if($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $data = $request->all();
            $logotitle = new TitleLogo();
            $resp = $logotitle->titleLogAddOrUpdate($data);
            if($resp) {
                $message = config('constants.MESSAGE.TITLE_LOGO_ADDED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.FAILED_TITLE_LOGO_ADD');
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
    public function getTitleLogoList() {
        try {
            $logotitle = new TitleLogo();
            $logotitleList = $logotitle->titleLogoList();
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $logotitleList, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function getTitleLogoData($id) {
        try {
            $logotitle = new TitleLogo();
            $logotitleData = $logotitle->titleLogoData($id);
            $message = config('constants.MESSAGE.SUCCESS');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $logotitleData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function deleteTitleLogo($id) {
        try {
            $logotitle = new TitleLogo();
            $logotitle->titleLogoDelete($id);
            $message = config('constants.MESSAGE.TITLE_LOGO_DELETED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, null, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

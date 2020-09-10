<?php

namespace App\Http\Controllers\StaticPage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Validator;

class StaticPageController extends Controller
{
    public function addStaticPage(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'body'=>'required',
                'button_name'=>'required',
                'button_url'=>'required',
                'title'=>'required',
                //'title_image'=>'required',
                'title_description'=>'required',
            ]);
            if($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $data = $request->all();
            $staticPage = new StaticPage();
            $resp = $staticPage->staticPageAddOrUpdate($data);
            if($resp) {
                $message = config('constants.MESSAGE.STATICPAGE_ADDED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.FAILED_STATICPAGE_ADD');
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
    public function getStaticPageList() {
        try {
            $staticPage = new StaticPage();
            $staticList = $staticPage->staticPageList();
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $staticList, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function getStaticPageData($id) {
        try {
            $staticPage = new StaticPage();
            $pageData = $staticPage->staticPageData($id);
            $message = config('constants.MESSAGE.SUCCESS');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $pageData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function deleteStaticPage($id) {
        try {
            $staticPage = new StaticPage();
            $staticPage->staticPageDelete($id);
            $message = config('constants.MESSAGE.STATICPAGE_DELETED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, null, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

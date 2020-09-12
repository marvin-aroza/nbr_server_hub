<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Category;

class RecordsController extends Controller
{
    public function addRecords(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name'=>'required',
                'subcategory_id'=>'required',
            ]);
            if($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $data = $request->all();
            $category = new Category();
            $resp = $category->recordsAddOrUpdate($data);
            if($resp) {
                $message = config('constants.MESSAGE.RECORDS_ADDED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.FAILED_RECORDS_ADD');
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
    public function getRecordList($id) {
        try {
            $category = new Category();
            $recordList = $category->recordsList($id);
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $recordList, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function getRecordData($id) {
        try {
            $category = new Category();
            $recordData = $category->recordsData($id);
            $message = config('constants.MESSAGE.SUCCESS');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $recordData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function deleteRecord($id) {
        try {
            $category = new Category();
            $category->recordsDelete($id);
            $message = config('constants.MESSAGE.RECORDS_DELETED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, null, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

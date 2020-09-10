<?php

namespace App\Http\Controllers\News;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\News;
use Validator;

class NewsController extends Controller
{
    public function addNews(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'title'=>'required',
                'author_name'=>'required',
                'publish_date'=>'required',
                'subtitle'=>'required',
                //'cover_image'=>'required',
                //'body'=>'required',
            ]);
            if($validator->fails()) {
                $message = $validator->errors();
                $code = config('constants.ERROR.CODE.BAD_REQUEST'); // Ok
                return jsonResponse(false, null, $message, $code);
            }
            $data = $request->all();
            $news = new News();
            $resp = $news->newsAddOrUpdate($data);
            if($resp) {
                $message = config('constants.MESSAGE.NEWS_ADDED');
                $code = config('constants.ERROR.CODE.OK'); // Ok
                return jsonResponse(true, null, $message, $code);
            } else {
                $message = config('constants.MESSAGE.FAILED_NEWS_ADD');
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
    public function getNewsList() {
        try {
            $news = new News();
            $newsList = $news->newsList();
            $message = config('constants.MESSAGE.DATA_FETCHED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $newsList, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function getNewsData($id) {
        try {
            $news = new News();
            $newsData = $news->newsPageData($id);
            $message = config('constants.MESSAGE.SUCCESS');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, $newsData, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
    public function deleteNews($id) {
        try {
            $news = new News();
            $news->newsDelete($id);
            $message = config('constants.MESSAGE.NEWS_DELETED');
            $code = config('constants.ERROR.CODE.OK'); // Ok
            return jsonResponse(true, null, $message, $code);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

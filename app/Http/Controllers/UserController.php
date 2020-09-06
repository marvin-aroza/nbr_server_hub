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
                        ->select('users.*','mr.id as role_id','mr.name as role','c.name as country')
                        ->get()->toArray();
            echo '<pre>';
            print_r($userslist);
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $code = $ex->getCode();
            return jsonResponse(false, null, $message, $code);
        }
    }
}

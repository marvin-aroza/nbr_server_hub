<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Navbar extends Model
{
    public function navbarAddOrUpdate($data) {
        $insertData = array(
            'name' => $data['name'],
            'url' => $data['url'],
            'sort_order'=> $data['sort_order'],
            'is_child_present'=> $data['is_child_present'],
            'is_static'=>$data['is_static'],
            'in_footer'=>$data['in_footer']
        );
        if(!empty($data['id'])) {//update navbar
            return DB::table('navbar')->where('id',$data['id'])->update($insertData);
        } else {//insert navbar
            return DB::table('navbar')->insert($insertData);
        }
    }
    public function navbarList() {
        return DB::table('navbar')->where(['is_active'=>true, 'is_deleted'=>0])->get()->toArray();
    }
    public function navbarData($id) {
        return DB::table('navbar')->where('id',$id)->get()->toArray();
    }
    public function navbarDelete($id) {
        DB::table('navbar')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
        $is_subnav_exists = DB::table('subnavbar')->where('navbar_id',$id)->exists();
        if($is_subnav_exists) {
            DB::table('subnavbar')->where('navbar_id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
        }
        $is_static_exists = DB::table('static_pages')->where('navbar_id',$id)->exists();
        if($is_static_exists) {
            DB::table('static_pages')->where('navbar_id',$id)->update(['is_active'=>false, 'is_deleted'=>1]);
        }
    }
    public function subnavbarAddOrUpdate($data) {
        $insertData = array(
            'name' => $data['name'],
            'url' => $data['url'],
            'navbar_id' => $data['navbar_id'],
            'sort_order'=> $data['sort_order'],
            'is_static'=>$data['is_static'],
            'in_footer'=>$data['in_footer']
        );
        if(!empty($data['id'])) {//update navbar
            return DB::table('subnavbar')->where('id',$data['id'])->update($insertData);
        } else {//insert navbar
            return DB::table('subnavbar')->insert($insertData);
        }
    }
    public function subnavbarList($id) {
        return DB::table('subnavbar')->where(['navbar_id'=>$id,'is_active'=>true, 'is_deleted'=>0])->get()->toArray();
    }
    public function subnavbarData($id) {
        return DB::table('subnavbar')->where('id',$id)->get()->toArray();
    }
    public function subnavbarDelete($id) {
        DB::table('subnavbar')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
        $is_static_exists = DB::table('static_pages')->where('subnavbar_id',$id)->exists();
        if($is_static_exists) {
            DB::table('static_pages')->where('subnavbar_id',$id)->update(['is_active'=>false, 'is_deleted'=>1]);
        }
    }
}

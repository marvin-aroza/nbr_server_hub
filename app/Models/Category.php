<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Category extends Model
{
    public function categoryAddOrUpdate($data) {
        if(!empty($data['id'])) {//update navbar
            DB::table('categories')->where('id',$data['id'])->update($data);
        } else {//insert navbar
            $data['id'] = DB::table('categories')->insertGetId($data);
        }
        if(!empty($data['id'])) {
            DB::commit();
            return $data['id'];
        } else {
            DB::rollBack();
            return null;
        }
    }
    public function categoryList() {
        $categories = DB::table('categories')
                ->where(['is_active'=>true, 'is_deleted'=>0])->get()->toArray();
        if(empty($categories)) {
            $categories = [];
        }
        return $categories;
    }
    public function categoryData($id) {
        $categories = DB::table('categories')
                ->where(['id'=>$id,'is_active'=>true,'is_deleted'=>0])->first();
        if(empty($categories)) { 
            $categories = [];
        }
        return $categories;
    }
    public function categoryDelete($id) {
        DB::table('categories')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
        $is_subnav_exists = DB::table('subcategories')->where('category_id',$id)->exists();
        if($is_subnav_exists) {
            DB::table('subcategories')->where('category_id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
        }
//        $is_static_exists = DB::table('static_pages')->where('navbar_id',$id)->exists();
//        if($is_static_exists) {
//            DB::table('static_pages')->where('navbar_id',$id)->update(['is_active'=>false, 'is_deleted'=>1]);
//        }
    }
    
    public function subcategoryAddOrUpdate($data) {
        $insert_data = [
            'name' => $data['name'],
            'category_id'=>$data['category_id']
        ];
        if(!empty($data['id'])) {//update navbar
            DB::table('subcategories')->where('id',$data['id'])->update($insert_data);
        } else {//insert navbar
            $data['id'] = DB::table('subcategories')->insertGetId($insert_data);
        }
        if(!empty($data['id'])) {
            DB::commit();
            return $data['id'];
        } else {
            DB::rollBack();
            return null;
        }
    }
    public function subcategoryList($id) {
        $categories = DB::table('subcategories')
                ->where(['category_id'=>$id,'is_active'=>true, 'is_deleted'=>0])->get()->toArray();
        if(empty($categories)) {
            $categories = [];
        }
        return $categories;
    }
    public function subcategoryData($id) {
        $categories = DB::table('subcategories')
                ->where(['id'=>$id,'is_active'=>true,'is_deleted'=>0])->first();
        if(empty($categories)) { 
            $categories = [];
        }
        return $categories;
    }
    public function subcategoryDelete($id) {
        DB::table('subcategories')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
    }
}

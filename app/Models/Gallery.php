<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Gallery extends Model
{
    public function imageAddOrUpdate($data) {
        DB::beginTransaction();
        $insertData = array();
        if(!empty($data['image'])) {#upload title image
            $filename = 'gallery-image-' . time() . '.' . $data['image']->getClientOriginalExtension();
            $insertData['name'] = $data['image']->getClientOriginalName();
            $path = $data['image']->storeAs('public/Uploads/Gallery',$filename);
            $insertData['image_link'] = $filename;
        }
        if(!empty($data['id'])) {//update gallery image
            DB::table('gallery')->where('id',$data['id'])->update($insertData);
        } else {//insert navbar
            $data['id'] = DB::table('gallery')->insertGetId($insertData);
        }
        if(!empty($data['id'])) {
            DB::commit();
            return $data['id'];
        } else {
            DB::rollBack();
            return null;
        }
    }
    public function imageList() {
        $imageList = DB::table('gallery')
                ->where(['is_active'=>true, 'is_deleted'=>0])->get()->toArray();
        if(!empty($imageList)) {
            foreach($imageList as $k=>$v)
            {
                if(!empty($v->image_link)){
                    $v->image_link = asset('storage/Uploads/Gallery/'.$v->image_link);
                }
            }
        } else {
            $imageList = [];
        }
        return $imageList;
    }
    public function imageData($id) {
        $imageData = DB::table('gallery')
                ->where(['id'=>$id,'is_active'=>true,'is_deleted'=>0])->first();
        if($imageData) {
            if(!empty($imageData->image_link)) {
                $imageData->image_link = asset('storage/Uploads/Gallery/'.$imageData->image_link);
            }
        } else {
            $imageData=[];
        }
        return $imageData;
    }
    public function imageDelete($id) {
        DB::table('gallery')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
    }
    
    public function videoAddOrUpdate($data) {
        DB::beginTransaction();
        if(!empty($data['id'])) {//update gallery image
            DB::table('videos')->where('id',$data['id'])->update($data);
        } else {//insert navbar
            $data['id'] = DB::table('videos')->insertGetId($data);
        }
        if(!empty($data['id'])) {
            DB::commit();
            return $data['id'];
        } else {
            DB::rollBack();
            return null;
        }
    }
}

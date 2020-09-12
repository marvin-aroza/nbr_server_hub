<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class StaticPage extends Model
{
    public function staticPageAddOrUpdate($data) {
        DB::beginTransaction();
        $insertData = array(
            'navbar_id' => isset($data['navbar_id'])?$data['navbar_id']:NULL,
            'subnavbar_id' => isset($data['subnavbar_id'])?$data['subnavbar_id']:NULL,
            'body' => $data['body'],
            'button_name' => $data['button_name'],
            'button_url'=> $data['button_url'],
            'title'=> $data['title'],
            'title_description'=>$data['title_description']
        );
        if(!empty($data['id'])) {//update navbar
            DB::table('static_pages')->where('id',$data['id'])->update($insertData);
        } else {//insert navbar
            $data['id'] = DB::table('static_pages')->insertGetId($insertData);
        }
        /*if(!empty($data['title_image']) && !empty($data['id'])) {#upload title image
            $filename = 'title-image-' . time() . '.' . $data['title_image']->getClientOriginalExtension();
            $path = $data['title_image']->storeAs('public/Uploads/Title',$filename);
            $image_exists = DB::table('lk_static_title_images')->where('static_page_id',$data['id'])->first();
            if(!empty($image_exists)) { 
                DB::table('lk_static_title_images')->where('static_page_id',$data['id'])->update(['title_image'=>$filename]);
            } else {
                DB::table('lk_static_title_images')->insert(['static_page_id'=>$data['id'], 'title_image'=>$filename]);
            }
        }*/
        if(!empty($data['id'])) {
            DB::commit();
            return $data['id'];
        } else {
            DB::rollBack();
            return null;
        }
    }
    public function staticPageList() {
        $staticpages = DB::table('static_pages as st')
                ->leftjoin('lk_static_title_images as lk','st.id','lk.static_page_id')
                ->select('st.*','lk.title_image')
                ->where(['st.is_active'=>true, 'st.is_deleted'=>0])->get()->toArray();
        if(!empty($staticpages)) {
            foreach($staticpages as $k=>$v)
            {
                if(!empty($v->title_image)){
                    $v->title_image = asset('storage/Uploads/Title/'.$v->title_image);
                }
            }
        } else {
            $staticpages = [];
        }
        return $staticpages;
    }
    public function staticPageData($id) {
        $staticpage = DB::table('static_pages as st')
                ->leftjoin('lk_static_title_images as lk','st.id','lk.static_page_id')
                ->select('st.*','lk.title_image')
                ->where(['st.id'=>$id,'st.is_active'=>true,'st.is_deleted'=>0])->first();
        if(!empty($staticpage)) {
            $staticpage->title_image = asset('storage/Uploads/Title/'.$staticpage->title_image);
        } else {
            $staticpage = [];
        }
        return $staticpage;
    }
    public function staticPageDelete($id) {
        DB::table('static_pages')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
    }
}

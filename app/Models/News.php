<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class News extends Model
{
    public function newsAddOrUpdate($data) {
        DB::beginTransaction();
        $insertData = array(
            'title' => isset($data['title'])?$data['title']:NULL,
            'subtitle' => isset($data['subtitle'])?$data['subtitle']:NULL,
            'author_name' => $data['author_name'],
            'publish_date' => $data['publish_date'],
            'body'=> $data['body']
        );
        if(!empty($data['id'])) {//update navbar
            DB::table('news')->where('id',$data['id'])->update($insertData);
        } else {//insert news
            $data['id'] = DB::table('news')->insertGetId($insertData);
        }
        if(!empty($data['cover_image']) && !empty($data['id'])) {#upload title image
            $filename = 'cover-image-' . time() . '.' . $data['cover_image']->getClientOriginalExtension();
            $path = $data['cover_image']->storeAs('public/Uploads/News',$filename);
            $image_exists = DB::table('lk_news_images')->where('news_id',$data['id'])->first();
            if(!empty($image_exists)) { 
                DB::table('lk_news_images')->where('news_id',$data['id'])->update(['cover_image'=>$filename]);
            } else {
                DB::table('lk_news_images')->insert(['news_id'=>$data['id'], 'cover_image'=>$filename]);
            }
        }
        if(!empty($data['id'])) {
            DB::commit();
            return $data['id'];
        } else {
            DB::rollBack();
            return null;
        }
    }
    public function newsList() {
        $staticpages = DB::table('news as st')
                ->leftjoin('lk_news_images as lk','st.id','lk.news_id')
                ->select('st.*','lk.cover_image')
                ->where(['st.is_active'=>true, 'st.is_deleted'=>0])->get()->toArray();
        if(!empty($staticpages)) {
            foreach($staticpages as $k=>$v)
            {
                if(!empty($v->cover_image)){
                    $v->cover_image = url('public/Uploads/News/'.$v->cover_image);
                }
            }
        } else {
            $staticpages = [];
        }
        return $staticpages;
    }
    public function newsPageData($id) {
        $staticpage = DB::table('news as st')
                ->leftjoin('lk_news_images as lk','st.id','lk.news_id')
                ->select('st.*','lk.cover_image')
                ->where(['st.id'=>$id,'st.is_active'=>true,'st.is_deleted'=>0])->first();
        if($staticpage) {
            $staticpage->cover_image = url('public/Uploads/Title/'.$staticpage->cover_image);
        }
        return $staticpage;
    }
    public function newsDelete($id) {
        DB::table('news')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
    }
}

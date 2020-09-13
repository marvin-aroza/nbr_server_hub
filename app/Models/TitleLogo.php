<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TitleLogo extends Model
{
    public function titleLogAddOrUpdate($data) {
        DB::beginTransaction();
        $insertData = array(
            'logo_image' => isset($data['logo_image'])?$data['logo_image']:NULL,
            'logo_title' => isset($data['logo_title'])?$data['logo_title']:NULL,
            'website_name' => $data['website_name'],
            'website_title' => $data['website_title']
        );
        if(!empty($data['logo_image'])) {#upload title image
            $filename = 'logo-image-' . time() . '.' . $data['logo_image']->getClientOriginalExtension();
            $path = $data['logo_image']->storeAs('public/Uploads/TitleLogo',$filename);
            $insertData['logo_image'] = $filename;
        }
        if(!empty($data['id'])) {//update navbar
            if(empty($insertData['logo_image'])) {
                unset($insertData['logo_image']);
            }
            DB::table('title_logo')->where('id',$data['id'])->update($insertData);
        } else {//insert navbar
            $data['id'] = DB::table('title_logo')->insertGetId($insertData);
        }
        
        if(!empty($data['id'])) {
            DB::commit();
            return $data['id'];
        } else {
            DB::rollBack();
            return null;
        }
    }
    public function titleLogoList() {
        $titlelogo = DB::table('title_logo as tl')
                ->where(['tl.is_active'=>true, 'tl.is_deleted'=>0])->get()->toArray();
        if(!empty($titlelogo)) {
            foreach($titlelogo as $k=>$v)
            {
                if(!empty($v->logo_image)){
                    $v->logo_image = asset('storage/Uploads/TitleLogo/'.$v->logo_image);
                }
            }
        } else {
            $titlelogo = [];
        }
        return $titlelogo;
    }
    public function titleLogoData($id) {
        $titlelogo = DB::table('title_logo as st')
                ->where(['st.id'=>$id,'st.is_active'=>true,'st.is_deleted'=>0])->first();
        if($titlelogo) {
            if(!empty($titlelogo->logo_image)){
                $titlelogo->logo_image = asset('storage/Uploads/TitleLogo/'.$titlelogo->logo_image);
            }
        } else {
            $titlelogo=[];
        }
        return $titlelogo;
    }
    public function titleLogoDelete($id) {
        DB::table('title_logo')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
    }
}

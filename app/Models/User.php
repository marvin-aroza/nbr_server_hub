<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use DB;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'email', 'password','phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function addApplicationForm($data) {
        if(!empty($data)) {
            return DB::table('application_get_in_touch')->insert($data);
        }
    }
    
    public function formList() {
        return DB::table('application_get_in_touch')->select('id','user_id','first_name','last_name','record_in_mind','idea_of_event','job_title')
                ->where(['is_active'=>true,'is_deleted'=>0])->get()->toArray();
    }
    
    public function formData($id) {
        return DB::table('application_get_in_touch')
                ->where(['id'=>$id,'is_active'=>true,'is_deleted'=>0])->get()->toArray();
    }
    
    public function formDataDelete($id) {
        return DB::table('application_get_in_touch')->where('id',$id)->update(['is_active'=>false,'is_deleted'=>1]);
    }
}

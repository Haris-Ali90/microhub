<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewUser extends Model
{

    public $table = 'dashboard_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'full_name', 'email', 'password', 'social_media_platform','gender','dob','role_id','status','phone','area','latitude','longitude','social_media_id','profile_picture', 'device_token','device_type','push_status','newsfeed_status','message_status','avaibility_status','prefix','year_of_experience','background','profession','token','promo_video','statistics'
    ];


}
